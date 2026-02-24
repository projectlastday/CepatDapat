<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Allowed statuses in the system
    private const STATUSES = ['pending', 'accepted', 'rejected', 'open', 'sold', 'unsold', 'canceled'];

    // Roles that can access this page
    private const ALLOWED_ROLES = [1, 5, 7];

    /**
     * Main report page with filters, charts, and table.
     */
    public function index(Request $request)
    {
        if (!\App\Helpers\PermissionHelper::check('report_view')) {
            return redirect('/catalog')->with('error', 'Akses ditolak.');
        }

        // --- Parse & validate filters ---
        $filters = $this->parseFilters($request);

        // --- Base query builder (reused for charts + table) ---
        $baseQuery = $this->buildBaseQuery($filters);

        // ===== CHART 1: Lelang by Status (Pie) =====
        $statusCounts = (clone $baseQuery)
            ->select('lelang.status', DB::raw('COUNT(*) as total'))
            ->groupBy('lelang.status')
            ->pluck('total', 'lelang.status')
            ->toArray();

        // Make sure all selected statuses appear in the pie chart (even with 0)
        $statusChartData = [];
        foreach ($filters['statuses'] as $s) {
            $statusChartData[$s] = $statusCounts[$s] ?? 0;
        }

        // ===== CHART 2: Pendapatan Harian (Line/Area) — sold only =====
        $revenueQuery = $this->buildBaseQuery($filters)
            ->where('lelang.status', 'sold');

        $dailyRevenue = $revenueQuery
            ->select(DB::raw('DATE(lelang.tgl_status_sold) as day'), DB::raw('SUM(lelang.harga_awal) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        // Build full date range for chart x-axis
        $revenueDates = [];
        $revenueValues = [];
        $current = Carbon::parse($filters['start_date'])->copy();
        $end = Carbon::parse($filters['end_date'])->copy();
        while ($current->lte($end)) {
            $dateStr = $current->toDateString();
            $revenueDates[] = $current->format('d M');
            $revenueValues[] = (int) ($dailyRevenue[$dateStr] ?? 0);
            $current->addDay();
        }

        // ===== CHART 3: Top Bidders / Keaktifan Lelang (Bar) =====
        $topBidders = DB::table('penawaran')
            ->join('users', 'penawaran.id_user', '=', 'users.id_user')
            ->join('lelang', 'penawaran.id_lelang', '=', 'lelang.id_lelang')
            ->whereIn('lelang.status', $filters['statuses'])
            ->whereBetween('penawaran.waktu_penawaran', [$filters['start_date'], $filters['end_date'] . ' 23:59:59'])
            ->select('users.username', DB::raw('COUNT(*) as total_bids'))
            ->groupBy('users.username')
            ->orderByDesc('total_bids')
            ->limit(10)
            ->get();

        $bidderNames = $topBidders->pluck('username')->toArray();
        $bidderCounts = $topBidders->pluck('total_bids')->toArray();

        // ===== TABLE DATA (paginated) =====
        $tableQuery = $this->buildBaseQuery($filters)
            ->leftJoin('penawaran', function ($join) {
                $join->on('lelang.id_lelang', '=', 'penawaran.id_lelang')
                    ->whereRaw('penawaran.penawaran_harga = (SELECT MAX(p2.penawaran_harga) FROM penawaran p2 WHERE p2.id_lelang = lelang.id_lelang)');
            })
            ->leftJoin('users as buyer', 'penawaran.id_user', '=', 'buyer.id_user')
            ->select(
                'lelang.id_lelang',
                'lelang.nama_barang',
                'lelang.harga_awal',
                'lelang.status',
                'lelang.tgl_input',
                'lelang.tgl_status_open',
                'lelang.tgl_status_sold',
                'users.username as pemilik',
                'buyer.username as pemenang',
                'penawaran.penawaran_harga as harga_akhir'
            )
            ->orderBy('lelang.tgl_input', 'desc');

        $tableData = $tableQuery->paginate(15)->appends($request->query());

        // ===== Package everything =====
        return view('reports.index', [
            'filters' => $filters,
            'allStatuses' => self::STATUSES,
            'statusChartData' => $statusChartData,
            'revenueDates' => $revenueDates,
            'revenueValues' => $revenueValues,
            'bidderNames' => $bidderNames,
            'bidderCounts' => $bidderCounts,
            'tableData' => $tableData,
        ]);
    }

    /**
     * Export filtered data as PDF.
     */
    public function exportPdf(Request $request)
    {
        if (!\App\Helpers\PermissionHelper::check('report_view')) {
            return redirect('/catalog')->with('error', 'Akses ditolak.');
        }

        $filters = $this->parseFilters($request);
        $rows = $this->getExportData($filters);

        $pdf = Pdf::loadView('reports.pdf', [
            'rows' => $rows,
            'filters' => $filters,
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_lelang_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export filtered data as Excel (HTML table → .xls).
     * Uses a simple HTML response with Excel-compatible headers.
     */
    public function exportExcel(Request $request)
    {
        if (!\App\Helpers\PermissionHelper::check('report_view')) {
            return redirect('/catalog')->with('error', 'Akses ditolak.');
        }

        $filters = $this->parseFilters($request);
        $rows = $this->getExportData($filters);

        $filename = 'laporan_lelang_' . now()->format('Ymd_His') . '.xls';

        // Build HTML table
        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="UTF-8"></head><body>';
        $html .= '<table border="1">';
        $html .= '<tr><th>No</th><th>Tanggal</th><th>Nama Barang</th><th>Pemilik</th><th>Pemenang</th><th>Harga Awal</th><th>Harga Akhir</th><th>Status</th></tr>';

        foreach ($rows as $i => $row) {
            $html .= '<tr>';
            $html .= '<td>' . ($i + 1) . '</td>';
            $html .= '<td>' . Carbon::parse($row->tgl_input)->format('d/m/Y H:i') . '</td>';
            $html .= '<td>' . e($row->nama_barang) . '</td>';
            $html .= '<td>' . e($row->pemilik) . '</td>';
            $html .= '<td>' . e($row->pemenang ?? '-') . '</td>';
            $html .= '<td>' . number_format($row->harga_awal, 0, ',', '.') . '</td>';
            $html .= '<td>' . number_format($row->harga_akhir, 0, ',', '.') . '</td>';
            $html .= '<td>' . ucfirst($row->status) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // ============================
    //  Private helpers
    // ============================

    /**
     * Parse and sanitize request filters.
     */
    private function parseFilters(Request $request): array
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        // Validate dates
        try {
            $startDate = Carbon::parse($startDate)->toDateString();
            $endDate = Carbon::parse($endDate)->toDateString();
        } catch (\Exception $e) {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->toDateString();
        }

        // Ensure start <= end
        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $statuses = $request->input('statuses', self::STATUSES);
        if (!is_array($statuses)) {
            $statuses = self::STATUSES;
        }
        // Whitelist
        $statuses = array_intersect($statuses, self::STATUSES);
        if (empty($statuses)) {
            $statuses = self::STATUSES;
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'statuses' => array_values($statuses),
        ];
    }

    /**
     * Build the base query with date range + status filters.
     */
    private function buildBaseQuery(array $filters)
    {
        return DB::table('lelang')
            ->join('users', 'lelang.id_user', '=', 'users.id_user')
            ->whereIn('lelang.status', $filters['statuses'])
            ->where('lelang.status_delete', 0)
            ->whereBetween('lelang.tgl_input', [$filters['start_date'], $filters['end_date'] . ' 23:59:59']);
    }

    /**
     * Get flat export data (used by both PDF and Excel).
     */
    private function getExportData(array $filters)
    {
        return $this->buildBaseQuery($filters)
            ->leftJoin('penawaran', function ($join) {
                $join->on('lelang.id_lelang', '=', 'penawaran.id_lelang')
                    ->whereRaw('penawaran.penawaran_harga = (SELECT MAX(p2.penawaran_harga) FROM penawaran p2 WHERE p2.id_lelang = lelang.id_lelang)');
            })
            ->leftJoin('users as buyer', 'penawaran.id_user', '=', 'buyer.id_user')
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
}
