

<?php $__env->startSection('content'); ?>
    <style>
        /* Style Premium dari Proyek Lama Anda */
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
            background-color: #e9ecef;
            cursor: zoom-in;
        }

        .card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .price-tag {
            color: #f44336;
            /* Warna Merah Berry untuk harga */
            font-size: 1.2rem;
            font-weight: 800;
        }

        .bid-input-display {
            font-size: 1.6rem;
            font-weight: 800;
            text-align: center;
            border: none;
            width: 100%;
            outline: none;
            background: transparent;
        }

        .bid-container {
            border: 2px solid #e3f2fd;
            border-radius: 12px;
            padding: 5px 15px;
            background: #fff;
        }

        .btn-circle-fixed {
            width: 45px !important;
            height: 45px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>

    <div class="row g-4 text-start">
        <?php $__empty_1 = true; $__currentLoopData = $barang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $fotos = array_filter(explode(',', $item->foto));
                $foto_utama = $fotos[0] ?? null;
                $jml_tambahan = count($fotos) - 1;
                $col_class = $jml_tambahan == 1 ? 'col-12' : ($jml_tambahan == 2 ? 'col-6' : 'col-4');
                // Menggunakan session id_user sesuai logika MainController
                $is_owner = (session('id_user') == $item->id_user); 
            ?>

            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card auction-card shadow-sm h-100">
                    <div class="card-img-container"
                        onclick="showFullScreen('<?php echo e(asset('img_barang/' . ($foto_utama ?: 'no-image.png'))); ?>')">
                        <img src="<?php echo e(asset('img_barang/' . ($foto_utama ?: 'no-image.png'))); ?>" alt="<?php echo e($item->nama_barang); ?>">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold text-dark text-truncate mb-1"><?php echo e($item->nama_barang); ?></h5>
                        <p class="price-tag mb-2">Rp <span
                                id="card-price-<?php echo e($item->id_lelang); ?>"><?php echo e(number_format($item->harga_awal, 0, ',', '.')); ?></span>
                        </p>
                        <p class="text-muted small mb-3 flex-grow-1"><?php echo e(Str::limit($item->deskripsi, 60)); ?></p>

                        <div class="mb-3">
                            
                            <span
                                class="badge bg-light-danger text-danger countdown-timer py-2 px-2 fw-bold w-100 fs-6 font-monospace"
                                data-target="<?php echo e(\Carbon\Carbon::parse($item->tgl_status_sold)->toIso8601String()); ?>"
                                data-style="short">
                                <i class="ti ti-clock me-2"></i>Memuat...
                            </span>
                        </div>

                        <button class="btn btn-primary w-100 py-2 fw-bold shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#modalTawar<?php echo e($item->id_lelang); ?>">
                            <i class="ti ti-gavel me-1"></i> TAWAR
                        </button>
                    </div>
                </div>

                
                <div class="modal fade" id="modalTawar<?php echo e($item->id_lelang); ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                            <div class="modal-header border-0 pb-0 text-start">
                                <h5 class="fw-bold mt-2 ms-2"><i class="ti ti-info-circle me-2 text-primary"></i>Detail
                                    Penawaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-4 text-start">
                                    <div class="col-md-6 text-center">
                                        <div class="rounded-4 overflow-hidden mb-3 shadow-sm border"
                                            style="height: 250px; background: #f8f9fa; cursor: zoom-in;"
                                            onclick="showFullScreen('<?php echo e(asset('img_barang/' . ($foto_utama ?: 'no-image.png'))); ?>')">
                                            <img src="<?php echo e(asset('img_barang/' . ($foto_utama ?: 'no-image.png'))); ?>"
                                                class="w-100 h-100" style="object-fit: contain;">
                                        </div>
                                        <div class="row g-2">
                                            <?php $__currentLoopData = $fotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($key > 0): ?>
                                                    <div class="<?php echo e($col_class); ?>">
                                                        <img src="<?php echo e(asset('img_barang/' . $f)); ?>"
                                                            class="w-100 rounded border shadow-sm"
                                                            style="height: 60px; object-fit: cover; cursor: zoom-in;"
                                                            onclick="showFullScreen('<?php echo e(asset('img_barang/' . $f)); ?>')">
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex flex-column">
                                        <h3 class="fw-bold text-dark"><?php echo e($item->nama_barang); ?></h3>
                                        <div class="row mb-3">
                                            <div class="col-6 border-end">
                                                <label class="text-muted small fw-bold">PELELANG</label>
                                                <p class="mb-0 text-primary fw-bold"><?php echo e($item->username); ?></p>
                                            </div>
                                            <div class="col-6 ps-3">
                                                <label class="text-muted small fw-bold">TAWARAN TERTINGGI</label>
                                                <p class="mb-0 text-success fw-bold" id="bidder-info-<?php echo e($item->id_lelang); ?>">
                                                    <?php if(isset($item->bidder_username)): ?>
                                                        <?php echo e($item->bidder_username); ?>

                                                    <?php else: ?>
                                                        Belum ada
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>

                                        
                                        <div class="mb-4 text-center w-100">
                                            <div class="alert alert-danger d-block w-100 py-2 px-3 shadow-sm border-2">
                                                <i class="ti ti-clock me-2 fs-5"></i>
                                                <span class="fw-bold fs-5 countdown-timer font-monospace"
                                                    data-target="<?php echo e(\Carbon\Carbon::parse($item->tgl_status_sold)->toIso8601String()); ?>">
                                                    Memuat...
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="text-muted small fw-bold">DESKRIPSI</label>
                                            <div class="p-3 bg-light rounded-3"
                                                style="font-size: 0.85rem; min-height: 80px; max-height: 120px; overflow-y: auto;">
                                                <?php echo e($item->deskripsi ?: 'Tidak ada deskripsi.'); ?>

                                            </div>
                                        </div>

                                        <div class="pt-3 border-top mt-auto">
                                            
                                            <form action="/auction/bid" method="POST" id="form-bid-<?php echo e($item->id_lelang); ?>">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id_lelang" value="<?php echo e($item->id_lelang); ?>">
                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-circle-fixed shadow-sm"
                                                        onclick="adjustBid('<?php echo e($item->id_lelang); ?>', -1000)"><span>−</span></button>
                                                    <div class="bid-container flex-grow-1 shadow-sm text-center">
                                                        <input type="text" id="displayBid<?php echo e($item->id_lelang); ?>"
                                                            class="bid-input-display"
                                                            value="<?php echo e(number_format($item->harga_awal, 0, ',', '.')); ?>"
                                                            readonly>
                                                        <input type="hidden" id="inputBid<?php echo e($item->id_lelang); ?>"
                                                            name="nominal_tawaran" value="<?php echo e($item->harga_awal); ?>"
                                                            data-min="<?php echo e($item->harga_awal); ?>">
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-outline-success btn-circle-fixed shadow-sm"
                                                        onclick="adjustBid('<?php echo e($item->id_lelang); ?>', 1000)"><span>+</span></button>
                                                </div>
                                                <button type="button" class="btn btn-primary btn-lg w-100 fw-bold shadow"
                                                    onclick="confirmBid('<?php echo e($item->id_lelang); ?>', <?php echo e($is_owner ? 'true' : 'false'); ?>, '<?php echo e(addslashes($item->nama_barang)); ?>')">
                                                    KIRIM TAWARAN
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <h5>Belum ada lelang yang aktif saat ini.</h5>
            </div>
        <?php endif; ?>
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

    <script>
        // Fungsi Tambah/Kurang Harga
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function adjustBid(id, amount) {
            const hiddenInput = document.getElementById('inputBid' + id);
            const displayInput = document.getElementById('displayBid' + id);
            let currentVal = parseInt(hiddenInput.value);
            const minVal = parseInt(hiddenInput.getAttribute('data-min'));

            let newVal = currentVal + amount;
            if (newVal < minVal) newVal = minVal;

            hiddenInput.value = newVal;
            displayInput.value = formatRupiah(newVal);
        }

        let selectedFormId = null;

        function confirmBid(id, isOwner, namaBarang) {
            if (isOwner) {
                alert("Peringatan: Anda tidak dapat menawar barang Anda sendiri!");
                return;
            }

            const nominal = document.getElementById('displayBid' + id).value;
            document.getElementById('confirmBidAmount').innerText = "Rp " + nominal;
            document.getElementById('confirmItemName').innerText = namaBarang;

            selectedFormId = 'form-bid-' + id;

            const modal = new bootstrap.Modal(document.getElementById('modalConfirmBid'));
            modal.show();
        }

        function submitBid() {
            if (selectedFormId) {
                document.getElementById(selectedFormId).submit();
            }
        }

        function showFullScreen(imgSrc) {
            document.getElementById('fullScreenImage').src = imgSrc;
            const modal = new bootstrap.Modal(document.getElementById('imageViewerModal'));
            modal.show();
        }

        function updateCountdowns() {
            document.querySelectorAll('.countdown-timer').forEach(timer => {
                const targetStr = timer.getAttribute('data-target');
                if (!targetStr) return;
                const targetDate = new Date(targetStr).getTime();
                const now = new Date().getTime();
                const distance = targetDate - now;

                if (distance < 0) { timer.innerHTML = "Berakhir"; return; }

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

    
    <div class="modal fade" id="modalConfirmBid" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold mb-0 text-primary">Konfirmasi Penawaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <h5 class="fw-bold fs-4 mb-3" id="confirmItemName">Nama Barang</h5>
                    <p class="text-muted mb-2">Apakah Anda yakin ingin menawar dengan harga:</p>
                    <h1 class="fw-bold text-success display-6 mb-4" id="confirmBidAmount">Rp 0</h1>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary fw-bold py-2" onclick="submitBid()">YA, KIRIM TAWARAN</button>
                        <button type="button" class="btn btn-outline-secondary fw-bold py-2" data-bs-dismiss="modal">BATAL</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\jason\Documents\Brian\CepatDapat_new\resources\views\auction\catalog.blade.php ENDPATH**/ ?>