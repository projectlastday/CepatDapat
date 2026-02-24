<!doctype html>
<html lang="en">

<head>
    <title>Login | CepatDapat</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="icon" href="<?php echo e(asset('assets/images/favicon.svg')); ?>" type="image/x-icon" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        id="main-font-link" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/phosphor/duotone/style.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/tabler-icons.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/feather.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/fontawesome.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/material.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>" id="main-style-link" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-preset.css')); ?>" />
    <style>
        .math-captcha-container {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
        }

        .math-captcha-container .math-question {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="card my-5">
                    <div class="card-body">
                        <a href="#" class="d-flex justify-content-center">
                            <img src="<?php echo e(asset($site_settings['logo'] ?? 'assets/images/CepatDapat.png')); ?>" alt="image"
                                style="width: 70%;" />
                        </a>
                        
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger mt-3">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <h5 class="my-4 d-flex justify-content-center">Sign in Dengan Username</h5>

                        <form action="<?php echo e(route('login.post')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control" id="floatingInput"
                                    placeholder="Username" value="<?php echo e(old('username')); ?>" required />
                                <label for="floatingInput">Username</label>
                            </div>

                            
                            <div class="form-floating mb-3">
                                <input type="password" name="password" class="form-control" id="floatingInput1"
                                    placeholder="Password" required />
                                <label for="floatingInput1">Password</label>
                            </div>

                            
                            <div id="recaptchaContainer" class="mb-3 d-flex justify-content-center">
                                <div class="g-recaptcha" data-sitekey="<?php echo e($recaptchaSiteKey); ?>"></div>
                            </div>

                            
                            <div id="mathCaptchaContainer" class="mb-3 math-captcha-container" style="display: none;">
                                <p class="mb-2 text-muted" style="font-size: 0.85rem;">
                                    <i class="ti ti-alert-circle"></i> reCAPTCHA tidak tersedia. Jawab pertanyaan
                                    berikut:
                                </p>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="math-question"><?php echo e($mathQuestion); ?></span>
                                    <input type="number" name="math_answer" class="form-control"
                                        style="max-width: 100px;" placeholder="Jawaban" />
                                </div>
                            </div>

                            <div class="d-flex mt-1 justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1"
                                        checked="" />
                                    <label class="form-check-label text-muted" for="customCheckc1">Remember me</label>
                                </div>
                                <a href="/forgot-password" class="text-secondary">Lupa Password?</a>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-secondary shadow-sm">Sign In</button>
                            </div>
                        </form>
                        <hr />
                        <h5 class="d-flex justify-content-center">
                            Tidak memiliki akun? &nbsp; <a href="<?php echo e(route('register')); ?>"
                                class="link-primary">Register</a>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('assets/js/plugins/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/simplebar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/icon/custom-font.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/script.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/theme.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/feather.min.js')); ?>"></script>

    
    <script>
        var recaptchaLoaded = false;

        function onRecaptchaLoad() {
            recaptchaLoaded = true;
        }

        function showMathFallback() {
            document.getElementById('recaptchaContainer').style.display = 'none';
            document.getElementById('mathCaptchaContainer').style.display = 'block';
        }
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad" async defer
        onerror="showMathFallback()"></script>
    <script>
        // Additional timeout check — if reCAPTCHA hasn't loaded after 5 seconds, show math
        setTimeout(function () {
            if (!recaptchaLoaded) {
                showMathFallback();
            }
        }, 5000);
    </script>

    <script>layout_change('light');</script>
    <script>font_change('Roboto');</script>
    <script>change_box_container('false');</script>
    <script>layout_caption_change('true');</script>
    <script>layout_rtl_change('false');</script>
    <script>preset_change('preset-1');</script>
</body>

</html><?php /**PATH C:\Users\jason\Documents\Brian\CepatDapat_new\resources\views/auth/login.blade.php ENDPATH**/ ?>