<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    private const ALLOWED_TABS = ['login', 'activity', 'cancel', 'delete', 'uncancel', 'restore', 'setting'];
    private const LELANG_TABS = ['cancel', 'delete', 'uncancel', 'restore'];

    public function index(Request $request)
    {
        // Super Admin only
        if (!\App\Helpers\PermissionHelper::check('history_view')) {
            abort(403);
        }

        $tab = $request->input('tab', 'login');
        if (!in_array($tab, self::ALLOWED_TABS)) {
            $tab = 'login';
        }

        // --- Parse filters ---
        $search = $request->input('search', '');
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $namaLelang = $request->input('nama_lelang', '');

        // Swap dates if needed
        if ($startDate && $endDate && $startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // --- Build query per tab ---
        $query = $this->buildTabQuery($tab);

        // --- Apply common filters ---
        // Username search
        if ($search) {
            $query->where('u.username', 'like', '%' . $search . '%');
        }

        // Date range
        if ($startDate) {
            $query->where('h.created_at', '>=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $query->where('h.created_at', '<=', $endDate . ' 23:59:59');
        }

        // Nama Lelang (only for lelang tabs)
        if ($namaLelang && in_array($tab, self::LELANG_TABS)) {
            $query->where('l.nama_barang', 'like', '%' . $namaLelang . '%');
        }

        // --- Sorting ---
        $query->orderBy('h.created_at', 'desc');

        // --- Paginate ---
        $data = $query->paginate(10)->withQueryString();

        return view('admin.history_data', [
            'tab' => $tab,
            'data' => $data,
            'search' => $search,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'nama_lelang' => $namaLelang,
            'lelangTabs' => self::LELANG_TABS,
        ]);
    }

    /**
     * Build the base query with joins for the given tab.
     */
    private function buildTabQuery(string $tab)
    {
        switch ($tab) {
            case 'login':
                return DB::table('history_login as h')
                    ->join('users as u', 'h.id_user', '=', 'u.id_user')
                    ->select(
                        'u.username',
                        'h.ip_address',
                        'h.user_agent',
                        'h.created_at'
                    );

            case 'activity':
                return DB::table('history_activity_user as h')
                    ->join('users as u', 'h.id_user', '=', 'u.id_user')
                    ->select(
                        'u.username',
                        'h.url',
                        'h.created_at'
                    );

            case 'cancel':
                return DB::table('history_cancel_lelang as h')
                    ->join('users as u', 'h.id_pelaku', '=', 'u.id_user')
                    ->leftJoin('lelang as l', 'h.id_lelang', '=', 'l.id_lelang')
                    ->select(
                        'u.username as pelaku',
                        'l.nama_barang',
                        'h.alasan',
                        'h.created_at',
                        'l.status as current_status',
                        'l.status_delete'
                    );

            case 'delete':
                return DB::table('history_delete_lelang as h')
                    ->join('users as u', 'h.id_pelaku', '=', 'u.id_user')
                    ->leftJoin('lelang as l', 'h.id_lelang', '=', 'l.id_lelang')
                    ->select(
                        'u.username as pelaku',
                        'l.nama_barang',
                        'h.alasan',
                        'h.created_at',
                        'l.status as current_status',
                        'l.status_delete'
                    );

            case 'uncancel':
                return DB::table('history_uncancel_lelang as h')
                    ->join('users as u', 'h.id_pelaku', '=', 'u.id_user')
                    ->leftJoin('lelang as l', 'h.id_lelang', '=', 'l.id_lelang')
                    ->select(
                        'u.username as pelaku',
                        'l.nama_barang',
                        'h.alasan',
                        'h.created_at',
                        'l.status as current_status',
                        'l.status_delete'
                    );

            case 'restore':
                return DB::table('history_restore_lelang as h')
                    ->join('users as u', 'h.id_pelaku', '=', 'u.id_user')
                    ->leftJoin('lelang as l', 'h.id_lelang', '=', 'l.id_lelang')
                    ->select(
                        'u.username as pelaku',
                        'l.nama_barang',
                        'h.alasan',
                        'h.created_at',
                        'l.status as current_status',
                        'l.status_delete'
                    );

            case 'setting':
                return DB::table('history_setting_website as h')
                    ->join('users as u', 'h.id_pelaku', '=', 'u.id_user')
                    ->select(
                        'u.username as pelaku',
                        'h.type',
                        'h.data_lama',
                        'h.data_baru',
                        'h.created_at'
                    );

            default:
                return DB::table('history_login as h')
                    ->join('users as u', 'h.id_user', '=', 'u.id_user')
                    ->select('u.username', 'h.ip_address', 'h.user_agent', 'h.created_at');
        }
    }
}
