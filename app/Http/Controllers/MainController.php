<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Pastikan Carbon diimport untuk pengaturan waktu

class MainController extends Controller
{
    public function info()
    {
        return view('info');
    }

    public function home()
    {
        return view('home');
    }

    public function katalog()
    {
        $barang = DB::table('lelang')
            ->join('users', 'lelang.id_user', '=', 'users.id_user')
            ->leftJoin('penawaran', function ($join) {
                $join->on('lelang.id_lelang', '=', 'penawaran.id_lelang')
                    ->whereRaw('penawaran.penawaran_harga = (select max(penawaran_harga) from penawaran where id_lelang = lelang.id_lelang)');
            })
            ->leftJoin('users as bidder', 'penawaran.id_user', '=', 'bidder.id_user')
            ->select(
                'lelang.*',
                'users.username',
                'users.kelas',
                'bidder.username as bidder_username',
                'bidder.kelas as bidder_kelas'
            )
            ->where('lelang.status', 'open')
            ->where('lelang.tgl_akhir', '>', Carbon::now()) // Menggunakan Carbon untuk waktu WIB
            ->orderBy('lelang.tgl_akhir', 'asc')
            ->get();

        return view('katalog', ['barang' => $barang]);
    }

    public function aksi_tawar(Request $request)
    {
        if (!session('id_user')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $id_lelang = $request->input('id_lelang');
        $nominal_tawaran = $request->input('nominal_tawaran');

        $barang = DB::table('lelang')->where('id_lelang', $id_lelang)->first();

        // VALIDASI TAMBAHAN: Tidak boleh menawar barang sendiri
        if ($barang->id_user == session('id_user')) {
            return redirect()->back()->with('error', 'Anda tidak dapat menawar barang Anda sendiri!');
        }

        if ($nominal_tawaran <= $barang->harga_awal) {
            return redirect()->back()->with('error', 'Tawaran harus lebih tinggi dari harga saat ini!');
        }

        DB::table('penawaran')->insert([
            'id_lelang' => $id_lelang,
            'id_user' => session('id_user'),
            'penawaran_harga' => $nominal_tawaran,
            'waktu_penawaran' => Carbon::now() // Waktu WIB
        ]);

        DB::table('lelang')->where('id_lelang', $id_lelang)->update([
            'harga_awal' => $nominal_tawaran
        ]);

        return redirect()->back()->with('success', 'Berhasil! Anda menjadi penawar tertinggi.');
    }

    public function login()
    {
        if (session('id_user')) {
            return redirect('/home');
        }
        return view('login');
    }

    public function aksi_login(Request $request)
    {
        $data = [
            'username' => $request->input('username'),
            'password' => $request->input('password')
        ];

        $model = new MainModel();
        $user = $model->login('users', $data);

        if ($user) {
            $request->session()->put('id_user', $user->id_user);
            $request->session()->put('id_user_type', $user->id_user_type);
            $request->session()->put('username', $user->username);
            $request->session()->put('nama_lengkap', $user->nama_lengkap);

            return redirect('/home');
        } else {
            return back()->with('error', 'Username atau Password salah.');
        }
    }

    public function aksi_logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/home');
    }

    public function pasang_lelang()
    {
        if (!session('id_user')) {
            return redirect('/login');
        }
        return view('pasang_lelang');
    }

    public function aksi_pasang_lelang(Request $request)
    {
        if (!session('id_user')) {
            return redirect('/login');
        }

        $nama_file_foto = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                if ($file) {
                    $nama_foto = time() . "_" . $file->getClientOriginalName();
                    $file->move(public_path('img_barang'), $nama_foto);
                    $nama_file_foto[] = $nama_foto;
                }
            }
        }

        $data = array(
            'id_user' => session('id_user'),
            'nama_barang' => $request->input('nama_barang'),
            'deskripsi' => $request->input('deskripsi'),
            'harga_awal' => $request->input('harga_awal'),
            'foto' => implode(',', $nama_file_foto),
            'status' => 'pending',
            'tgl_input' => Carbon::now(), // Waktu WIB
        );

        $model = new MainModel();
        $model->simpan('lelang', $data);

        return redirect('/lelangku')->with('success', 'Barang Anda berhasil didaftarkan!');
    }

    public function moderasi()
    {
        if (session('id_user_type') != 1 && session('id_user_type') != 2) {
            return redirect('/login');
        }

        $barang = DB::table('lelang')
            ->join('users', 'lelang.id_user', '=', 'users.id_user')
            ->select('lelang.*', 'users.username')
            ->where('lelang.status', 'pending')
            ->get();

        return view('moderasi', ['barang' => $barang]);
    }

    public function aksi_moderasi(Request $request)
    {
        if (!session('id_user')) {
            return redirect('/login');
        }

        $data = [
            'status' => $request->input('status'),
            'manager_id' => session('id_user'),
            'tgl_update_status' => Carbon::now()
        ];

        $model = new MainModel();
        $model->edit('lelang', ['id_lelang' => $request->input('id_lelang')], $data);

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }

    public function aksi_suspend(Request $request)
    {
        if (!session('id_user')) {
            return redirect('/login');
        }

        $model = new MainModel();
        $model->edit('users', ['id_user' => $request->input('id_user')], ['id_user_type' => 4]);

        $model->edit('lelang', ['id_lelang' => $request->input('id_lelang')], [
            'status' => 'rejected',
            'manager_id' => session('id_user'),
            'tgl_update_status' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Akun disuspend dan barang ditolak.');
    }

    public function lelangku(Request $request)
    {
        if (!session('id_user')) {
            return redirect('/login');
        }

        $filter = $request->query('status', 'all');
        $model = new MainModel();
        $barang = $model->getLelangByUser(session('id_user'), $filter);

        return view('lelangku', [
            'barang' => $barang,
            'current_filter' => $filter
        ]);
    }

    public function aksi_mulai_lelang(Request $request)
    {
        if (!session('id_user')) {
            return redirect('/login');
        }

        $id_lelang = $request->input('id_lelang');
        $durasi = (int) $request->input('durasi');

        // Mengatur tgl_akhir secara presisi ke akhir hari di zona waktu Jakarta
        $tgl_akhir = Carbon::now('Asia/Jakarta')->addDays($durasi)->endOfDay();

        $data = [
            'status' => 'open',
            'tgl_akhir' => $tgl_akhir,
            'tgl_update_status' => Carbon::now()
        ];

        $model = new MainModel();
        $model->edit('lelang', ['id_lelang' => $id_lelang], $data);

        return redirect()->back()->with('success', 'Lelang dimulai! Berakhir pada ' . $tgl_akhir->format('d M Y, H:i') . ' WIB');
    }

    // Fungsi khusus untuk mensuplai data ke AJAX Auto-Reload
    public function get_updates()
    {
        $barang = DB::table('lelang')
            ->leftJoin('penawaran', function ($join) {
                $join->on('lelang.id_lelang', '=', 'penawaran.id_lelang')
                    ->whereRaw('penawaran.penawaran_harga = (select max(penawaran_harga) from penawaran where id_lelang = lelang.id_lelang)');
            })
            ->leftJoin('users as bidder', 'penawaran.id_user', '=', 'bidder.id_user')
            ->select(
                'lelang.id_lelang',
                'lelang.harga_awal',
                'lelang.status', // Tambahkan status agar JS bisa tahu jika ada perubahan status
                'bidder.username as bidder_username',
                'bidder.kelas as bidder_kelas'
            )
            // Hapus filter ->where('lelang.status', 'open') agar semua barang di lelangku ikut terupdate harganya
            ->get();

        return response()->json($barang);
    }
}