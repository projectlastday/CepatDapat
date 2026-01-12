<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="/">
            <span class="text-danger">Cepat</span><span class="text-primary">Dapat</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('home') ? 'active fw-bold' : '' }}" href="/home">Home</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link fw-bold {{ request()->is('katalog') ? 'active' : '' }}" 
                       href="/katalog" style="color: #dc3545 !important;">Katalog</a>
                </li>
                
                @if(session('id_user'))
                    <li class="nav-item">
                        <a class="nav-link fw-bold {{ request()->is('pasang_lelang') ? 'active' : '' }}" 
                           href="/pasang_lelang" style="color: #0d6efd !important;">Pasang Lelang</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('lelangku') ? 'active' : '' }}" href="/lelangku">Lelangku</a>
                    </li>
                    
                    {{-- Menu Moderasi Khusus Admin/Manager --}}
                    @if(session('id_user_type') == 1 || session('id_user_type') == 2)
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-bold {{ request()->is('moderasi') ? 'active' : '' }}" href="/moderasi">Moderasi</a>
                        </li>
                    @endif
                @endif
            </ul>

            <div class="d-flex align-items-center gap-3">
                @if(session('id_user'))
                    <div class="navbar-text d-none d-lg-inline small">
                        Halo, <span class="fw-bold text-dark">{{ session('username') }}</span>
                    </div>
                    <a href="/logout" class="btn btn-outline-danger btn-sm rounded-pill px-4 shadow-sm fw-bold">Logout</a>
                @else
                    <a href="/login" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">Login</a>
                @endif
            </div>
        </div>
    </div>
</nav>