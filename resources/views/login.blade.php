@extends('layouts.main')

@section('content')
<style>
    /* Memberikan kontras agar card putih terlihat jelas */
    body {
        background-color: #f0f2f5; 
    }
    .card {
        border-radius: 12px;
        border: none;
    }
    /* Style tambahan untuk pesan error agar terlihat menyatu */
    .alert-danger-custom {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        font-size: 0.9rem;
        border: 1px solid #f5c6cb;
    }
</style>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-5" style="width: 400px;">
        <h2 class="text-center mb-4 fw-bold text-dark">Login</h2>

        @if ($errors->has('username') || $errors->has('password') || session('error'))
            <div class="alert-danger-custom">
                Username atau password salah.
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-danger py-2 fw-bold">Masuk Sekarang</button>
            </div>
        </form>

        <div class="text-center mt-4">
            <a href="/" class="text-decoration-none text-muted small">Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection