<?php

namespace App\Http\Controllers;

use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Generate math captcha as fallback
        $num1 = rand(1, 20);
        $num2 = rand(1, 20);
        session()->put('math_captcha', $num1 + $num2);
        $mathQuestion = "{$num1} + {$num2} = ?";

        return view('auth.login', [
            'mathQuestion' => $mathQuestion,
            'recaptchaSiteKey' => env('RECAPTCHA_SITE_KEY'),
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // --- Captcha Validation ---
        $captchaValid = false;

        // 1. Try Google reCAPTCHA first
        $recaptchaResponse = $request->input('g-recaptcha-response');
        if ($recaptchaResponse) {
            try {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->ip(),
                ]);

                $result = $response->json();
                if (isset($result['success']) && $result['success'] === true) {
                    $captchaValid = true;
                }
            } catch (\Exception $e) {
                Log::error('reCAPTCHA verification failed: ' . $e->getMessage());
            }
        }

        // 2. Fallback to math captcha
        if (!$captchaValid) {
            $mathAnswer = $request->input('math_answer');
            $expectedAnswer = session('math_captcha');

            if ($mathAnswer !== null && $expectedAnswer !== null && (int) $mathAnswer === (int) $expectedAnswer) {
                $captchaValid = true;
            }
        }

        if (!$captchaValid) {
            return back()->withInput()->withErrors(['captcha' => 'Verifikasi captcha gagal. Silakan coba lagi.']);
        }

        // Clear math captcha from session after validation
        session()->forget('math_captcha');

        // Cek kecocokan Plain Text langsung di database
        $user = DB::table('users')
            ->where('username', $request->username)
            ->where('password', $request->password)
            ->first();

        if ($user) {
            // Gunakan session manual, jangan gunakan Auth::login
            $request->session()->put('id_user', $user->id_user);
            $request->session()->put('username', $user->username);
            $request->session()->put('id_user_type', $user->id_user_type);

            // Record login history
            try {
                DB::table('history_login')->insert([
                    'id_user' => $user->id_user,
                    'ip_address' => $request->ip(),
                    'user_agent' => \App\Helpers\DeviceInfoHelper::getDeviceInfo($request),
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to record login history: ' . $e->getMessage());
            }

            return redirect('/');
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    /**
     * Send OTP for a given channel (email or telepon).
     * POST /register/send-otp
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:email,telepon',
            'email' => 'required_if:channel,email|nullable|email',
            'telepon' => 'required_if:channel,telepon|nullable|string',
        ]);

        $channel = $request->input('channel');
        $email = $request->filled('email') ? strtolower(trim($request->input('email'))) : null;
        $telepon = $request->filled('telepon') ? preg_replace('/[^0-9+]/', '', trim($request->input('telepon'))) : null;

        // --- Check if email/telepon is already registered ---
        if ($channel === 'email' && $email) {
            $exists = DB::table('users')->where('email', $email)->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terdaftar.',
                ], 422);
            }
        }

        if ($channel === 'telepon' && $telepon) {
            $exists = DB::table('users')->where('telepon', $telepon)->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor telepon sudah terdaftar.',
                ], 422);
            }
        }

        // --- Find or create OTP record ---
        // Search by either email OR telepon to unify both channels into one row,
        // even when the user fills the form incrementally (email first, telepon later).
        $otpRecord = OtpVerification::where(function ($q) use ($email, $telepon) {
                if ($email) {
                    $q->orWhere('email', $email);
                }
                if ($telepon) {
                    $q->orWhere('telepon', $telepon);
                }
            })
            ->whereNull('used_at')
            ->latest()
            ->first();

        // --- Cooldown check (server-side 120s) ---
        $lastSentCol = $channel . '_last_sent_at';
        if ($otpRecord && $otpRecord->$lastSentCol) {
            $secondsSince = now()->diffInSeconds($otpRecord->$lastSentCol, false);
            // diffInSeconds returns positive if $lastSentCol is in the future relative to now,
            // negative if in the past. We want: if less than 120 seconds have passed, block.
            $elapsed = abs(now()->diffInSeconds($otpRecord->$lastSentCol));
            if ($elapsed < 120) {
                $remaining = 120 - $elapsed;
                return response()->json([
                    'success' => false,
                    'message' => "Tunggu {$remaining} detik sebelum mengirim ulang kode.",
                    'cooldown_remaining' => $remaining,
                ], 429);
            }
        }

        // --- Generate OTP ---
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash = Hash::make($otp);

        // --- Store OTP ---
        $updateData = [
            $channel . '_otp_hash' => $otpHash,
            $channel . '_expires_at' => now()->addMinutes(10),
            $channel . '_last_sent_at' => now(),
            $channel . '_attempts' => 0, // reset attempts on new OTP
            $channel . '_verified_at' => null, // reset verification on new OTP
        ];

        // Always sync email/telepon fields so both end up on the same record
        if ($email) {
            $updateData['email'] = $email;
        }
        if ($telepon) {
            $updateData['telepon'] = $telepon;
        }

        if ($otpRecord) {
            $otpRecord->update($updateData);
        } else {
            // Create new record
            $otpRecord = OtpVerification::create(array_merge([
                'email' => $email,
                'telepon' => $telepon,
            ], $updateData));
        }

        // --- Log OTP in dev/local only (NEVER in production) ---
        if (app()->environment('local', 'development')) {
            Log::info("OTP [{$channel}] for " . ($channel === 'email' ? $email : $telepon) . ": {$otp}");
        }

        // --- In production, send via SMS/Email service here ---
        // Mail::to($email)->send(new OtpMail($otp));
        // SmsService::send($telepon, "Kode verifikasi Anda: {$otp}");

        if ($channel === 'email' && $email) {
            try {
                Mail::to($email)->send(new OtpMail($otp));
            } catch (\Exception $e) {
                Log::error("Email OTP failed: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim email OTP. Pastikan konfigurasi SMTP benar.',
                    'otp_debug' => app()->environment('local', 'development') ? $otp : null,
                ], 500);
            }
        }

        if ($channel === 'telepon' && $telepon) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Zt24bUTJJvbYmRjBTmMt',
                ])->post('https://api.fonnte.com/send', [
                            'target' => $telepon,
                            'message' => "*Kode Verifikasi CepatDapat*\n\nKode Anda adalah: *$otp*\n\nJangan berikan kode ini kepada siapapun!",
                            'countryCode' => '62',
                        ]);

                if (!$response->successful()) {
                    Log::error("Fonnte API failed: " . $response->body());
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengirim WhatsApp OTP. Silakan coba lagi nanti.',
                        'otp_debug' => app()->environment('local', 'development') ? $otp : null,
                    ], 500);
                }
            } catch (\Exception $e) {
                Log::error("Fonnte API Exception: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal terhubung ke layanan WhatsApp.',
                    'otp_debug' => app()->environment('local', 'development') ? $otp : null,
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode verifikasi telah dikirim.',
            // Only include OTP in dev for testing convenience
            'otp_debug' => app()->environment('local', 'development') ? $otp : null,
        ]);
    }

    /**
     * Register with dual-OTP verification.
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'telepon' => 'required|string|unique:users,telepon',
            'password' => 'required|min:8',
            'email_code_verifikasi' => 'required|string|size:6',
            'telepon_code_verifikasi' => 'required|string|size:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.unique' => 'Nomor telepon sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'email_code_verifikasi.required' => 'Kode verifikasi email wajib diisi.',
            'email_code_verifikasi.size' => 'Kode verifikasi email harus 6 digit.',
            'telepon_code_verifikasi.required' => 'Kode verifikasi telepon wajib diisi.',
            'telepon_code_verifikasi.size' => 'Kode verifikasi telepon harus 6 digit.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $telepon = preg_replace('/[^0-9+]/', '', trim($request->input('telepon')));

        // --- Find OTP record ---
        $otpRecord = OtpVerification::where('email', $email)
            ->where('telepon', $telepon)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return back()->withInput()->withErrors([
                'email_code_verifikasi' => 'Kode verifikasi tidak ditemukan. Silakan kirim ulang kode.',
            ]);
        }

        // --- Check if already used ---
        if ($otpRecord->used_at) {
            return back()->withInput()->withErrors([
                'email_code_verifikasi' => 'Kode verifikasi sudah digunakan. Silakan kirim ulang kode.',
            ]);
        }

        // --- Check email OTP ---
        $maxAttempts = 5;

        // Email attempts check
        if ($otpRecord->email_attempts >= $maxAttempts) {
            return back()->withInput()->withErrors([
                'email_code_verifikasi' => 'Terlalu banyak percobaan. Silakan kirim ulang kode email.',
            ]);
        }

        // Telepon attempts check
        if ($otpRecord->telepon_attempts >= $maxAttempts) {
            return back()->withInput()->withErrors([
                'telepon_code_verifikasi' => 'Terlalu banyak percobaan. Silakan kirim ulang kode telepon.',
            ]);
        }

        // --- Verify Email OTP ---
        if (!$otpRecord->email_otp_hash || !Hash::check($request->email_code_verifikasi, $otpRecord->email_otp_hash)) {
            $otpRecord->increment('email_attempts');
            return back()->withInput()->withErrors([
                'email_code_verifikasi' => 'Kode verifikasi email salah.',
            ]);
        }

        if ($otpRecord->email_expires_at && $otpRecord->email_expires_at->isPast()) {
            return back()->withInput()->withErrors([
                'email_code_verifikasi' => 'Kode verifikasi email sudah kedaluwarsa. Silakan kirim ulang.',
            ]);
        }

        // --- Verify Telepon OTP ---
        if (!$otpRecord->telepon_otp_hash || !Hash::check($request->telepon_code_verifikasi, $otpRecord->telepon_otp_hash)) {
            $otpRecord->increment('telepon_attempts');
            return back()->withInput()->withErrors([
                'telepon_code_verifikasi' => 'Kode verifikasi telepon salah.',
            ]);
        }

        if ($otpRecord->telepon_expires_at && $otpRecord->telepon_expires_at->isPast()) {
            return back()->withInput()->withErrors([
                'telepon_code_verifikasi' => 'Kode verifikasi telepon sudah kedaluwarsa. Silakan kirim ulang.',
            ]);
        }

        // --- All OTPs valid — create user in a transaction ---
        DB::transaction(function () use ($request, $email, $telepon, $otpRecord) {
            DB::table('users')->insert([
                'username' => $request->username,
                'email' => $email,
                'telepon' => $telepon,
                'password' => $request->password, // Simpan Plain Text (project convention)
                'id_user_type' => 3,
                'email_verified_at' => now(),
                'telepon_verified_at' => now(),
                'created_at' => now(),
            ]);

            $otpRecord->update([
                'used_at' => now(),
                'email_verified_at' => now(),
                'telepon_verified_at' => now(),
            ]);
        });

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush(); // Hapus semua session manual

        return redirect('/login');
    }
}
