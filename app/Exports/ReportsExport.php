<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Barang',
            'Pemilik',
            'Pemenang',
            'Harga Awal',
            'Harga Akhir',
            'Status',
        ];
    }

    public function collection()
    {
        return DB::table('lelang')
            ->join('users', 'lelang.id_user', '=', 'users.id_user')
            ->leftJoin('penawaran', function ($join) {
                $join->on('lelang.id_lelang', '=', 'penawaran.id_lelang')
                    ->whereRaw('penawaran.penawaran_harga = (SELECT MAX(p2.penawaran_harga) FROM penawaran p2 WHERE p2.id_lelang = lelang.id_lelang)');
            })
            ->leftJoin('users as buyer', 'penawaran.id_user', '=', 'buyer.id_user')
            ->whereIn('lelang.status', $this->filters['statuses'])
            ->where('lelang.status_delete', 0)
            ->whereBetween('lelang.tgl_input', [$this->filters['start_date'], $this->filters['end_date'] . ' 23:59:59'])
            ->select(
                'lelang.tgl_input',
                'lelang.nama_barang',
                'users.username as pemilik',
                'buyer.username as pemenang',
                'lelang.harga_awal',
                DB::raw('COALESCE(penawaran.penawaran_harga, 0) as harga_akhir'),
                'lelang.status'
            )
            ->orderBy('lelang.tgl_input', 'desc')
            ->get();
    }

    public function map($row): array
    {
        return [
            Carbon::parse($row->tgl_input)->format('d/m/Y H:i'),
            $row->nama_barang,
            $row->pemilik,
            $row->pemenang ?? '-',
            number_format($row->harga_awal, 0, ',', '.'),
            number_format($row->harga_akhir, 0, ',', '.'),
            ucfirst($row->status),
        ];
    }
}
