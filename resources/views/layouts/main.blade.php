<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    {{-- Memastikan Bootstrap Icons tersedia untuk ikon trofi --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>


<body>

    @yield('content')

    {{-- Script Eksternal --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    {{-- Script Logika Auto Reload Data (AJAX Polling) --}}
    <script>
        // Fungsi untuk memformat angka ke Rupiah
        function formatIDR(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        async function refreshDataOnly() {
            try {
                // Mengambil data dari route API yang sudah dibuat
                const response = await fetch('/api/katalog-updates');
                const data = await response.json();

                data.forEach(item => {
                    // 1. Update Harga di Katalog (Card)
                    const cardPrice = document.getElementById(`card-price-${item.id_lelang}`);
                    if (cardPrice) cardPrice.innerText = formatIDR(item.harga_awal);

                    // 2. Update Harga di Pop-up Tawar
                    const modalPrice = document.getElementById(`modal-price-${item.id_lelang}`);
                    if (modalPrice) modalPrice.innerText = formatIDR(item.harga_awal);

                    // 3. Update Nama Penawar Tertinggi di Pop-up
                    const bidderInfo = document.getElementById(`bidder-info-${item.id_lelang}`);
                    if (bidderInfo) {
                        bidderInfo.innerHTML = item.bidder_username
                            ? `<i class="bi bi-trophy me-1"></i>${item.bidder_username} (${item.bidder_kelas})`
                            : 'Belum ada penawar';
                    }

                    // 4. Update Batas Minimal Tawaran (agar validasi tetap akurat)
                    const hiddenInput = document.getElementById(`inputBid${item.id_lelang}`);
                    if (hiddenInput) {
                        hiddenInput.setAttribute('data-min', item.harga_awal);
                    }
                });
            } catch (e) { 
                console.log("Koneksi sibuk, mencoba sinkronisasi ulang..."); 
            }
        }

        // Jalankan setiap 5 detik (Sangat hemat data dibanding full reload)
        setInterval(refreshDataOnly, 5000);
    </script>

</body>
</html>