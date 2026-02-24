<?php

namespace App\Http\Controllers;

use App\Services\DiscordWebhookService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionController extends Controller
{
    public function catalog()
    {
        $this->checkAuctionExpiry();

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
                // 'users.kelas', <-- HAPUS BARIS INI
                'bidder.username as bidder_username'
                // 'bidder.kelas as bidder_kelas' <-- HAPUS BARIS INI
            )
            ->where('lelang.status', 'open')
            ->where('lelang.status_delete', 0)
            ->orderBy('lelang.tgl_status_open', 'desc')
            ->get();

        return view('auction.catalog', ['barang' => $barang]);
    }

    public function placeBid(Request $request)
    {
        // Cek login menggunakan session id_user
        if (!session('id_user')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $id_lelang = $request->input('id_lelang');
        $nominal_tawaran = $request->input('nominal_tawaran');

        $barang = DB::table('lelang')->where('id_lelang', $id_lelang)->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Lelang tidak ditemukan.');
        }

        // Phase 5: Reject bids unless auction is open, not deleted, and not expired
        if ($barang->status !== 'open') {
            return redirect()->back()->with('error', 'Lelang ini tidak sedang berlangsung.');
        }
        if ($barang->status_delete != 0) {
            return redirect()->back()->with('error', 'Lelang ini telah dihapus.');
        }
        if ($barang->tgl_status_sold && Carbon::parse($barang->tgl_status_sold)->isPast()) {
            return redirect()->back()->with('error', 'Waktu lelang telah berakhir.');
        }

        // Validasi kepemilikan: Tidak boleh menawar barang sendiri
        if ($barang->id_user == session('id_user')) {
            return redirect()->back()->with('error', 'Anda tidak dapat menawar barang Anda sendiri!');
        }

        // Validasi harga: Harus lebih tinggi dari harga saat ini
        if ($nominal_tawaran <= $barang->harga_awal) {
            return redirect()->back()->with('error', 'Tawaran harus lebih tinggi dari harga saat ini!');
        }

        // Simpan penawaran baru
        DB::table('penawaran')->insert([
            'id_lelang' => $id_lelang,
            'id_user' => session('id_user'),
            'penawaran_harga' => $nominal_tawaran,
            'waktu_penawaran' => Carbon::now(),
        ]);

        // Update harga terbaru di tabel lelang
        DB::table('lelang')->where('id_lelang', $id_lelang)->update([
            'harga_awal' => $nominal_tawaran,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Anda menjadi penawar tertinggi.');
    }

    public function add_auction()
    {
        return view('add_auction');
    }

    public function store_auction(Request $request)
    {
        if (!session('id_user')) {
            return redirect('/login');
        }

        $nama_file_foto = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                if ($file->isValid()) {
                    $nama_foto = time() . "_" . $file->getClientOriginalName();
                    $file->move(public_path('img_barang'), $nama_foto);
                    $nama_file_foto[] = $nama_foto;
                }
            }
        }

        DB::table('lelang')->insert([
            'id_user' => session('id_user'),
            'nama_barang' => $request->input('nama_barang'),
            'deskripsi' => $request->input('deskripsi'),
            'harga_awal' => $request->input('harga_awal'),
            'foto' => implode(',', $nama_file_foto),
            'status' => 'pending',
            'tgl_input' => Carbon::now(),
        ]);

        return redirect('/lelangku')->with('success', 'Barang Anda berhasil didaftarkan!');
    }
    public function lelangku(Request $request)
    {
        if (!session('id_user')) {
            return redirect('/login');
        }

        $this->checkAuctionExpiry();

        $status = $request->query('status', 'all');
        $id_user = session('id_user');

        $query = DB::table('lelang')
            ->leftJoin(DB::raw('(SELECT id_lelang, id_user, penawaran_harga FROM penawaran WHERE penawaran_harga = (SELECT MAX(p2.penawaran_harga) FROM penawaran p2 WHERE p2.id_lelang = penawaran.id_lelang)) as penawaran'), function ($join) {
                $join->on('lelang.id_lelang', '=', 'penawaran.id_lelang');
            })
            ->leftJoin('users as buyer', 'penawaran.id_user', '=', 'buyer.id_user')
            ->select('lelang.*', 'buyer.username as buyer_username', 'buyer.telepon as buyer_telepon', 'penawaran.penawaran_harga as harga_terjual')
            ->where('lelang.id_user', $id_user)
            ->where('lelang.status_delete', 0)
            ->orderBy('lelang.tgl_input', 'desc');

        if ($status != 'all') {
            $query->where('status', $status);
        }

        $barang = $query->get();

        return view('auction.lelangku', [
            'barang' => $barang,
            'current_filter' => $status
        ]);
    }

    /**
     * Dapat / Barang Menang — shows auctions the current user has won (highest bid on sold auctions).
     */
    public function dapat(Request $request)
    {
        if (!session('id_user')) {
            return redirect('/login');
        }

        $this->checkAuctionExpiry();

        $userId = session('id_user');

        // Anti-join: find the single highest bid per auction.
        // p1 is the candidate bid. LEFT JOIN p2 where p2 is strictly better.
        // If p2.id_lelang IS NULL, no better bid exists → p1 is the winner.
        $query = DB::table('penawaran as p1')
            ->leftJoin('penawaran as p2', function ($join) {
                $join->on('p1.id_lelang', '=', 'p2.id_lelang')
                    ->where(function ($q) {
                        $q->whereColumn('p2.penawaran_harga', '>', 'p1.penawaran_harga')
                          ->orWhere(function ($q2) {
                              // Tie-breaker: later bid wins
                              $q2->whereColumn('p2.penawaran_harga', '=', 'p1.penawaran_harga')
                                 ->whereColumn('p2.waktu_penawaran', '>', 'p1.waktu_penawaran');
                          });
                    });
            })
            ->whereNull('p2.id_lelang') // no strictly better bid exists
            ->join('lelang', 'p1.id_lelang', '=', 'lelang.id_lelang')
            ->join('users as owner', 'lelang.id_user', '=', 'owner.id_user')
            ->where('lelang.status', 'sold')
            ->where('lelang.status_delete', 0)
            ->where('p1.id_user', $userId)
            ->select(
                'lelang.id_lelang',
                'lelang.nama_barang',
                'lelang.deskripsi',
                'lelang.foto',
                'lelang.status',
                'lelang.harga_awal',
                'lelang.tgl_status_sold',
                'p1.penawaran_harga as winning_bid',
                'owner.username as owner_username',
                'owner.telepon as owner_telepon'
            )
            ->orderByDesc('lelang.tgl_status_sold');

        $barang = $query->paginate(12)->withQueryString();

        return view('auction.dapat', compact('barang'));
    }

    public function start_auction(Request $request)
    {
        $id_lelang = $request->input('id_lelang');
        $durasi = $request->input('durasi');

        // Logic calculate end date (today + duration days at 24:00)
        // Carbon::now() -> addDays($durasi) -> endOfDay()
        $tgl_akhir = Carbon::now()->addDays((int) $durasi)->endOfDay();

        DB::table('lelang')->where('id_lelang', $id_lelang)->update([
            'status' => 'open',
            'tgl_status_sold' => $tgl_akhir,
            'tgl_status_open' => Carbon::now()
        ]);

        return redirect('/lelangku')->with('success', 'Lelang berhasil dimulai!');
    }

    // Moderasi
    public function moderasi()
    {
        if (!\App\Helpers\PermissionHelper::check('moderation_view')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk melihat halaman moderasi.');
        }

        $barang = DB::table('lelang')
            ->join('users', 'lelang.id_user', '=', 'users.id_user')
            ->select('lelang.*', 'users.username')
            ->where('lelang.status', 'pending')
            ->where('lelang.status_delete', 0)
            ->orderBy('lelang.tgl_input', 'asc')
            ->get();

        return view('moderasi', ['barang' => $barang]);
    }

    public function moderasi_aksi(Request $request)
    {
        if (!\App\Helpers\PermissionHelper::check('moderation_action')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses memoderasi.');
        }

        $id_lelang = $request->input('id_lelang');
        $status = $request->input('status'); // accepted / rejected

        $updateData = [
            'status' => $status,
            'moderator_id' => session('id_user')
        ];

        if ($status == 'accepted') {
            $updateData['tgl_status_accepted'] = Carbon::now();
        } elseif ($status == 'rejected') {
            $updateData['tgl_status_rejected'] = Carbon::now();
        }

        DB::table('lelang')->where('id_lelang', $id_lelang)->update($updateData);

        return redirect('/moderasi')->with('success', 'Status barang berhasil diperbarui!');
    }

    public function suspend_user(Request $request)
    {
        if (!\App\Helpers\PermissionHelper::check('user_suspend')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk men-suspend user.');
        }

        $id_user = $request->input('id_user');

        // Hapus barang-barang user
        DB::table('lelang')->where('id_user', $id_user)->delete();

        // Hapus user
        DB::table('users')->where('id_user', $id_user)->delete();

        return redirect('/moderasi')->with('success', 'User berhasil disuspend!');
    }

    public function manajemen_lelang(Request $request)
    {
        // Akses hanya untuk Admin (1), Super Moderator (6), dan Super Admin (7)
        if (!\App\Helpers\PermissionHelper::check('auction_manage_view')) {
            return redirect('/catalog')->with('error', 'Akses ditolak.');
        }

        $this->checkAuctionExpiry();

        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = DB::table('lelang')
            ->join('users', 'lelang.id_user', '=', 'users.id_user') // Join untuk dapat username pemilik
            ->leftJoin('users as moderator', 'lelang.moderator_id', '=', 'moderator.id_user') // Join untuk username moderator
            ->leftJoin('penawaran', function ($join) {
                $join->on('lelang.id_lelang', '=', 'penawaran.id_lelang')
                    ->whereRaw('penawaran.penawaran_harga = (select max(p2.penawaran_harga) from penawaran p2 where p2.id_lelang = lelang.id_lelang)');
            })
            ->leftJoin('users as buyer', 'penawaran.id_user', '=', 'buyer.id_user') // Join untuk username pembeli
            ->select('lelang.*', 'users.username', 'moderator.username as moderator_username', 'buyer.username as buyer_username', 'penawaran.penawaran_harga as harga_terjual')
            ->where('lelang.status_delete', 0)
            ->orderBy('lelang.tgl_input', 'desc');

        if ($status != 'all') {
            $query->where('lelang.status', $status);
        }

        if ($search) {
            $query->where('lelang.nama_barang', 'like', '%' . $search . '%');
        }

        $barang = $query->get();

        return view('auction.manajemen_lelang', [
            'barang' => $barang,
            'current_filter' => $status,
            'search' => $search
        ]);
    }

    public function cancel_auction(Request $request)
    {
        // Akses hanya untuk Admin (1), Super Moderator (6), dan Super Admin (7)
        if (!\App\Helpers\PermissionHelper::check('auction_cancel')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk membatalkan lelang.');
        }

        $id_lelang = $request->input('id_lelang');
        $alasan = $request->input('alasan');

        if (!$id_lelang || !$alasan) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        // Update status lelang
        DB::table('lelang')->where('id_lelang', $id_lelang)->update([
            'status' => 'canceled',
            'tgl_status_canceled' => Carbon::now()
        ]);

        // Catat di history
        DB::table('history_cancel_lelang')->insert([
            'id_pelaku' => session('id_user'),
            'id_lelang' => $id_lelang,
            'alasan' => $alasan,
            'created_at' => Carbon::now()
        ]);

        $namaBarang = DB::table('lelang')->where('id_lelang', $id_lelang)->value('nama_barang') ?? '-';
        DiscordWebhookService::send('cancel', session('username'), $namaBarang, $alasan);

        return redirect()->back()->with('success', 'Lelang berhasil dibatalkan.');
    }

    public function delete_auction(Request $request)
    {
        // Akses hanya untuk Admin (1) dan Super Admin (7)
        if (!\App\Helpers\PermissionHelper::check('auction_delete')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus lelang.');
        }

        $id_lelang = $request->input('id_lelang');
        $alasan = $request->input('alasan');

        if (!$id_lelang || !$alasan) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        // Soft delete: set status_delete = 1
        DB::table('lelang')->where('id_lelang', $id_lelang)->update([
            'status_delete' => 1,
        ]);

        // Catat di history
        DB::table('history_delete_lelang')->insert([
            'id_pelaku' => session('id_user'),
            'id_lelang' => $id_lelang,
            'alasan' => $alasan,
            'created_at' => Carbon::now()
        ]);

        $namaBarang = DB::table('lelang')->where('id_lelang', $id_lelang)->value('nama_barang') ?? '-';
        DiscordWebhookService::send('delete', session('username'), $namaBarang, $alasan);

        return redirect()->back()->with('success', 'Lelang berhasil dihapus.');
    }

    public function deleted_auctions(Request $request)
    {
        // Akses hanya untuk Super Admin (7)
        if (session('id_user_type') != 7) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $this->checkAuctionExpiry();

        $search = $request->input('search');

        $query = DB::table('lelang')
            ->join('users', 'lelang.id_user', '=', 'users.id_user')
            ->leftJoin('users as moderator', 'lelang.moderator_id', '=', 'moderator.id_user')
            ->leftJoin(DB::raw('(SELECT id_lelang, id_user, penawaran_harga FROM penawaran WHERE penawaran_harga = (SELECT MAX(p2.penawaran_harga) FROM penawaran p2 WHERE p2.id_lelang = penawaran.id_lelang)) as penawaran'), function ($join) {
                $join->on('lelang.id_lelang', '=', 'penawaran.id_lelang');
            })
            ->leftJoin('users as buyer', 'penawaran.id_user', '=', 'buyer.id_user')
            ->select('lelang.*', 'users.username', 'moderator.username as moderator_username', 'buyer.username as buyer_username', 'penawaran.penawaran_harga as harga_terjual')
            ->where('lelang.status_delete', 1)
            ->orderBy('lelang.tgl_input', 'desc');

        if ($search) {
            $query->where('lelang.nama_barang', 'like', '%' . $search . '%');
        }

        $barang = $query->get();

        return view('auction.lelang_terhapus', [
            'barang' => $barang,
            'search' => $search,
        ]);
    }

    public function restore_auction(Request $request)
    {
        // Akses hanya untuk Super Admin (7)
        if (!\App\Helpers\PermissionHelper::check('auction_restore')) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $id_lelang = $request->input('id_lelang');
        $alasan = $request->input('alasan');

        if (!$id_lelang || !$alasan) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        // Restore: set status_delete = 0
        DB::table('lelang')->where('id_lelang', $id_lelang)->update([
            'status_delete' => 0,
        ]);

        // Catat di history
        DB::table('history_restore_lelang')->insert([
            'id_pelaku' => session('id_user'),
            'id_lelang' => $id_lelang,
            'alasan' => $alasan,
            'created_at' => Carbon::now()
        ]);

        $namaBarang = DB::table('lelang')->where('id_lelang', $id_lelang)->value('nama_barang') ?? '-';
        DiscordWebhookService::send('restore', session('username'), $namaBarang, $alasan);

        return redirect()->back()->with('success', 'Lelang berhasil direstore.');
    }

    public function canceled_auctions(Request $request)
    {
        // Akses hanya untuk Super Admin (7)
        if (session('id_user_type') != 7) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $this->checkAuctionExpiry();

        $search = $request->input('search');

        $query = DB::table('lelang')
            ->join('users', 'lelang.id_user', '=', 'users.id_user')
            ->leftJoin('users as moderator', 'lelang.moderator_id', '=', 'moderator.id_user')
            ->leftJoin(DB::raw('(SELECT id_lelang, id_user, penawaran_harga FROM penawaran WHERE penawaran_harga = (SELECT MAX(p2.penawaran_harga) FROM penawaran p2 WHERE p2.id_lelang = penawaran.id_lelang)) as penawaran'), function ($join) {
                $join->on('lelang.id_lelang', '=', 'penawaran.id_lelang');
            })
            ->leftJoin('users as buyer', 'penawaran.id_user', '=', 'buyer.id_user')
            ->select('lelang.*', 'users.username', 'moderator.username as moderator_username', 'buyer.username as buyer_username', 'penawaran.penawaran_harga as harga_terjual')
            ->where('lelang.status', 'canceled')
            ->where('lelang.status_delete', 0)
            ->orderBy('lelang.tgl_status_canceled', 'desc');

        if ($search) {
            $query->where('lelang.nama_barang', 'like', '%' . $search . '%');
        }

        $barang = $query->get();

        return view('auction.lelang_tercancel', [
            'barang' => $barang,
            'search' => $search,
        ]);
    }

    public function uncancel_auction(Request $request)
    {
        // Akses hanya untuk Super Admin (7)
        if (!\App\Helpers\PermissionHelper::check('auction_uncancel')) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $id_lelang = $request->input('id_lelang');
        $alasan = $request->input('alasan');

        if (!$id_lelang || !$alasan) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        // Uncancel: set status back to accepted, clear tgl_status_canceled
        DB::table('lelang')->where('id_lelang', $id_lelang)->update([
            'status' => 'accepted',
            'tgl_status_canceled' => null,
        ]);

        // Catat di history
        DB::table('history_uncancel_lelang')->insert([
            'id_pelaku' => session('id_user'),
            'id_lelang' => $id_lelang,
            'alasan' => $alasan,
            'created_at' => Carbon::now()
        ]);

        $namaBarang = DB::table('lelang')->where('id_lelang', $id_lelang)->value('nama_barang') ?? '-';
        DiscordWebhookService::send('uncancel', session('username'), $namaBarang, $alasan);

        return redirect()->back()->with('success', 'Lelang berhasil di-uncancel.');
    }

    protected function checkAuctionExpiry()
    {
        // Filter only active, non-deleted, expired auctions
        $expired_auctions = DB::table('lelang')
            ->where('status', 'open')
            ->where('status_delete', 0)
            ->where('tgl_status_sold', '<=', Carbon::now())
            ->get();

        foreach ($expired_auctions as $auction) {
            // Find deterministic winner: highest bid, tie-break by latest waktu_penawaran
            $winner = DB::table('penawaran as p1')
                ->leftJoin('penawaran as p2', function ($join) {
                    $join->on('p1.id_lelang', '=', 'p2.id_lelang')
                        ->where(function ($q) {
                            $q->whereColumn('p2.penawaran_harga', '>', 'p1.penawaran_harga')
                              ->orWhere(function ($q2) {
                                  $q2->whereColumn('p2.penawaran_harga', '=', 'p1.penawaran_harga')
                                     ->whereColumn('p2.waktu_penawaran', '>', 'p1.waktu_penawaran');
                              });
                        });
                })
                ->whereNull('p2.id_lelang')
                ->where('p1.id_lelang', $auction->id_lelang)
                ->select('p1.id_user', 'p1.penawaran_harga')
                ->first();

            if ($winner) {
                // Atomic transition: only update if still 'open' (prevents duplicate processing)
                $affected = DB::table('lelang')
                    ->where('id_lelang', $auction->id_lelang)
                    ->where('status', 'open')
                    ->update(['status' => 'sold']);

                if ($affected > 0) {
                    // Fetch owner and winner user data for notifications
                    $owner = DB::table('users')->where('id_user', $auction->id_user)->first();
                    $winnerUser = DB::table('users')->where('id_user', $winner->id_user)->first();

                    if ($owner && $winnerUser) {
                        \App\Jobs\SendAuctionSoldNotificationsJob::dispatch(
                            $auction->id_lelang,
                            $auction->nama_barang,
                            $owner->id_user,
                            $owner->username,
                            $owner->telepon ?? null,
                            $winnerUser->id_user,
                            $winnerUser->username,
                            $winnerUser->telepon ?? null,
                            $winner->penawaran_harga
                        );

                        Log::info('checkAuctionExpiry: Auction sold, notification dispatched.', [
                            'auction_id' => $auction->id_lelang,
                            'status' => 'sold',
                            'winner_id' => $winner->id_user,
                            'notification_dispatched' => true,
                        ]);
                    } else {
                        Log::warning('checkAuctionExpiry: Auction sold but user data missing.', [
                            'auction_id' => $auction->id_lelang,
                            'owner_found' => (bool) $owner,
                            'winner_found' => (bool) $winnerUser,
                            'notification_dispatched' => false,
                        ]);
                    }
                }
                // If affected == 0, another request already processed this auction — skip silently
            } else {
                // No bids: mark as unsold (atomic)
                $affected = DB::table('lelang')
                    ->where('id_lelang', $auction->id_lelang)
                    ->where('status', 'open')
                    ->update(['status' => 'unsold']);

                if ($affected > 0) {
                    Log::info('checkAuctionExpiry: Auction unsold (no bids).', [
                        'auction_id' => $auction->id_lelang,
                        'status' => 'unsold',
                    ]);
                }
            }
        }
    }
}
