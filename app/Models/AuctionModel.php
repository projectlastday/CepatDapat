<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MainModel extends Model
{
    /**
     * Fungsi login standar
     */
    public function login($table, $where)
    {
        return DB::table($table)->where($where)->first();
    }

    /**
     * Fungsi insert data ke tabel tertentu
     */
    public function simpan($tabel, $data)
    {
        return DB::table($tabel)->insert($data);
    }

    /**
     * Fungsi ambil data dengan kondisi where
     */
    public function tampilWhere($table, $where)
    {
        return DB::table($table)->where($where)->get();
    }

    /**
     * Fungsi update data (digunakan untuk Setujui, Tolak, Suspend, dan Buka Lelang)
     */
    public function edit($tabel, $where, $data)
    {
        return DB::table($tabel)->where($where)->update($data);
    }

    /**
     * FUNGSI BARU: Mengambil data lelang milik user tertentu dengan filter status
     * Digunakan untuk halaman Lelangku
     */
    public function getLelangByUser($id_user, $status)
    {
        // Query dasar berdasarkan id_user milik pelelang
        $query = DB::table('lelang')->where('id_user', $id_user);

        // Jika filter yang dipilih bukan 'all', tambahkan filter status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Mengurutkan berdasarkan tanggal input terbaru
        return $query->orderBy('tgl_input', 'desc')->get();
    }
}
