<!doctype html>
<html lang="en">

<head>
    <title>Lupa Password | CepatDapat</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        id="main-font-link" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />
</head>

<body>


    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="card my-5">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-end mb-4">
                            <h3 class="mb-0"><b>Lupa Password</b></h3>
                            <a href="/login" class="link-primary">Kembali ke Login</a>
                        </div>

                        <p class="text-muted mb-4">Pilih metode untuk menerima link reset password.</p>

                        {{-- Status Message --}}
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ti ti-check me-2"></i>{{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Error Messages --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $error)
                                    <div><i class="ti ti-alert-circle me-2"></i>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="/forgot-password" id="forgotForm">
                            @csrf

                            {{-- Method Selection --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Kirim link via:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="method" value="email"
                                            id="methodEmail" {{ old('method', 'email') === 'email' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="methodEmail">
                                            <i class="ti ti-mail me-1"></i>Email
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="method" value="telepon"
                                            id="methodTelepon" {{ old('method') === 'telepon' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="methodTelepon">
                                            <i class="ti ti-brand-whatsapp me-1"></i>WhatsApp
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Email Input --}}
                            <div class="mb-3" id="emailGroup">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="contoh@email.com" value="{{ old('email') }}">
                            </div>

                            {{-- Phone Input --}}
                            <div class="mb-3" id="teleponGroup" style="display: none;">
                                <label for="telepon" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon"
                                    placeholder="08xxxxxxxxxx" value="{{ old('telepon') }}">
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    <i class="ti ti-send me-2"></i>Kirim Link Reset Password
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>

    <script>
        // Toggle email / telepon input based on radio selection
        const methodRadios = document.querySelectorAll('input[name="method"]');
        const emailGroup = document.getElementById('emailGroup');
        const teleponGroup = document.getElementById('teleponGroup');

        function toggleMethod() {
            const selected = document.querySelector('input[name="method"]:checked').value;
            if (selected === 'email') {
                emailGroup.style.display = '';
                teleponGroup.style.display = 'none';
                document.getElementById('telepon').value = '';
            } else {
                emailGroup.style.display = 'none';
                teleponGroup.style.display = '';
                document.getElementById('email').value = '';
            }
        }

        methodRadios.forEach(r => r.addEventListener('change', toggleMethod));
        // Initialize on page load
        toggleMethod();
    </script>
</body>

</html>