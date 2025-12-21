<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Guest;
use App\Models\Order;
use App\Models\Item_in;
use App\Models\Item_out;
use App\Models\Item_out_guest;
use App\Models\Guest_carts_item;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function index()
    {
        // ============================
        // 🔹 JUMLAH DATA
        // ============================
        $itemNow = Item::count();
        $supplierNow = Supplier::count();
        $userNow = User::count();
        $guestNow = Guest::count();
        $ordersNow = Order::count();

        // Hari ini & kemarin
        $itemToday = Item::whereDate('created_at', today())->count();
        $supplierToday = Supplier::whereDate('created_at', today())->count();
        $userToday = User::whereDate('created_at', today())->count();
        $guestToday = Guest::whereDate('created_at', today())->count();
        $ordersToday = Order::whereDate('created_at', today())->count();

        $itemYesterday = Item::whereDate('created_at', today()->subDay())->count();
        $supplierYesterday = Supplier::whereDate('created_at', today()->subDay())->count();
        $userYesterday = User::whereDate('created_at', today()->subDay())->count();
        $guestYesterday = Guest::whereDate('created_at', today()->subDay())->count();
        $ordersYesterday = Order::whereDate('created_at', today()->subDay())->count();

        // Selisih
        $itemDiff = $itemToday - $itemYesterday;
        $supplierDiff = $supplierToday - $supplierYesterday;
        $userDiff = $userToday - $userYesterday;
        $guestDiff = $guestToday - $guestYesterday;
        $orderDiff = $ordersToday - $ordersYesterday;

        // Persentase growth
        $itemPercent = $itemYesterday > 0 ? round(($itemDiff / $itemYesterday) * 100, 1) : 0;
        $supplierPercent = $supplierYesterday > 0 ? round(($supplierDiff / $supplierYesterday) * 100, 1) : 0;
        $userPercent = $userYesterday > 0 ? round(($userDiff / $userYesterday) * 100, 1) : 0;
        $guestPercent = $guestYesterday > 0 ? round(($guestDiff / $guestYesterday) * 100, 1) : 0;

        // ============================
        // 🔹 DATA TABEL TERBARU
        // ============================
        $itemIns = Item_in::with('item')->latest()->take(5)->get();
        $ordersIns = Order::latest()->take(5)->get();
        $lowStockItems = Item::where('stock','<',11)
                             ->whereIn('id', Item_in::pluck('item_id'))
                             ->orderBy('stock','asc')
                             ->take(5)
                             ->get();

        // ============================
        // 🔹 GRAFIK MASUK & ORDER
        // ============================
        $dailyLabels = []; $dailyMasuk = []; $dailyOrder = [];
        $weeklyLabels = []; $weeklyMasuk = []; $weeklyOrder = [];
        $monthlyLabels = []; $monthlyMasuk = []; $monthlyOrder = [];
        $triwulanLabels = ['Triwulan 1','Triwulan 2','Triwulan 3','Triwulan 4']; $triwulanMasuk = []; $triwulanOrder = [];
        $semesterLabels = ['Semester 1','Semester 2']; $semesterMasuk = []; $semesterOrder = [];
        $yearlyLabels = []; $yearlyMasuk = []; $yearlyOrder = [];

        // Harian (minggu ini)
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        for($date=$startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()){
            $dailyLabels[] = $date->format('D');
            $dailyMasuk[] = Item_in::whereDate('created_at',$date)->sum('quantity') ?? 0;
            $dailyOrder[] = Order::whereDate('created_at',$date)->count();
        }

        // Mingguan (bulan ini)
        $startOfMonth = Carbon::now()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $endOfMonth = Carbon::now()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        for($weekStart=$startOfMonth->copy(); $weekStart->lte($endOfMonth); $weekStart->addWeek()){
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
            $weeklyLabels[] = 'Minggu '.$weekStart->format('W');
            $weeklyMasuk[] = Item_in::whereBetween('created_at',[$weekStart,$weekEnd])->sum('quantity') ?? 0;
            $weeklyOrder[] = Order::whereBetween('created_at',[$weekStart,$weekEnd])->count();
        }

        // Bulanan (tahun ini)
        for($m=1;$m<=12;$m++){
            $monthlyLabels[] = Carbon::create()->month($m)->format('M');
            $monthlyMasuk[] = Item_in::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',$m)->sum('quantity') ?? 0;
            $monthlyOrder[] = Order::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',$m)->count();
        }

        // Triwulan
        for($i=0;$i<4;$i++){
            $start = Carbon::create(Carbon::now()->year,($i*3)+1,1)->startOfMonth();
            $end = $start->copy()->addMonths(2)->endOfMonth();
            $triwulanMasuk[] = Item_in::whereBetween('created_at',[$start,$end])->sum('quantity') ?? 0;
            $triwulanOrder[] = Order::whereBetween('created_at',[$start,$end])->count();
        }

        // Semester
        for($i=0;$i<2;$i++){
            $start = Carbon::create(Carbon::now()->year,($i*6)+1,1)->startOfMonth();
            $end = $start->copy()->addMonths(5)->endOfMonth();
            $semesterMasuk[] = Item_in::whereBetween('created_at',[$start,$end])->sum('quantity') ?? 0;
            $semesterOrder[] = Order::whereBetween('created_at',[$start,$end])->count();
        }

        // Tahunan (5 tahun terakhir)
        $startYear = Carbon::now()->year - 4;
        $endYear = Carbon::now()->year;
        for($y=$startYear;$y<=$endYear;$y++){
            $yearlyLabels[] = $y;
            $yearlyMasuk[] = Item_in::whereYear('created_at',$y)->sum('quantity') ?? 0;
            $yearlyOrder[] = Order::whereYear('created_at',$y)->count();
        }

        // ============================
        // 🔹 GROWTH
        // ============================
        $thisMonth = $monthlyMasuk[Carbon::now()->month-1] ?? 0;
        $lastMonth = $monthlyMasuk[Carbon::now()->month-2] ?? 0;
        $growth = ($lastMonth>0) ? (($thisMonth-$lastMonth)/$lastMonth)*100 : 0;

        // ============================
        // 🔹 RETURN KE VIEW
        // ============================
        return view('role.super_admin.dashboard', [
            'item' => $itemNow,
            'suppliers' => $supplierNow,
            'users' => $userNow,
            'guests' => $guestNow,
            'orders' => $ordersNow,          
            'itemDiff' => $itemDiff,
            'supplierDiff' => $supplierDiff,
            'userDiff' => $userDiff,
            'guestDiff' => $guestDiff,
            'orderDiff' => $orderDiff,       
            'itemIns' => $itemIns,
            'lowStockItems' => $lowStockItems,
            'dailyLabels' => $dailyLabels,
            'dailyMasuk' => $dailyMasuk,
            'dailyOrder' => $dailyOrder,
            'weeklyLabels' => $weeklyLabels,
            'weeklyMasuk' => $weeklyMasuk,
            'weeklyOrder' => $weeklyOrder,
            'monthlyLabels' => $monthlyLabels,
            'monthlyMasuk' => $monthlyMasuk,
            'monthlyOrder' => $monthlyOrder,
            'triwulanLabels' => $triwulanLabels,
            'triwulanMasuk' => $triwulanMasuk,
            'triwulanOrder' => $triwulanOrder,
            'semesterLabels' => $semesterLabels,
            'semesterMasuk' => $semesterMasuk,
            'semesterOrder' => $semesterOrder,
            'yearlyLabels' => $yearlyLabels,
            'yearlyMasuk' => $yearlyMasuk,
            'yearlyOrder' => $yearlyOrder,
            'growth' => $growth,
        ]);
    }
}
