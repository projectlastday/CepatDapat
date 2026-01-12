@extends('layouts.main')

@include('bootstrap.navbar')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
            padding-right: 0 !important;
        }

        .main-card {
            border: none;
            border-radius: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        /* Textfield Abu-abu Tanpa Animasi */
        .form-control-grey {
            border: none !important;
            border-radius: 12px !important;
            padding: 14px 18px !important;
            background-color: #f1f3f5 !important;
            font-size: 1rem;
            color: #212529;
            transition: none !important;
        }

        .form-control-grey:focus {
            background-color: #e9ecef !important;
            outline: none;
            box-shadow: none !important;
        }

        .form-section-title {
            font-size: 1.1rem;
            border-left: 4px solid #dc3545;
            padding-left: 10px;
            margin-bottom: 25px;
        }

        /* Box Upload Foto Produk */
        .image-upload-box {
            border: 2px dashed #cbd5e0;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .image-upload-box:hover {
            border-color: #0d6efd;
            background: #f1f3f5;
        }

        .file-input-hidden {
            display: none;
        }

        .preview-img {
            max-width: 100%;
            max-height: 140px;
            border-radius: 10px;
            display: none;
            object-fit: cover;
        }

        .btn-remove-img {
            position: absolute;
            top: 10px;
            right: 10px;
            display: none;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 14px;
            z-index: 10;
        }

        /* Info Boxes */
        .status-info {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .warning-suspend {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
            padding: 15px;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        @media (max-width: 576px) {
            .form-control-grey {
                font-size: 0.9rem !important;
                padding: 12px !important;
            }

            .main-card {
                padding: 20px !important;
            }
        }
    </style>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-11 text-start">
                <form action="/pasang_lelang" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card main-card">
                        <div class="card-body p-4 p-md-5">
                            <div class="row g-5">

                                <div class="col-lg-7">
                                    <h5 class="form-section-title fw-bold text-dark text-uppercase">Detail Barang</h5>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark small text-uppercase">Nama Barang</label>
                                        <input type="text" name="nama_barang" class="form-control form-control-grey"
                                            placeholder="Apa nama barang yang ingin Anda lelang?" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark small text-uppercase">Harga Awal</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-0"
                                                style="background-color: #e9ecef; border-radius: 12px 0 0 12px; font-weight: bold;">Rp</span>
                                            <input type="number" class="form-control form-control-grey" name="harga_awal"
                                                style="border-radius: 0 12px 12px 0 !important;"
                                                placeholder="Tentukan harga mulai lelang" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark small text-uppercase">Deskripsi
                                            Barang</label>
                                        {{-- Ukuran rows dipendekan menjadi 5 --}}
                                        <textarea class="form-control form-control-grey" name="deskripsi" rows="2"
                                            placeholder="Jelaskan kondisi dan spesifikasi barang Anda di sini..."></textarea>
                                    </div>

                                    <div class="d-none d-lg-block">
                                        <div class="status-info d-flex align-items-start mb-3 shadow-sm">
                                            <i class="bi bi-shield-check fs-5 me-2"></i>
                                            <div>Barang Anda akan ditinjau terlebih dahulu sebelum tampil secara publik.
                                            </div>
                                        </div>
                                        <div class="warning-suspend d-flex align-items-start shadow-sm">
                                            <i class="bi bi-exclamation-octagon fs-5 me-2"></i>
                                            <div>Penyalahgunaan fitur lelang akan berakibat pada <strong>Suspend
                                                    Akun</strong>.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <h5 class="form-section-title fw-bold text-dark text-uppercase">Foto Produk</h5>
                                    <p class="text-muted small mb-4">Pilih foto terbaik max 3 gambar size 5mb per foto.</p>

                                    <div class="row g-3 mb-4">
                                        @for ($i = 1; $i <= 3; $i++)
                                            <div class="{{ $i == 1 ? 'col-12' : 'col-6' }}">
                                                <div class="image-upload-wrapper position-relative">
                                                    <button type="button" class="btn-remove-img shadow" id="remove-{{ $i }}"
                                                        onclick="removeImage({{ $i }})">
                                                        <i class="bi bi-x"></i>
                                                    </button>

                                                    <label class="image-upload-box shadow-sm" id="box-{{ $i }}"
                                                        for="input-{{ $i }}">
                                                        <img id="preview-{{ $i }}" class="preview-img">
                                                        <div class="icon-label" id="label-{{ $i }}">
                                                            <i class="bi bi-image text-muted"
                                                                style="font-size: {{ $i == 1 ? '3rem' : '2rem' }};"></i>
                                                            <span class="d-block mt-2 fw-bold small text-uppercase">
                                                                {{ $i == 1 ? 'Foto Utama' : 'Optional' }}
                                                            </span>
                                                        </div>
                                                    </label>

                                                    <input type="file" name="foto[]" id="input-{{ $i }}"
                                                        class="file-input-hidden" accept="image/*"
                                                        onchange="previewImage(this, {{ $i }})" {{ $i == 1 ? 'required' : '' }}>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold rounded-3 shadow">
                                            DAFTARKAN LELANG
                                        </button>
                                        <a href="/home"
                                            class="btn btn-link text-muted text-decoration-none text-center">Batal</a>
                                    </div>

                                    <div class="d-lg-none mt-4">
                                        <div class="status-info d-flex align-items-start mb-3 shadow-sm">
                                            <i class="bi bi-shield-check fs-5 me-2"></i>
                                            <div>Barang Anda akan ditinjau terlebih dahulu sebelum tampil secara publik.
                                            </div>
                                        </div>
                                        <div class="warning-suspend d-flex align-items-start mb-0 shadow-sm">
                                            <i class="bi bi-exclamation-octagon fs-5 me-2"></i>
                                            <div>Penyalahgunaan fitur lelang akan berakibat pada <strong>Suspend
                                                    Akun</strong>.</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input, id) {
            const preview = document.getElementById('preview-' + id);
            const label = document.getElementById('label-' + id);
            const removeBtn = document.getElementById('remove-' + id);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    label.style.display = 'none';
                    removeBtn.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        function removeImage(id) {
            const input = document.getElementById('input-' + id);
            const preview = document.getElementById('preview-' + id);
            const label = document.getElementById('label-' + id);
            const removeBtn = document.getElementById('remove-' + id);
            input.value = "";
            preview.src = "";
            preview.style.display = 'none';
            label.style.display = 'block';
            removeBtn.style.display = 'none';
        }
    </script>
@endsection