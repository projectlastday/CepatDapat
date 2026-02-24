

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
    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Lelangku</h5>
            </div>

            
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="<?php echo e(route('auction.lelangku', ['status' => 'all'])); ?>"
                    class="btn btn-sm rounded-pill px-4 fw-bold <?php echo e($current_filter == 'all' ? 'btn-primary shadow' : 'btn-outline-secondary'); ?>">
                    All
                </a>

                <?php
                    $statuses = ['pending', 'accepted', 'rejected', 'open', 'sold', 'unsold', 'canceled'];
                ?>
                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('auction.lelangku', ['status' => $s])); ?>"
                        class="btn btn-sm rounded-pill px-4 fw-bold <?php echo e($current_filter == $s ? 'btn-primary shadow' : 'btn-outline-secondary'); ?>">
                        <?php echo e(ucfirst($s)); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="row g-4">
                        <?php $__empty_1 = true; $__currentLoopData = $barang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $fotos = array_filter(explode(',', $item->foto));
                                $foto_utama = $fotos[0] ?? null;

                                // Normalize phone for wa.me link
                                $rawPhone = preg_replace('/[^0-9]/', '', $item->buyer_telepon ?? '');
                                if ($rawPhone && str_starts_with($rawPhone, '0')) {
                                    $rawPhone = '62' . substr($rawPhone, 1);
                                }
                                $waLink = $rawPhone ? 'https://wa.me/' . $rawPhone : null;
                            ?>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <div class="card auction-card shadow-sm h-100 position-relative">
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

                                        <p class="price-tag mb-4">Rp <span
                                                id="card-price-<?php echo e($item->id_lelang); ?>"><?php echo e(number_format($item->harga_awal, 0, ',', '.')); ?></span>
                                        </p>

                                        <div class="mt-auto">
                                            <?php if($item->status == 'sold'): ?>
                                                <p class="text-muted small mb-2 text-center">
                                                    Pemenang: <strong><?php echo e($item->buyer_username ?? 'Tidak diketahui'); ?></strong>
                                                </p>
                                                <div class="badge bg-success w-100 py-2 mb-2 shadow-sm fs-6">Sold</div>
                                            <?php elseif($item->status == 'rejected'): ?>
                                                <div class="alert alert-danger p-2 mb-2 small text-center rounded-3 fw-bold">Ditolak
                                                    Moderator</div>
                                            <?php elseif($item->status == 'open'): ?>
                                                <div class="badge bg-primary w-100 py-2 mb-2 shadow-sm">Sedang Berlangsung</div>
                                            <?php elseif($item->status != 'accepted'): ?>
                                                <div class="badge bg-light text-dark border w-100 py-2 fw-bold mb-2">
                                                    <?php echo e(ucfirst($item->status)); ?></div>
                                            <?php endif; ?>

                                            
                                            <button type="button" class="btn btn-info w-100 py-2 mb-2 shadow-sm fw-bold text-white d-flex justify-content-center align-items-center gap-2"
                                                data-bs-toggle="modal" data-bs-target="#modalInfo<?php echo e($item->id_lelang); ?>">
                                                <i class="ti ti-info-circle fs-5"></i>
                                                <span>INFO</span>
                                            </button>

                                            <?php if($item->status == 'accepted'): ?>
                                                <button type="button" class="btn btn-success w-100 py-2 shadow-sm"
                                                    data-bs-toggle="modal" data-bs-target="#modalMulai<?php echo e($item->id_lelang); ?>">
                                                    Mulai Lelang
                                                </button>
                                            <?php elseif($item->status == 'open'): ?>
                                                <div class="text-center">
                                                    <small class="text-danger fw-bold countdown-timer"
                                                        id="timer-<?php echo e($item->id_lelang); ?>" data-target="<?php echo e($item->tgl_status_sold); ?>" data-style="short"
                                                        style="font-size: 0.85rem;">
                                                        Memuat Countdown...
                                                    </small>
                                                </div>
                                            <?php elseif($item->status == 'sold'): ?>
                                                <?php if($waLink): ?>
                                                    <a href="<?php echo e($waLink); ?>" target="_blank" rel="noopener"
                                                        class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                                        <i class="ti ti-brand-whatsapp me-1"></i> Hubungi Penawar
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary w-100 py-2 fw-bold" disabled>
                                                        <i class="ti ti-brand-whatsapp me-1"></i> Hubungi Penawar
                                                    </button>
                                                    <small class="text-muted d-block text-center mt-1">Nomor WA penawar tidak tersedia</small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                
                                <?php if($item->status == 'accepted'): ?>
                                    <div class="modal fade" id="modalMulai<?php echo e($item->id_lelang); ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="fw-bold mb-0">Atur Durasi Lelang</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="<?php echo e(route('auction.start')); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="id_lelang" value="<?php echo e($item->id_lelang); ?>">
                                                    <div class="modal-body text-center p-4">
                                                        <div class="alert alert-info border-0 rounded-4 mb-4"
                                                            style="background-color: #e7f3ff;">
                                                            <i class="ti ti-info-circle text-primary mb-2 d-block fs-4"></i>
                                                            <p class="small text-dark mb-0">
                                                                <strong>Informasi:</strong> Username Anda akan ditampilkan
                                                                sebagai identitas pelelang pada halaman Katalog setelah lelang
                                                                dimulai.
                                                            </p>
                                                        </div>

                                                        <p class="text-muted mb-4">Pilih berapa hari lelang ini akan berlangsung:
                                                        </p>
                                                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                                                            <?php for($i = 1; $i <= 7; $i++): ?>
                                                                <input type="radio" class="btn-check" name="durasi"
                                                                    id="day<?php echo e($item->id_lelang); ?><?php echo e($i); ?>" value="<?php echo e($i); ?>" <?php echo e($i == 7 ? 'checked' : ''); ?>>
                                                                <label
                                                                    class="btn btn-outline-success btn-lg rounded-4 px-4 py-3 fw-bold"
                                                                    for="day<?php echo e($item->id_lelang); ?><?php echo e($i); ?>">
                                                                    <?php echo e($i); ?> <br> <span class="small"
                                                                        style="font-size: 0.6em;">Hari</span>
                                                                </label>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <div class="mt-4">
                                                            <p class="text-muted small mb-0">Lelang akan berakhir pukul
                                                                <strong>24:00</strong> di hari terakhir.</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4 pt-0">
                                                        <button type="submit"
                                                            class="btn btn-success btn-lg w-100 fw-bold rounded-4 shadow">Mulai
                                                            Lelang</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                
                                <div class="modal fade" id="modalInfo<?php echo e($item->id_lelang); ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="fw-bold mb-0">Detail Barang Lelang</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                            <?php if($item->status == 'sold'): ?>
                                                                <tr>
                                                                    <th>Harga Terjual</th>
                                                                    <td class="fw-bold text-success">Rp <?php echo e(number_format($item->harga_terjual ?? 0, 0, ',', '.')); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Pemenang</th>
                                                                    <td><?php echo e($item->buyer_username ?? '-'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Telepon Pemenang</th>
                                                                    <td><?php echo e($item->buyer_telepon ?? '-'); ?></td>
                                                                </tr>
                                                            <?php endif; ?>
                                                            <tr>
                                                                <th>Foto</th>
                                                                <td>
                                                                    <?php
                                                                        $modalFotos = array_filter(explode(',', $item->foto));
                                                                    ?>
                                                                    <?php if(count($modalFotos) > 0): ?>
                                                                        <div class="d-flex flex-wrap gap-2">
                                                                            <?php $__currentLoopData = $modalFotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <a href="javascript:void(0)" onclick="viewImage('<?php echo e(asset('img_barang/' . trim($f))); ?>')">
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
                                                                <th>Tanggal Berakhir</th>
                                                                <td><?php echo e($item->tgl_status_sold ?? '-'); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Status</th>
                                                                <td>
                                                                    <div class="badge bg-secondary"><?php echo e(ucfirst($item->status)); ?></div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                
                                                <?php if($item->status == 'sold' && $waLink): ?>
                                                    <div class="d-grid mt-2">
                                                        <a href="<?php echo e($waLink); ?>" target="_blank" rel="noopener"
                                                            class="btn btn-success fw-bold py-2">
                                                            <i class="ti ti-brand-whatsapp me-1"></i> Hubungi Pemenang via WhatsApp
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-12 text-center py-5">
                                <div class="py-5">
                                    <i class="ti ti-box display-1 text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <h5 class="mt-3 text-muted">Tidak ada barang dengan status "<?php echo e(ucfirst($current_filter)); ?>"
                                    </h5>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // SCRIPT COUNTDOWN (Berjalan lokal di halaman ini agar presisi per detik)
            function updateCountdowns() {
                const timers = document.querySelectorAll('.countdown-timer');
                timers.forEach(timer => {
                    const targetTimestamp = timer.getAttribute('data-target');
                    if (!targetTimestamp) return;

                    const targetDate = new Date(targetTimestamp).getTime();
                    const now = new Date().getTime();
                    const distance = targetDate - now;

                    if (distance < 0) {
                        timer.innerHTML = "Lelang Berakhir";
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    let display = "";
                    if (timer.dataset.style === 'short') {
                        if (days > 0) display += days + "h ";
                        display += hours + "j " + minutes + "m " + seconds + "d";
                    } else {
                        if (days > 0) display += days + " Hari ";
                        display += hours + " Jam " + minutes + " Menit " + seconds + " Detik";
                    }
                    timer.innerHTML = display;
                });
            }
            setInterval(updateCountdowns, 1000);
            document.addEventListener('DOMContentLoaded', updateCountdowns);
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/brian/Files/Web Project/CepatDapat_new/resources/views/auction/lelangku.blade.php ENDPATH**/ ?>