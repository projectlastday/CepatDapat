<!doctype html>
<html lang="en">

<head>
    <title>404 - Halaman Tidak Ditemukan | CepatDapat</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="icon" href="<?php echo e(asset('assets/images/favicon.svg')); ?>" type="image/x-icon" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/tabler-icons.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>" id="main-style-link" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-preset.css')); ?>" />
    <style>
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
        }

        .error-icon {
            font-size: 3rem;
            color: #a855f7;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .error-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.1);
        }
    </style>
</head>

<body>
    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="card error-card my-5">
                    <div class="card-body text-center py-5 px-4">

                        
                        <div class="error-icon mb-3">
                            <i class="ti ti-mood-sad"></i>
                        </div>

                        
                        <h1 class="error-code mb-2">404</h1>

                        
                        <h4 class="fw-bold text-dark mb-2">Halaman Tidak Ditemukan</h4>
                        <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">
                            Halaman yang Anda cari mungkin telah dihapus, berganti nama,
                            atau tidak tersedia untuk sementara waktu.
                        </p>

                        
                        <a href="<?php echo e(url('/')); ?>" class="btn btn-primary shadow-sm px-4 py-2">
                            <i class="ti ti-arrow-left me-1"></i> Kembali ke Catalog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('assets/js/plugins/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/simplebar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/script.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/theme.js')); ?>"></script>

    <script>layout_change('light');</script>
    <script>font_change('Roboto');</script>
    <script>preset_change('preset-1');</script>
</body>

</html>
<?php /**PATH C:\Users\jason\Documents\Brian\CepatDapat_new\resources\views/errors/404.blade.php ENDPATH**/ ?>