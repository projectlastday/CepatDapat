@extends('layouts.main')

@include('bootstrap.navbar')

@section('content')
    <style>
        body {
            background-color: #f8f9fa;
        }

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
            background-color: #e9ecef;
        }

        .card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .price-tag {
            color: #dc3545;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .btn-review {
            border-radius: 10px;
            font-weight: 600;
        }

        .badge-review {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            font-weight: 600;
        }
    </style>

    <div class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-md-6 text-center text-md-start">
                <h2 class="fw-bold text-dark mb-0">Antrian Moderasi</h2>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span class="badge badge-review rounded-pill px-4 py-2 shadow-sm">
                    <i class="bi bi-hourglass-split me-2"></i>{{ $barang->count() }} Barang Menunggu Review
                </span>
            </div>
        </div>

        <div class="row g-4">
            @forelse($barang as $item)
                @php
                    $fotos = explode(',', $item->foto);
                    $foto_utama = $fotos[0];
                @endphp
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="card moderasi-card shadow-sm h-100">
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
                            <p class="price-tag mb-3">Rp {{ number_format($item->harga_awal, 0, ',', '.') }}</p>

                            <p class="text-muted small mb-4 flex-grow-1">
                                {{ Str::limit($item->deskripsi, 60) }}
                            </p>

                            <div class="d-grid mt-auto">
                                <button type="button" class="btn btn-outline-primary btn-review py-2" data-bs-toggle="modal"
                                    data-bs-target="#modalReview{{ $item->id_lelang }}">
                                    <i class="bi bi-search me-2"></i>Review
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GABUNGAN MODAL REVIEW --}}
                <div class="modal fade" id="modalReview{{ $item->id_lelang }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="fw-bold mt-2 ms-2"><i class="bi bi-eye me-2 text-primary"></i>Detail Review Barang</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            
                            <div class="modal-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        @php
                                            $fotos_modal = array_filter(explode(',', $item->foto));
                                            $foto_utama_modal = $fotos_modal[0] ?? null;

                                            $jml_foto_tambahan = count($fotos_modal) - 1;
                                            $col_class = 'col-4'; 
                                            
                                            if ($jml_foto_tambahan == 1) {
                                                $col_class = 'col-12';
                                            } elseif ($jml_foto_tambahan == 2) {
                                                $col_class = 'col-6';
                                            }
                                        @endphp

                                        <div class="image-main-preview rounded-4 overflow-hidden mb-3 shadow-sm" style="height: 300px; background: #f8f9fa; cursor: zoom-in;" 
                                             onclick="showFullScreen('{{ asset('img_barang/' . $foto_utama_modal) }}')">
                                            @if($foto_utama_modal)
                                                <img src="{{ asset('img_barang/' . $foto_utama_modal) }}" class="w-100 h-100" style="object-fit: contain;">
                                            @else
                                                <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                                                    <i class="bi bi-image fs-1"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="row g-2">
                                            @foreach($fotos_modal as $key => $f)
                                                @if($key > 0)
                                                    <div class="{{ $col_class }}">
                                                        <div class="rounded-3 overflow-hidden border shadow-sm" style="height: 80px; cursor: zoom-in;" 
                                                             onclick="showFullScreen('{{ asset('img_barang/' . $f) }}')">
                                                            <img src="{{ asset('img_barang/' . $f) }}" class="w-100 h-100" style="object-fit: cover;">
                                                        </div>
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
                                            <div class="col-6">
                                                <label class="text-muted small fw-bold text-uppercase">Pengunggah</label>
                                                <p class="mb-0 text-primary fw-bold">
                                                    <i class="bi bi-person-circle me-1"></i>{{ $item->username }}
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <label class="text-muted small fw-bold text-uppercase">Waktu Input</label>
                                                <p class="mb-0 text-muted small">
                                                    <i class="bi bi-calendar3 me-1"></i>{{ date('d M Y', strtotime($item->tgl_input)) }}
                                                </p>
                                                <p class="mb-0 text-muted small">
                                                    <i class="bi bi-clock me-1"></i>Pukul {{ date('H:i', strtotime($item->tgl_input)) }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mb-4 flex-grow-1">
                                            <label class="text-muted small fw-bold text-uppercase">Deskripsi</label>
                                            <div class="p-3 bg-light rounded-3 shadow-sm" 
                                                 style="font-size: 0.85rem; min-height: 100px; max-height: 200px; overflow-y: auto; word-break: break-all; overflow-wrap: break-word;">
                                                {{ $item->deskripsi ?: 'Tidak ada deskripsi barang.' }}
                                            </div>
                                        </div>

                                        <div class="row g-2 pt-3 border-top mb-2">
                                            <div class="col-6">
                                                <form action="/moderasi/aksi" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id_lelang" value="{{ $item->id_lelang }}">
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-outline-danger w-100 fw-bold py-2 rounded-3">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="col-6">
                                                <form action="/moderasi/aksi" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id_lelang" value="{{ $item->id_lelang }}">
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" class="btn btn-success w-100 fw-bold py-2 rounded-3 shadow-sm">
                                                        Accept
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <form action="/moderasi/suspend" method="POST">
                                            @csrf
                                            <input type="hidden" name="id_user" value="{{ $item->id_user }}">
                                            <input type="hidden" name="id_lelang" value="{{ $item->id_lelang }}">
                                            <button type="submit" class="btn btn-dark w-100 fw-bold py-2 rounded-3" 
                                                    onclick="return confirm('PERINGATAN: Akun {{ $item->username }} akan disuspend. Lanjutkan?')">
                                                <i class="bi bi-slash-circle me-2"></i>Suspend Penjual
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="py-5">
                        <i class="bi bi-check2-all display-1 text-success opacity-50"></i>
                        <h3 class="mt-4 fw-bold text-dark">Antrian Kosong</h3>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="imageViewerModal" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.8);">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 text-center">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    <img src="" id="fullScreenImage" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showFullScreen(imgSrc) {
            document.getElementById('fullScreenImage').src = imgSrc;
            var myModal = new bootstrap.Modal(document.getElementById('imageViewerModal'));
            myModal.show();
        }
    </script>
@endsection