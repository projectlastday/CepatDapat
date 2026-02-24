<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Restrict access to user type 1, 5, and 7
        if (!\App\Helpers\PermissionHelper::check('dashboard_view')) {
            abort(403);
        }

        // ===== HOURLY DATA (0-23) =====
        $hourly_categories = [];
        for ($i = 0; $i < 24; $i++) {
            $hourly_categories[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
        }

        // Today - Lelang Baru (count by tgl_status_open)
        $today_lelang = $this->getHourlyCount('tgl_status_open', Carbon::today());

        // Today - Transaksi (sum harga_awal where status=sold by tgl_status_sold)
        $today_transaksi = $this->getHourlySum('tgl_status_sold', Carbon::today());

        // Yesterday - Lelang Baru
        $yesterday_lelang = $this->getHourlyCount('tgl_status_open', Carbon::yesterday());

        // Yesterday - Transaksi
        $yesterday_transaksi = $this->getHourlySum('tgl_status_sold', Carbon::yesterday());

        // ===== DAILY DATA (1 - end of month) =====
        $daysInMonth = Carbon::now()->daysInMonth;
        $daily_categories = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $daily_categories[] = 'Tgl ' . $i;
        }

        $lastMonthDays = Carbon::now()->subMonth()->daysInMonth;
        $daily_categories_last = [];
        for ($i = 1; $i <= $lastMonthDays; $i++) {
            $daily_categories_last[] = 'Tgl ' . $i;
        }

        // This Month - Lelang Baru
        $this_month_lelang = $this->getDailyCount('tgl_status_open', Carbon::now());

        // This Month - Transaksi
        $this_month_transaksi = $this->getDailySum('tgl_status_sold', Carbon::now());

        // Last Month - Lelang Baru
        $last_month_lelang = $this->getDailyCount('tgl_status_open', Carbon::now()->subMonth());

        // Last Month - Transaksi
        $last_month_transaksi = $this->getDailySum('tgl_status_sold', Carbon::now()->subMonth());

        // Package all chart data
        $all_chart_data = [
            'hourly' => [
                'categories' => $hourly_categories,
                'series' => [
                    'today_lelang' => $today_lelang,
                    'today_transaksi' => $today_transaksi,
                    'yesterday_lelang' => $yesterday_lelang,
                    'yesterday_transaksi' => $yesterday_transaksi,
                ],
            ],
            'daily' => [
                'categories' => $daily_categories,
                'categories_last' => $daily_categories_last,
                'series' => [
                    'this_month_lelang' => $this_month_lelang,
                    'this_month_transaksi' => $this_month_transaksi,
                    'last_month_lelang' => $last_month_lelang,
                    'last_month_transaksi' => $last_month_transaksi,
                ],
            ],
        ];

        return view('dashboard', compact('all_chart_data'));
    }

    // --- HELPER: Hourly COUNT ---
    private function getHourlyCount(string $dateCol, Carbon $date): array
    {
        $data = DB::table('lelang')
            ->select(DB::raw("HOUR($dateCol) as h"), DB::raw('COUNT(*) as total'))
            ->whereDate($dateCol, $date)
            ->groupBy('h')
            ->pluck('total', 'h');

        $result = [];
        for ($i = 0; $i < 24; $i++) {
            $result[] = (int) $data->get($i, 0);
        }
        return $result;
    }

    // --- HELPER: Hourly SUM (sold only) ---
    private function getHourlySum(string $dateCol, Carbon $date): array
    {
        if (!\App\Helpers\PermissionHelper::check('report_view')) {
            abort(403);
        }
        $data = DB::table('lelang')
            ->select(DB::raw("HOUR($dateCol) as h"), DB::raw('SUM(harga_awal) as total'))
            ->where('status', 'sold')
            ->whereDate($dateCol, $date)
            ->groupBy('h')
            ->pluck('total', 'h');

        $result = [];
        for ($i = 0; $i < 24; $i++) {
            $result[] = (int) $data->get($i, 0);
        }
        return $result;
    }

    // --- HELPER: Daily COUNT ---
    private function getDailyCount(string $dateCol, Carbon $month): array
    {
        $data = DB::table('lelang')
            ->select(DB::raw("DAY($dateCol) as d"), DB::raw('COUNT(*) as total'))
            ->whereMonth($dateCol, $month->month)
            ->whereYear($dateCol, $month->year)
            ->groupBy('d')
            ->pluck('total', 'd');

        $daysInMonth = $month->daysInMonth;
        $result = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $result[] = (int) $data->get($i, 0);
        }
        return $result;
    }

    // --- HELPER: Daily SUM (sold only) ---
    private function getDailySum(string $dateCol, Carbon $month): array
    {
        $data = DB::table('lelang')
            ->select(DB::raw("DAY($dateCol) as d"), DB::raw('SUM(harga_awal) as total'))
            ->where('status', 'sold')
            ->whereMonth($dateCol, $month->month)
            ->whereYear($dateCol, $month->year)
            ->groupBy('d')
            ->pluck('total', 'd');

        $daysInMonth = $month->daysInMonth;
        $result = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $result[] = (int) $data->get($i, 0);
        }
        return $result;
    }
}
