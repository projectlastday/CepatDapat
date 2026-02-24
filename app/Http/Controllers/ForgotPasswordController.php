<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form with Email/Phone options.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Validate input and send the reset link via Email or WhatsApp.
     */
    public function sendResetLink(Request $request)
    {
        $method = $request->input('method'); // 'email' or 'telepon'

        if ($method === 'email') {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = DB::table('users')->where('email', $request->email)->first();
            if (!$user) {
                return back()->withInput()->withErrors(['email' => 'Email tidak ditemukan.']);
            }

            // Generate token
            $token = Str::random(64);

            // Delete existing tokens for this email
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Store token
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            // Build reset URL
            $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

            // Send email
            try {
                Mail::send('emails.reset_password', ['resetUrl' => $resetUrl, 'user' => $user], function ($message) use ($request) {
                    $message->to($request->email);
                    $message->subject('Reset Password - CepatDapat');
                });

                return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
            } catch (\Exception $e) {
                Log::error('Reset password email failed: ' . $e->getMessage());
                return back()->withInput()->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi.']);
            }

        } elseif ($method === 'telepon') {
            $request->validate([
                'telepon' => 'required',
            ]);

            // Normalize phone number
            $telepon = preg_replace('/[^0-9]/', '', $request->telepon);
            if (str_starts_with($telepon, '0')) {
                $telepon = '62' . substr($telepon, 1);
            }

            $user = DB::table('users')->where('telepon', $telepon)->first();
            if (!$user) {
                return back()->withInput()->withErrors(['telepon' => 'Nomor telepon tidak ditemukan.']);
            }

            // Generate token
            $token = Str::random(64);

            // Delete existing tokens for this phone
            DB::table('password_reset_tokens')->where('telepon', $telepon)->delete();

            // Store token
            DB::table('password_reset_tokens')->insert([
                'telepon' => $telepon,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            // Build reset URL
            $resetUrl = url('/reset-password/' . $token . '?telepon=' . urlencode($telepon));

            // Send via Fonnte (WhatsApp)
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Zt24bUTJJvbYmRjBTmMt',
                ])->post('https://api.fonnte.com/send', [
                            'target' => $telepon,
                            'message' => "Reset Password CepatDapat\n\nKlik link berikut untuk mereset password Anda:\n{$resetUrl}\n\nLink berlaku selama 60 menit.\nJika Anda tidak meminta reset password, abaikan pesan ini.",
                        ]);

                Log::info('Fonnte reset password response', ['body' => $response->body()]);

                return back()->with('status', 'Link reset password telah dikirim ke WhatsApp Anda.');
            } catch (\Exception $e) {
                Log::error('Reset password WhatsApp failed: ' . $e->getMessage());
                return back()->withInput()->withErrors(['telepon' => 'Gagal mengirim pesan. Silakan coba lagi.']);
            }
        }

        return back()->withErrors(['method' => 'Pilih metode pengiriman.']);
    }

    /**
     * Show the reset password form.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        $telepon = $request->query('telepon');

        // Verify token exists
        $query = DB::table('password_reset_tokens');
        if ($email) {
            $query->where('email', $email);
        } elseif ($telepon) {
            $query->where('telepon', $telepon);
        } else {
            return redirect('/login')->withErrors(['token' => 'Link tidak valid.']);
        }

        $record = $query->first();

        if (!$record || !Hash::check($token, $record->token)) {
            return redirect('/login')->withErrors(['token' => 'Link reset password tidak valid atau sudah kadaluarsa.']);
        }

        // Check token expiry (60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('token', $record->token)->delete();
            return redirect('/login')->withErrors(['token' => 'Link reset password sudah kadaluarsa.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
            'telepon' => $telepon,
        ]);
    }

    /**
     * Reset the password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:4|confirmed',
        ]);

        $email = $request->input('email');
        $telepon = $request->input('telepon');

        // Find the token record
        $query = DB::table('password_reset_tokens');
        if ($email) {
            $query->where('email', $email);
        } elseif ($telepon) {
            $query->where('telepon', $telepon);
        } else {
            return back()->withErrors(['token' => 'Data tidak valid.']);
        }

        $record = $query->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Token tidak valid.']);
        }

        // Check token expiry
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('token', $record->token)->delete();
            return back()->withErrors(['token' => 'Link sudah kadaluarsa. Silakan minta link baru.']);
        }

        // Update user password (Plain Text per project convention)
        $updateQuery = DB::table('users');
        if ($email) {
            $updateQuery->where('email', $email);
        } else {
            $updateQuery->where('telepon', $telepon);
        }

        $updateQuery->update([
            'password' => $request->password,
        ]);

        // Delete the used token
        if ($email) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
        } else {
            DB::table('password_reset_tokens')->where('telepon', $telepon)->delete();
        }

        return redirect('/login')->with('status', 'Password berhasil direset! Silakan login dengan password baru.');
    }
}
