@extends('layouts.main')

@include('bootstrap.navbar')

@section('content')
    <style>
        /* Kontras antara background dan card */
        body {
            background-color: #f0f2f5;
        }

        /* .main-card  */
        .main-card {
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Identitas warna CepatDapat */
        .text-cepat {
            color: #dc3545;
        }

        .text-dapat {
            color: #0d6efd;
        }
    </style>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">

                {{-- Bagian Header Selamat Datang --}}
                @if(!session('id_user'))
                    <div class="text-center mb-5">
                        <h1 class="display-4 fw-bold text-danger">Anda belum login</h1>
                        <p class="text-muted">Silakan login terlebih dahulu untuk mulai menawar atau mengunggah barang lelang.
                        </p>
                        <a href="/login" class="btn btn-primary btn-lg px-5 mt-3 rounded-pill shadow-sm">Login Sekarang</a>
                    </div>
                @else
                    <div class="text-center mb-5">
                        <h1 class="display-4 fw-bold text-dark">Selamat Datang, {{ session('username') }}!</h1>
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="/katalog" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm">Lihat Katalog</a>
                            <a href="/pasang_lelang" class="btn btn-outline-danger btn-lg px-4 rounded-pill">Mulai Lelang</a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection