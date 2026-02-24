

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-sm-12">
            <style>
                .auction-card {
                    border: none;
                    border-radius: 16px;
                    transition: transform 0.3s ease;
                    background-color: #ffffff;
                    position: relative;
                }

                .auction-card:hover {
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

                .canceled-badge {
                    position: absolute;
                    top: 12px;
                    right: 12px;
                    z-index: 2;
                }
            </style>

            <div class="row">
                <div class="col-sm-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Lelang Tercancel</h5>
                    </div>

                    
                    <div class="mb-4">
                        <form action="<?php echo e(route('admin.canceled_auctions')); ?>" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control rounded-pill px-4"
                                placeholder="Cari nama barang..." value="<?php echo e($search ?? ''); ?>">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Cari</button>
                        </form>
                    </div>

                    <div class="row g-4">
                        <?php $__empty_1 = true; $__currentLoopData = $barang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $fotos = array_filter(explode(',', $item->foto));
                                $foto_utama = $fotos[0] ?? null;
                            ?>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <div class="card auction-card shadow-sm h-100 position-relative">
                                    <span class="badge bg-danger canceled-badge"><i class="ti ti-ban me-1"></i>Canceled</span>
                                    <div class="card-img-container">
                                        <?php if($foto_utama): ?>
                                            <img src="<?php echo e(asset('img_barang/' . $foto_utama)); ?>" alt="<?php echo e($item->nama_barang); ?>"
                                                style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">
                                        <?php else: ?>
                                            <div class="h-100 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-photo text-muted fs-1" style="font-size: 3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="fw-bold text-dark text-truncate mb-1"><?php echo e($item->nama_barang); ?></h5>

                                        <p class="text-muted small mb-2">
                                            <i class="ti ti-user me-1"></i> <?php echo e($item->username); ?>

                                        </p>

                                        <p class="price-tag mb-2">Rp
                                            <span><?php echo e(number_format($item->harga_awal, 0, ',', '.')); ?></span></p>

                                        <p class="text-muted small mb-4 flex-grow-1">
                                            <?php echo e(Str::limit($item->deskripsi, 50)); ?>

                                        </p>

                                        <div class="mt-auto">
                                            <button type="button"
                                                class="btn btn-info w-100 py-2 mb-2 shadow-sm fw-bold text-white"
                                                data-bs-toggle="modal" data-bs-target="#modalInfo<?php echo e($item->id_lelang); ?>">
                                                <i class="ti ti-info-circle me-1"></i> INFO
                                            </button>

                                            <div class="badge bg-danger w-100 py-2 fw-bold">
                                                Canceled
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="modal fade" id="modalInfo<?php echo e($item->id_lelang); ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="fw-bold mb-0">Detail Lelang (Canceled)</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <th width="35%">ID Lelang</th>
                                                            <td>#<?php echo e($item->id_lelang); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Pemilik (ID <?php echo e($item->id_user); ?>)</th>
                                                            <td><?php echo e($item->username); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Moderator (ID <?php echo e($item->moderator_id ?? '-'); ?>)</th>
                                                            <td><?php echo e($item->moderator_username ?? '-'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Penawar Tertinggi</th>
                                                            <td><?php echo e($item->buyer_username ?? '-'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Nama Barang</th>
                                                            <td><?php echo e($item->nama_barang); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Deskripsi</th>
                                                            <td><?php echo e($item->deskripsi); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Harga Awal</th>
                                                            <td>Rp <?php echo e(number_format($item->harga_awal, 0, ',', '.')); ?></td>
                                                        </tr>
                                                        <?php if($item->harga_terjual): ?>
                                                            <tr>
                                                                <th>Harga Terjual</th>
                                                                <td class="fw-bold text-success">Rp
                                                                    <?php echo e(number_format($item->harga_terjual, 0, ',', '.')); ?></td>
                                                            </tr>
                                                        <?php endif; ?>
                                                        <tr>
                                                            <th>Foto</th>
                                                            <td>
                                                                <?php
                                                                    $fotos = array_filter(explode(',', $item->foto));
                                                                ?>
                                                                <?php if(count($fotos) > 0): ?>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <?php $__currentLoopData = $fotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <a href="javascript:void(0)"
                                                                                onclick="viewImage('<?php echo e(asset('img_barang/' . trim($f))); ?>')">
                                                                                <img src="<?php echo e(asset('img_barang/' . trim($f))); ?>"
                                                                                    class="img-thumbnail"
                                                                                    style="height: 80px; width: 80px; object-fit: cover;"
                                                                                    alt="Foto Barang">
                                                                            </a>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status</th>
                                                            <td><span class="badge bg-danger">Canceled</span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Input</th>
                                                            <td><?php echo e($item->tgl_input); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Diterima (Accepted)</th>
                                                            <td><?php echo e($item->tgl_status_accepted ?? '-'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Ditolak (Rejected)</th>
                                                            <td><?php echo e($item->tgl_status_rejected ?? '-'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Mulai (Open)</th>
                                                            <td><?php echo e($item->tgl_status_open ?? '-'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Dibatalkan (Canceled)</th>
                                                            <td><?php echo e($item->tgl_status_canceled ?? '-'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Batas Akhir (Sold)</th>
                                                            <td><?php echo e($item->tgl_status_sold ?? '-'); ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            
                                            <hr>
                                            <div class="d-grid">
                                                <button class="btn btn-warning fw-bold" type="button"
                                                    onclick="openUncancelModal(<?php echo e($item->id_lelang); ?>, '<?php echo e($item->nama_barang); ?>')">
                                                    <i class="ti ti-arrow-back-up me-1"></i> Uncancel Lelang Ini
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-12 text-center py-5">
                                <div class="py-5">
                                    <i class="ti ti-box display-1 text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <h5 class="mt-3 text-muted">Tidak ada lelang yang dibatalkan</h5>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="modal fade" id="modalViewImage" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content bg-transparent border-0 shadow-none">
                        <div class="modal-body p-0 text-center position-relative">
                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                                data-bs-dismiss="modal" aria-label="Close"
                                style="z-index: 1056; filter: invert(1); opacity: 1;"></button>
                            <img id="previewImage" src="" alt="Preview" class="img-fluid rounded shadow-lg"
                                style="max-height: 90vh;">
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="modal fade" id="modalUncancelAuction" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold mb-0 text-warning">Konfirmasi Uncancel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?php echo e(route('admin.uncancel_auction')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id_lelang" id="uncancelIdLelang">
                            <div class="modal-body p-4">
                                <div class="alert alert-warning border-0 rounded-3 mb-3 d-flex align-items-center"
                                    role="alert">
                                    <i class="ti ti-arrow-back-up fs-4 me-2"></i>
                                    <div>
                                        Anda akan <strong>meng-uncancel</strong> lelang: <br>
                                        <strong id="uncancelNamaBarang"></strong>
                                    </div>
                                </div>
                                <p class="text-muted small">Status lelang akan dikembalikan ke <strong>accepted</strong>.
                                </p>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alasan Uncancel <span
                                            class="text-danger">*</span></label>
                                    <textarea name="alasan" class="form-control" rows="4" required
                                        placeholder="Jelaskan alasan uncancel lelang ini secara rinci..."></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-warning fw-bold py-2">Ya, Uncancel Lelang</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php $__env->startPush('scripts'); ?>
                <script>
                    function viewImage(url) {
                        const modal = new bootstrap.Modal(document.getElementById('modalViewImage'));
                        document.getElementById('previewImage').src = url;
                        modal.show();
                    }

                    function openUncancelModal(id, namaBarang) {
                        document.getElementById('uncancelIdLelang').value = id;
                        document.getElementById('uncancelNamaBarang').innerText = namaBarang;

                        const modal = new bootstrap.Modal(document.getElementById('modalUncancelAuction'));
                        modal.show();
                    }
                </script>
            <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\jason\Documents\Brian\CepatDapat_new\resources\views\auction\lelang_tercancel.blade.php ENDPATH**/ ?>