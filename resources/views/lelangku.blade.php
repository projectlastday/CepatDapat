@extends('layouts.main')

@include('bootstrap.navbar')

@section('content')
    <style>
        body {
            background-color: #f8f9fa;
            padding-right: 0 !important; 
        }

        .lelang-card {
            border: none;
            border-radius: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #ffffff;
            position: relative;
            backface-visibility: hidden; 
            -webkit-font-smoothing: subpixel-antialiased;
        }

        .lelang-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
        }

        .card-img-container {
            height: 200px;
            border-radius: 16px 16px 0 0;
            overflow: hidden;
            background-color: #e9ecef;
        }

        .card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .price-tag {
            color: #dc3545;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .btn-action {
            border-radius: 10px;
            font-weight: 600;
        }
    </style>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Lelangku</h2>
        </div>

        {{-- Filter Status --}}
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="/lelangku?status=all" 
               class="btn btn-sm rounded-pill px-4 fw-bold {{ $current_filter == 'all' ? 'btn-primary shadow' : 'btn-outline-secondary' }}">
                All
            </a>

            @php 
                $statuses = ['pending', 'accepted', 'rejected', 'open', 'sold', 'canceled'];
            @endphp
            @foreach($statuses as $s)
                <a href="/lelangku?status={{ $s }}" 
                   class="btn btn-sm rounded-pill px-4 fw-bold {{ $current_filter == $s ? 'btn-primary shadow' : 'btn-outline-secondary' }}">
                    {{ ucfirst($s) }}
                </a>
            @endforeach
        </div>

        <div class="row g-4">
            @forelse($barang as $item)
                @php
                    $fotos = array_filter(explode(',', $item->foto));
                    $foto_utama = $fotos[0] ?? null;
                @endphp
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="card lelang-card shadow-sm h-100">
                        <div class="card-img-container">
                            @if($foto_utama)
                                <img src="{{ asset('img_barang/' . $foto_utama) }}" alt="{{ $item->nama_barang }}">
                            @else
                                <div class="h-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-image text-muted fs-1"></i>
                                </div>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold text-dark text-truncate mb-1">{{ $item->nama_barang }}</h5>
                            
                            {{-- ID DITAMBAHKAN UNTUK AUTO UPDATE HARGA AGAR SINKRON DENGAN LAYOUT --}}
                            <p class="price-tag mb-2">Rp <span id="card-price-{{ $item->id_lelang }}">{{ number_format($item->harga_awal, 0, ',', '.') }}</span></p>

                            <p class="text-muted small mb-4 flex-grow-1">
                                {{ Str::limit($item->deskripsi, 50) }}
                            </p>
                            
                            <div class="mt-auto">
                                @if($item->status == 'accepted')
                                    <button type="button" class="btn btn-success btn-action w-100 py-2 shadow-sm" 
                                            data-bs-toggle="modal" data-bs-target="#modalMulai{{ $item->id_lelang }}">
                                        Mulai Lelang
                                    </button>
                                @elseif($item->status == 'rejected')
                                    <div class="alert alert-danger p-2 mb-0 small text-center rounded-3 fw-bold">Ditolak Moderator</div>
                                @elseif($item->status == 'open')
                                    <div class="badge bg-primary w-100 py-2 mb-2 shadow-sm">Sedang Berlangsung</div>
                                    <div class="text-center">
                                        {{-- ID TIMER TETAP ADA UNTUK SCRIPT COUNTDOWN LOKAL --}}
                                        <small class="text-danger fw-bold countdown-timer" 
                                               id="timer-{{ $item->id_lelang }}" 
                                               data-target="{{ $item->tgl_akhir }}" 
                                               style="font-size: 0.75rem;">
                                            Memuat Countdown...
                                        </small>
                                    </div>
                                @else
                                    <div class="badge bg-light text-dark border w-100 py-2 fw-bold">{{ ucfirst($item->status) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- MODAL MULAI LELANG --}}
                    @if($item->status == 'accepted')
                    <div class="modal fade" id="modalMulai{{ $item->id_lelang }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold mb-0">Atur Durasi Lelang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="/lelangku/mulai" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_lelang" value="{{ $item->id_lelang }}">
                                    <div class="modal-body text-center p-4">
                                        <div class="alert alert-info border-0 rounded-4 mb-4" style="background-color: #e7f3ff;">
                                            <i class="bi bi-info-circle-fill text-primary mb-2 d-block fs-4"></i>
                                            <p class="small text-dark mb-0">
                                                <strong>Informasi:</strong> Username dan Kelas Anda akan ditampilkan sebagai identitas pelelang pada halaman Katalog setelah lelang dimulai.
                                            </p>
                                        </div>

                                        <p class="text-muted mb-4">Pilih berapa hari lelang ini akan berlangsung:</p>
                                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                                            @for($i = 1; $i <= 7; $i++)
                                                <input type="radio" class="btn-check" name="durasi" id="day{{ $item->id_lelang }}{{ $i }}" value="{{ $i }}" {{ $i == 7 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success btn-lg rounded-4 px-4 py-3 fw-bold" for="day{{ $item->id_lelang }}{{ $i }}">
                                                    {{ $i }} <br> <span class="small" style="font-size: 0.6em;">Hari</span>
                                                </label>
                                            @endfor
                                        </div>
                                        <div class="mt-4">
                                            <p class="text-muted small mb-0">Lelang akan berakhir pukul <strong>24:00</strong> di hari terakhir.</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0">
                                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold rounded-4 shadow">Mulai Lelang</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="py-5">
                        <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
                        <h5 class="mt-3 text-muted">Tidak ada barang dengan status "{{ ucfirst($current_filter) }}"</h5>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        // SCRIPT COUNTDOWN (Berjalan lokal di halaman ini agar presisi per detik)
        function updateCountdowns() {
            const timers = document.querySelectorAll('.countdown-timer');
            timers.forEach(timer => {
                const targetDate = new Date(timer.getAttribute('data-target')).getTime();
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

                let display = "Berakhir: ";
                if (days > 0) display += days + "h ";
                display += hours + "j " + minutes + "m " + seconds + "s WIB";
                timer.innerHTML = display;
            });
        }
        setInterval(updateCountdowns, 1000);
        document.addEventListener('DOMContentLoaded', updateCountdowns);
    </script>
@endsection