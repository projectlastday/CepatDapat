<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign Up | CepatDapat</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        id="main-font-link">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
    <style>
        .otp-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .otp-row .form-floating {
            flex: 1;
        }

        .otp-row .btn {
            min-width: 120px;
            height: 58px;
            white-space: nowrap;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 5;
            background: none;
            border: none;
            padding: 4px;
            color: #6c757d;
        }

        .password-toggle:hover {
            color: #333;
        }

        .otp-status {
            font-size: 0.8rem;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="card mt-5">
                    <div class="card-body">
                        <a href="#" class="d-flex justify-content-center mt-3">
                            <img src="{{ asset($site_settings['logo'] ?? 'assets/images/CepatDapat.png') }}" alt="image"
                                class="img-fluid" style="width: 70%;">
                        </a>
                        <div class="row">
                            <div class="d-flex justify-content-center">
                                <div class="auth-header">
                                    <h2 class="text-secondary mt-5"><b>Sign up</b></h2>
                                    <p class="f-16 mt-2">Masukkan data untuk mendaftar</p>
                                </div>
                            </div>
                        </div>

                        {{-- Global Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('register.post') }}" method="POST" id="registerForm">
                            @csrf

                            {{-- Username --}}
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Username"
                                    value="{{ old('username') }}" required>
                                <label>Username</label>
                                @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="form-floating mb-3">
                                <input type="email" name="email" id="emailInput" class="form-control"
                                    placeholder="Email Address" value="{{ old('email') }}" required>
                                <label>Email Address</label>
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Email OTP --}}
                            <div id="emailOtpStatus" class="otp-status mb-2"></div>
                            @error('email_code_verifikasi') <small
                            class="text-danger d-block mb-2">{{ $message }}</small> @enderror
                            <div class="otp-row mb-3">
                                <div class="form-floating">
                                    <input type="text" name="email_code_verifikasi" id="emailOtpInput"
                                        class="form-control" placeholder="Kode Verifikasi Email" maxlength="6"
                                        inputmode="numeric" pattern="[0-9]{6}"
                                        value="{{ old('email_code_verifikasi') }}">
                                    <label>Kode Verifikasi Email</label>
                                </div>
                                <button type="button" class="btn btn-outline-secondary" id="btnSendEmailOtp"
                                    onclick="sendOtp('email')">
                                    Kirim Kode
                                </button>
                            </div>

                            {{-- Telepon --}}
                            <div class="form-floating mb-3">
                                <input type="tel" name="telepon" id="teleponInput" class="form-control"
                                    placeholder="Nomor Telepon" value="{{ old('telepon') }}" required>
                                <label>Nomor Telepon</label>
                                @error('telepon') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Telepon OTP --}}
                            <div id="teleponOtpStatus" class="otp-status mb-2"></div>
                            @error('telepon_code_verifikasi') <small
                            class="text-danger d-block mb-2">{{ $message }}</small> @enderror
                            <div class="otp-row mb-3">
                                <div class="form-floating">
                                    <input type="text" name="telepon_code_verifikasi" id="teleponOtpInput"
                                        class="form-control" placeholder="Kode Verifikasi Telepon" maxlength="6"
                                        inputmode="numeric" pattern="[0-9]{6}"
                                        value="{{ old('telepon_code_verifikasi') }}">
                                    <label>Kode Verifikasi Telepon</label>
                                </div>
                                <button type="button" class="btn btn-outline-secondary" id="btnSendTeleponOtp"
                                    onclick="sendOtp('telepon')">
                                    Kirim Kode
                                </button>
                            </div>

                            {{-- Password --}}
                            <div class="form-floating mb-3 password-wrapper">
                                <input type="password" name="password" id="passwordInput" class="form-control"
                                    placeholder="Password" required>
                                <label>Password</label>
                                <button type="button" class="password-toggle" onclick="togglePassword()" tabindex="-1">
                                    <i class="ti ti-eye" id="passwordToggleIcon"></i>
                                </button>
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-secondary p-2">Sign Up</button>
                            </div>
                        </form>

                        <hr>
                        <h5 class="d-flex justify-content-center">
                            Already have an account? &nbsp; <a href="{{ route('login') }}">Login</a>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/icon/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

    <script>
        // CSRF token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Countdown timers
        const timers = { email: null, telepon: null };
        const countdowns = { email: 0, telepon: 0 };

        /**
         * Send OTP for a given channel
         */
        function sendOtp(channel) {
            const btn = document.getElementById(channel === 'email' ? 'btnSendEmailOtp' : 'btnSendTeleponOtp');
            const statusEl = document.getElementById(channel === 'email' ? 'emailOtpStatus' : 'teleponOtpStatus');
            const emailVal = document.getElementById('emailInput').value.trim();
            const teleponVal = document.getElementById('teleponInput').value.trim();

            // Basic client-side validation
            // Clear any previous status (prevent stacking green + red)
            statusEl.innerHTML = '';

            if (channel === 'email' && !emailVal) {
                statusEl.innerHTML = '<span class="text-danger">Masukkan email terlebih dahulu.</span>';
                return;
            }
            if (channel === 'telepon' && !teleponVal) {
                statusEl.innerHTML = '<span class="text-danger">Masukkan nomor telepon terlebih dahulu.</span>';
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Mengirim...';

            fetch('{{ route("register.send-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    channel: channel,
                    email: emailVal,
                    telepon: teleponVal,
                }),
            })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        statusEl.innerHTML = '<span class="text-success">Kode verifikasi telah dikirim!</span>';
                        startCountdown(channel, 120);
                    } else {
                        statusEl.innerHTML = '<span class="text-danger">' + (data.message || 'Gagal mengirim kode.') + '</span>';
                        // If cooldown remaining, start timer with that value
                        if (data.cooldown_remaining) {
                            startCountdown(channel, data.cooldown_remaining);
                        } else {
                            btn.disabled = false;
                            btn.textContent = 'Kirim Kode';
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    statusEl.innerHTML = '<span class="text-danger">Terjadi kesalahan. Coba lagi.</span>';
                    btn.disabled = false;
                    btn.textContent = 'Kirim Kode';
                });
        }

        /**
         * Start a countdown timer for a channel button
         */
        function startCountdown(channel, seconds) {
            const btn = document.getElementById(channel === 'email' ? 'btnSendEmailOtp' : 'btnSendTeleponOtp');

            // Clear any existing timer
            if (timers[channel]) {
                clearInterval(timers[channel]);
            }

            countdowns[channel] = seconds;
            btn.disabled = true;
            btn.textContent = formatTime(countdowns[channel]);

            timers[channel] = setInterval(() => {
                countdowns[channel]--;
                if (countdowns[channel] <= 0) {
                    clearInterval(timers[channel]);
                    timers[channel] = null;
                    btn.disabled = false;
                    btn.textContent = 'Kirim Kode';
                } else {
                    btn.textContent = formatTime(countdowns[channel]);
                }
            }, 1000);
        }

        function formatTime(totalSeconds) {
            const m = Math.floor(totalSeconds / 60);
            const s = totalSeconds % 60;
            return m + ':' + (s < 10 ? '0' : '') + s;
        }

        /**
         * Toggle password visibility
         */
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('passwordToggleIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ti ti-eye-off';
            } else {
                input.type = 'password';
                icon.className = 'ti ti-eye';
            }
        }
    </script>
</body>

</html>