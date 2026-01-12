@extends('layouts.main')

@include('bootstrap.navbar')

@section('content')
    <style>
        body {
            background-color: #f0f2f5;
        }

        .main-card {
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .text-cepat {
            color: #dc3545;
        }

        .text-dapat {
            color: #0d6efd;
        }
    </style>

    <div class="text-center mt-5">
        <a href="/home" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Masuk ke Halaman
            Utama</a>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="main-card p-5 mb-5">
                    <h2 class="fw-bold mb-4 text-dark">
                        Mengenai <span class="text-cepat">Cepat</span><span class="text-dapat">Dapat</span>
                    </h2>

                    <p class="lead text-secondary">
                        <strong>CepatDapat</strong> adalah platform lelang digital eksklusif yang dirancang khusus sebagai
                        wadah kolaborasi dan ekonomi kreatif bagi seluruh siswa <strong>Permata Harapan</strong>. Platform
                        ini lahir dari inisiatif kemandirian siswa untuk menciptakan ekosistem pertukaran barang yang aman,
                        transparan, dan
                        terintegrasi di lingkungan sekolah.
                    </p>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5 class="fw-bold">Aktivitas Pengguna</h5>
                            <ul class="text-secondary">
                                <li><strong>Lelang Koleksi Pribadi:</strong> Memfasilitasi pengalihan kepemilikan barang
                                    seperti buku atau koleksi hobi kepada sesama siswa yang lebih membutuhkan.</li>
                                <li><strong>Optimasi Atribut Sekolah:</strong> Mempermudah akses perlengkapan belajar dengan
                                    nilai perolehan yang lebih ekonomis bagi seluruh murid.</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold">Keunggulan Platform</h5>
                            <ul class="text-secondary">
                                <li><strong>Inisiatif Mandiri:</strong> Tata kelola platform dijalankan secara mandiri untuk
                                    mengasah keterampilan berwirausaha antar siswa.</li>
                                <li><strong>Komunitas Terpercaya:</strong> Protokol transaksi yang terjaga karena hanya
                                    dapat diakses oleh civitas siswa <strong>Permata Harapan</strong>.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-light rounded-3 border-start border-danger border-4">
                        <p class="mb-0 fs-5 text-dark">
                            <strong>CepatDapat</strong> menjembatani kebutuhan antar siswa melalui sistem pemanfaatan aset
                            yang terpadu, memungkinkan barang yang tidak lagi digunakan menjadi solusi bernilai bagi siswa
                            <strong>Permata Harapan</strong> lainnya.
                        </p>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection