<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Lelang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-bottom: 20px;
        }

        .filter-info {
            background: #f5f5f5;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #343a40;
            color: #fff;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #999;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            color: #fff;
            font-size: 10px;
        }

        .badge-pending {
            background: #ffc107;
            color: #333;
        }

        .badge-accepted {
            background: #0dcaf0;
            color: #333;
        }

        .badge-rejected {
            background: #6c757d;
        }

        .badge-open {
            background: #0d6efd;
        }

        .badge-sold {
            background: #198754;
        }

        .badge-unsold {
            background: #212529;
        }

        .badge-canceled {
            background: #dc3545;
        }
    </style>
</head>

<body>
    <h1>Laporan Lelang — CepatDapat</h1>
    <div class="subtitle">Dicetak pada {{ now()->format('d/m/Y H:i') }}</div>

    <div class="filter-info">
        <strong>Periode:</strong> {{ $filters['start_date'] }} s/d {{ $filters['end_date'] }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Status:</strong> {{ implode(', ', array_map('ucfirst', $filters['statuses'])) }}
    </div>

    @if ($rows->isEmpty())
        <p style="text-align:center; color:#999; margin-top:40px;">Tidak ada data untuk filter yang dipilih.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Pemilik</th>
                    <th>Pemenang</th>
                    <th class="text-right">Harga Awal</th>
                    <th class="text-right">Harga Akhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->tgl_input)->format('d/m/Y') }}</td>
                        <td>{{ $row->nama_barang }}</td>
                        <td>{{ $row->pemilik }}</td>
                        <td>{{ $row->pemenang ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($row->harga_awal, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($row->harga_akhir, 0, ',', '.') }}</td>
                        <td><span class="badge badge-{{ $row->status }}">{{ ucfirst($row->status) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Total: {{ $rows->count() }} data &nbsp;|&nbsp; Laporan CepatDapat
        </div>
    @endif
</body>

</html>