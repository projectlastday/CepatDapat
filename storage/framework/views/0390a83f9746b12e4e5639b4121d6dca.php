

<?php $__env->startSection('content'); ?>
    <style>
        .moderasi-card {
            border: none;
            border-radius: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #ffffff;
            position: relative;
        }

        .moderasi-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
        }

        .card-img-container {
            height: 200px;
            border-radius: 16px 16px 0 0;
            overflow: hidden;
            background-color: #f8f9fa;
        }

        .price-tag {
            color: #dc3545;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .badge-review {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            font-weight: 600;
        }
    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="card bg-light-warning border border-warning shadow-none">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Antrian Moderasi</h5>
                            <p class="text-muted small mb-0">Review barang baru sebelum tampil di katalog</p>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                            <i class="ti ti-hourglass-empty me-1"></i><?php echo e($barang->count()); ?> Menunggu
                        </span>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <?php $__empty_1 = true; $__currentLoopData = $barang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $fotos = explode(',', $item->foto);
                        $foto_utama = $fotos[0];
                    ?>
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="card moderasi-card shadow-sm h-100">
                            <div class="card-img-container">
                                <?php if($foto_utama): ?>
                                    <img src="<?php echo e(asset('img_barang/' . $foto_utama)); ?>" alt="<?php echo e($item->nama_barang); ?>"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div class="h-100 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-photo text-muted fs-1" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold text-dark text-truncate mb-1"><?php echo e($item->nama_barang); ?></h5>
                                <p class="price-tag mb-3">Rp <?php echo e(number_format($item->harga_awal, 0, ',', '.')); ?></p>

                                <p class="text-muted small mb-4 flex-grow-1">
                                    <?php echo e(Str::limit($item->deskripsi, 60)); ?>

                                </p>

                                <div class="d-grid mt-auto">
                                    <button type="button" class="btn btn-primary py-2 shadow-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalReview<?php echo e($item->id_lelang); ?>">
                                        <i class="ti ti-search me-2"></i>Review
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="modal fade" id="modalReview<?php echo e($item->id_lelang); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold mt-2 ms-2"><i class="ti ti-eye me-2 text-primary"></i>Detail Review
                                        Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <?php
                                                $fotos_modal = array_filter(explode(',', $item->foto));
                                                $foto_utama_modal = $fotos_modal[0] ?? null;
                                                $jml_foto_tambahan = count($fotos_modal) - 1;
                                                $col_class = 'col-4';
                                                if ($jml_foto_tambahan == 1)
                                                    $col_class = 'col-12';
                                                elseif ($jml_foto_tambahan == 2)
                                                    $col_class = 'col-6';
                                            ?>

                                            <div class="rounded-4 overflow-hidden mb-3 shadow-sm border"
                                                style="height: 300px; background: #f8f9fa; cursor: zoom-in;"
                                                onclick="showFullScreen('<?php echo e(asset('img_barang/' . $foto_utama_modal)); ?>')">
                                                <?php if($foto_utama_modal): ?>
                                                    <img src="<?php echo e(asset('img_barang/' . $foto_utama_modal)); ?>" class="w-100 h-100"
                                                        style="object-fit: contain;">
                                                <?php else: ?>
                                                    <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                                                        <i class="ti ti-photo fs-1"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="row g-2">
                                                <?php $__currentLoopData = $fotos_modal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($key > 0): ?>
                                                        <div class="<?php echo e($col_class); ?>">
                                                            <div class="rounded-3 overflow-hidden border shadow-sm"
                                                                style="height: 80px; cursor: zoom-in;"
                                                                onclick="showFullScreen('<?php echo e(asset('img_barang/' . $f)); ?>')">
                                                                <img src="<?php echo e(asset('img_barang/' . $f)); ?>" class="w-100 h-100"
                                                                    style="object-fit: cover;">
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>

                                        <div class="col-md-6 d-flex flex-column">
                                            <div class="mb-3">
                                                <label class="text-muted small fw-bold text-uppercase">Nama Barang</label>
                                                <h3 class="fw-bold text-dark"><?php echo e($item->nama_barang); ?></h3>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="text-muted small fw-bold text-uppercase">Pengunggah</label>
                                                    <p class="mb-0 text-primary fw-bold">
                                                        <i class="ti ti-user-circle me-1"></i><?php echo e($item->username); ?>

                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <label class="text-muted small fw-bold text-uppercase">Waktu Input</label>
                                                    <p class="mb-0 text-muted small">
                                                        <i
                                                            class="ti ti-calendar-event me-1"></i><?php echo e(date('d M Y', strtotime($item->tgl_input))); ?>

                                                    </p>
                                                    <p class="mb-0 text-muted small">
                                                        <i
                                                            class="ti ti-clock me-1"></i><?php echo e(date('H:i', strtotime($item->tgl_input))); ?>

                                                        WIB
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="mb-4 flex-grow-1">
                                                <label class="text-muted small fw-bold text-uppercase">Deskripsi</label>
                                                <div class="p-3 bg-light rounded-3 shadow-sm border"
                                                    style="font-size: 0.85rem; min-height: 100px; max-height: 200px; overflow-y: auto;">
                                                    <?php echo e($item->deskripsi ?: 'Tidak ada deskripsi barang.'); ?>

                                                </div>
                                            </div>

                                            <div class="row g-2 pt-3 border-top mb-2">
                                                <div class="col-6">
                                                    <form action="<?php echo e(route('admin.moderasi_aksi')); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="id_lelang" value="<?php echo e($item->id_lelang); ?>">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit"
                                                            class="btn btn-outline-danger w-100 fw-bold py-2 rounded-3">
                                                            <i class="ti ti-x me-2"></i>Reject
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="col-6">
                                                    <form action="<?php echo e(route('admin.moderasi_aksi')); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="id_lelang" value="<?php echo e($item->id_lelang); ?>">
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button type="submit"
                                                            class="btn btn-success w-100 fw-bold py-2 rounded-3 shadow-sm">
                                                            <i class="ti ti-check me-2"></i>Accept
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <form action="<?php echo e(route('admin.suspend_user')); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id_user" value="<?php echo e($item->id_user); ?>">
                                                <button type="submit" class="btn btn-dark w-100 fw-bold py-2 rounded-3"
                                                    onclick="return confirm('PERINGATAN: Akun <?php echo e($item->username); ?> akan DIHAPUS beserta semua barang lelangnya. Lanjutkan?')">
                                                    <i class="ti ti-ban me-2"></i>Suspend Penjual
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12 text-center py-5">
                        <div class="py-5">
                            <i class="ti ti-circle-check display-1 text-success opacity-50" style="font-size: 4rem;"></i>
                            <h3 class="mt-4 fw-bold text-dark">Antrian Kosong</h3>
                            <p class="text-muted">Semua barang telah dimoderasi.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="imageViewerModal" tabindex="-1" aria-hidden="true"
        style="background: rgba(0,0,0,0.9); z-index: 3000;">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 text-center">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4"
                        data-bs-dismiss="modal"></button>
                    <img src="" id="fullScreenImage" class="img-fluid rounded-4 shadow-lg" style="max-height: 90vh;">
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function showFullScreen(imgSrc) {
                document.getElementById('fullScreenImage').src = imgSrc;
                new bootstrap.Modal(document.getElementById('imageViewerModal')).show();
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/brian/Files/Web Project/CepatDapat_new/resources/views/moderasi.blade.php ENDPATH**/ ?>