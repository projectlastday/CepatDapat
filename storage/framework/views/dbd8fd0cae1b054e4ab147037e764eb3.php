<!doctype html>
<html lang="en">

<head>
    <title>CepatDapat</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        id="main-font-link" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/phosphor/duotone/style.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/tabler-icons.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/feather.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/fontawesome.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/material.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>" id="main-style-link" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-preset.css')); ?>" />
</head>

<body>
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="/" class="b-brand text-primary">
                    <img src="<?php echo e(asset($site_settings['logo'] ?? 'assets/images/CepatDapat.png')); ?>"
                        style="width:70%" />
                </a>
            </div>
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <?php if(in_array(session('id_user_type'), [1, 7])): ?>
                        <li class="pc-item">
                            <a href="/" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                                <span class="pc-mtext">Dashboard</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(in_array(session('id_user_type'), [1, 5, 7])): ?>
                        <li class="pc-item">
                            <a href="/laporan" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-file-analytics"></i></span>
                                <span class="pc-mtext">Laporan</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="pc-item">
                        <a href="/catalog" class="pc-link">
                            <span class="pc-micon">
                                <img src="<?php echo e(asset('assets/images/bag.svg')); ?>" alt="logo"
                                    style="width: 20px; height: 20px;">
                            </span>
                            <span class="pc-mtext">Katalog</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="/auction-add" class="pc-link">
                            <span class="pc-micon">
                                <img src="<?php echo e(asset('assets/images/bag-plus-fill.svg')); ?>" alt="bag plus fill"
                                    style="width: 20px; height: 20px;">
                            </span>
                            <span class="pc-mtext">Pasang Lelang</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="/lelangku" class="pc-link">
                            <span class="pc-micon">
                                <img src="<?php echo e(asset('assets/images/clock-history.svg')); ?>" alt="lelangku"
                                    style="width: 20px; height: 20px;">
                            </span>
                            <span class="pc-mtext">Lelangku</span>
                        </a>
                    </li>
                    <?php if(in_array(session('id_user_type'), [1, 2])): ?>
                        <li class="pc-item">
                            <a href="/moderasi" class="pc-link">
                                <span class="pc-micon">
                                    <i class="ti ti-clipboard-check"></i>
                                </span>
                                <span class="pc-mtext">Moderasi</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(in_array(session('id_user_type'), [1, 6, 7])): ?>
                        <li class="pc-item pc-hasmenu">
                            <a href="#!" class="pc-link">
                                <span class="pc-micon">
                                    <i class="ti ti-database"></i>
                                </span>
                                <span class="pc-mtext">Manajemen Data</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="pc-submenu">
                                <li class="pc-item">
                                    <a class="pc-link" href="/manajemen-lelang">Manajemen Lelang</a>
                                </li>
                                <?php if(session('id_user_type') == 7): ?>
                                    <li class="pc-item">
                                        <a class="pc-link" href="/lelang-tercancel">Lelang Tercancel</a>
                                    </li>
                                    <li class="pc-item">
                                        <a class="pc-link" href="/lelang-terhapus">Lelang Terhapus</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if(session('id_user_type') == 7): ?>
                        <li class="pc-item">
                            <a href="/history-data" class="pc-link">
                                <span class="pc-micon">
                                    <i class="ti ti-history"></i>
                                </span>
                                <span class="pc-mtext">History Data</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="pc-header">
        <div class="header-wrapper">
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <li class="pc-h-item header-mobile-collapse">
                        <a href="#" class="pc-head-link head-link-secondary ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link head-link-secondary ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="ms-auto">
                <ul class="list-unstyled">
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link head-link-primary dropdown-toggle arrow-none me-0"
                            data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                            aria-expanded="false">
                            <span><i class="ti ti-settings"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <?php if(session('id_user_type') == 7): ?>
                                    <a href="/setting-website" class="dropdown-item"><i class="ti ti-settings"></i>
                                        <span>Setting Website</span></a>
                                <?php endif; ?>
                                <a href="/logout" class="dropdown-item"><i class="ti ti-logout"></i>
                                    <span>Logout</span></a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="pc-container">
        <div class="pc-content">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm-6 my-1">
                    <p class="m-0">CepatDapat Website</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo e(asset('assets/js/plugins/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/simplebar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/icon/custom-font.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/script.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/theme.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/feather.min.js')); ?>"></script>

    <script>layout_change('light');</script>
    <script>font_change('Roboto');</script>
    <script>change_box_container('false');</script>
    <script>layout_caption_change('true');</script>
    <script>layout_rtl_change('false');</script>
    <script>preset_change('preset-1');</script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH C:\Users\jason\Documents\Brian\CepatDapat_new\resources\views\layouts\app.blade.php ENDPATH**/ ?>