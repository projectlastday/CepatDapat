@extends('layouts.main')

@include('bootstrap.navbar')

@section('content')
    <style>
        body { background-color: #f8f9fa; padding-right: 0 !important; }
        
        .moderasi-card {
            border: none; border-radius: 16px; transition: transform 0.3s ease;
            background-color: #ffffff; position: relative;
        }
        .moderasi-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
        }
        .card-img-container {
            height: 200px; border-radius: 16px 16px 0 0; overflow: hidden;
            background-color: #e9ecef; cursor: zoom-in;
        }
        .card-img-container img { width: 100%; height: 100%; object-fit: cover; }
        .price-tag { color: #dc3545; font-size: 1.2rem; font-weight: 800; }
        .timer-display { color: #dc3545; font-weight: 700; font-size: 1.1rem; }

        .modal-content-premium { border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .bid-container { border: 2px solid #e9ecef; border-radius: 12px; padding: 5px 15px; background: #fff; }
        .bid-input-display { font-size: 1.6rem; font-weight: 800; text-align: center; border: none; width: 100%; outline: none; color: #212529; }
        .btn-circle-fixed { width: 45px !important; height: 45px !important; border-radius: 50% !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0; font-size: 1.5rem; font-weight: bold; padding: 0; }
        .img-thumbnail-custom { height: 60px; object-fit: cover; cursor: zoom-in; border-radius: 10px; border: 1px solid #ddd; }
    </style>

    <div class="container py-5 text-start">
        <h2 class="fw-bold text-dark mb-5">Katalog Lelang Sedang Berlangsung</h2>

        <div class="row g-4">
            @forelse($barang as $item)
                @php
                    $fotos = array_filter(explode(',', $item->foto));
                    $foto_utama = $fotos[0] ?? null;
                    $jml_tambahan = count($fotos) - 1;
                    $col_class = $jml_tambahan == 1 ? 'col-12' : ($jml_tambahan == 2 ? 'col-6' : 'col-4');
                    $is_owner = (session('id_user') == $item->id_user); 
                @endphp

                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="card moderasi-card shadow-sm h-100">
                        <div class="card-img-container" onclick="showFullScreen('{{ asset('img_barang/' . $foto_utama) }}')">
                            <img src="{{ asset('img_barang/' . ($foto_utama ?: 'no-image.png')) }}">
                        </div>
                        <div class="card-body d-flex flex-column text-start">
                            <h5 class="fw-bold text-dark text-truncate mb-1">{{ $item->nama_barang }}</h5>
                            
                            {{-- ID Harga untuk Sinkronisasi Global --}}
                            <p class="price-tag mb-2">Rp <span id="card-price-{{ $item->id_lelang }}">{{ number_format($item->harga_awal, 0, ',', '.') }}</span></p>
                            
                            <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($item->deskripsi, 60) }}</p>
                            
                            {{-- Countdown Timer --}}
                            <div class="timer-display mb-3 countdown-timer" data-target="{{ $item->tgl_akhir }}">Memuat...</div>
                            
                            <button class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTawar{{ $item->id_lelang }}">TAWAR</button>
                        </div>
                    </div>

                    <div class="modal fade" id="modalTawar{{ $item->id_lelang }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content modal-content-premium border-0 shadow-lg">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold mt-2 ms-2"><i class="bi bi-gavel me-2 text-primary"></i>Detail Penawaran Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 text-start">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="rounded-4 overflow-hidden mb-3 shadow-sm" style="height: 250px; background: #f8f9fa; cursor: zoom-in;" onclick="showFullScreen('{{ asset('img_barang/' . $foto_utama) }}')">
                                                <img src="{{ asset('img_barang/' . $foto_utama) }}" class="w-100 h-100" style="object-fit: contain;">
                                            </div>
                                            <div class="row g-2">
                                                @foreach($fotos as $key => $f)
                                                    @if($key > 0)
                                                        <div class="{{ $col_class }}">
                                                            <img src="{{ asset('img_barang/' . $f) }}" class="w-100 img-thumbnail-custom border shadow-sm" onclick="showFullScreen('{{ asset('img_barang/' . $f) }}')">
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex flex-column">
                                            <div class="mb-3">
                                                <label class="text-muted small fw-bold text-uppercase">Nama Barang</label>
                                                <h3 class="fw-bold text-dark">{{ $item->nama_barang }}</h3>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6 border-end">
                                                    <label class="text-muted small fw-bold text-uppercase">Pelelang</label>
                                                    <p class="mb-0 text-primary fw-bold small"><i class="bi bi-person-circle me-1"></i>{{ $item->username }}</p>
                                                </div>
                                                <div class="col-6 ps-3">
                                                    <label class="text-muted small fw-bold text-uppercase">Penawar Tertinggi</label>
                                                    {{-- ID Penawar untuk Sinkronisasi Global --}}
                                                    <p class="mb-0 text-success fw-bold small" id="bidder-info-{{ $item->id_lelang }}">
                                                        @if(isset($item->bidder_username))
                                                            <i class="bi bi-trophy me-1"></i>{{ $item->bidder_username }} ({{ $item->bidder_kelas }})
                                                        @else
                                                            Belum ada penawar
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6 border-end">
                                                    <label class="text-muted small fw-bold text-uppercase">Sisa Waktu</label>
                                                    <p class="mb-0 timer-display countdown-timer" data-target="{{ $item->tgl_akhir }}">Memuat...</p>
                                                </div>
                                                <div class="col-6 ps-3 text-start">
                                                    <label class="text-muted small fw-bold text-uppercase">Harga Sekarang</label>
                                                    {{-- ID Harga Modal untuk Sinkronisasi Global --}}
                                                    <p class="mb-0 text-danger fw-bold">Rp <span id="modal-price-{{ $item->id_lelang }}">{{ number_format($item->harga_awal, 0, ',', '.') }}</span></p>
                                                </div>
                                            </div>

                                            <div class="mb-4 flex-grow-1">
                                                <label class="text-muted small fw-bold text-uppercase">Deskripsi</label>
                                                <div class="p-3 bg-light rounded-3 shadow-sm" style="font-size: 0.85rem; min-height: 100px; max-height: 200px; overflow-y: auto; word-break: break-all; overflow-wrap: break-word;">
                                                    {{ $item->deskripsi ?: 'Tidak ada deskripsi barang.' }}
                                                </div>
                                            </div>

                                            <div class="pt-3 border-top text-center mt-auto">
                                                <form action="/katalog/tawar" method="POST" onsubmit="return confirmBid('{{ $item->id_lelang }}', {{ $is_owner ? 'true' : 'false' }})">
                                                    @csrf
                                                    <input type="hidden" name="id_lelang" value="{{ $item->id_lelang }}">
                                                    
                                                    <h6 class="fw-bold mb-3 text-start">Tentukan Nominal Tawaran</h6>
                                                    <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
                                                        <button type="button" class="btn btn-outline-danger btn-circle-fixed shadow-sm" onclick="adjustBid('{{ $item->id_lelang }}', -1000)"><span>−</span></button>
                                                        <div class="bid-container flex-grow-1 shadow-sm">
                                                            <input type="text" id="displayBid{{ $item->id_lelang }}" class="bid-input-display" value="{{ number_format($item->harga_awal, 0, ',', '.') }}" readonly>
                                                            <input type="hidden" id="inputBid{{ $item->id_lelang }}" name="nominal_tawaran" value="{{ $item->harga_awal }}" data-min="{{ $item->harga_awal }}">
                                                        </div>
                                                        <button type="button" class="btn btn-outline-success btn-circle-fixed shadow-sm" onclick="adjustBid('{{ $item->id_lelang }}', 1000)"><span>+</span></button>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-3 py-2 shadow-sm">KIRIM TAWARAN</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5"><h5 class="text-muted">Belum ada lelang yang aktif saat ini.</h5></div>
            @endforelse
        </div>
    </div>

    {{-- Viewer Gambar --}}
    <div class="modal fade" id="imageViewerModal" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.9); z-index: 3000;">
        <div class="modal-dialog modal-dialog-centered modal-xl"><div class="modal-content bg-transparent border-0"><div class="modal-body p-0 text-center"><button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" data-bs-dismiss="modal"></button><img src="" id="fullScreenImage" class="img-fluid rounded-4 shadow-lg" style="max-height: 90vh;"></div></div></div>
    </div>

    <script>
        // Gunakan fungsi format dari Layout Main (formatIDR) atau buat lokal jika perlu
        function formatRupiah(angka) { return new Intl.NumberFormat('id-ID').format(angka); }
        
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

        function confirmBid(id, isOwner) {
            if (isOwner) {
                alert("Peringatan: Anda tidak dapat melakukan penawaran pada barang yang Anda lelang sendiri!");
                return false;
            }
            const nominal = document.getElementById('displayBid' + id).value;
            return confirm("Apakah Anda yakin ingin melakukan penawaran sebesar Rp " + nominal + "?");
        }

        function showFullScreen(imgSrc) {
            document.getElementById('fullScreenImage').src = imgSrc;
            new bootstrap.Modal(document.getElementById('imageViewerModal')).show();
        }

        // --- HANYA FUNGSI COUNTDOWN (Fungsi update data ditarik ke layouts/main.blade.php) ---
        function updateCountdowns() {
            document.querySelectorAll('.countdown-timer').forEach(timer => {
                const targetDate = new Date(timer.getAttribute('data-target')).getTime();
                const now = new Date().getTime();
                const distance = targetDate - now;

                if (distance < 0) { 
                    timer.innerHTML = "Berakhir"; 
                    return; 
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Menambahkan label WIB sesuai instruksi sebelumnya
                let display = "";
                if (days > 0) display += days + "h ";
                display += hours + "j " + minutes + "m " + seconds + "s WIB";
                
                timer.innerHTML = display;
            });
        }
        
        setInterval(updateCountdowns, 1000);
        document.addEventListener('DOMContentLoaded', updateCountdowns);
    </script>
@endsection