<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domains\Order\Models\Order;
use App\Domains\Customer\Models\Customer;

class ReportController extends Controller
{
    /**
     * Menampilkan Dashboard Laporan
     */
    public function index()
    {
        // 1. Top 10 Pelanggan Loyal (Berdasarkan Total Belanja)
        // Asumsi relasi order ke customer ada
        // Kita hitung manual jika belum ada kolom total_spent di customer
        // 1. Top 10 Pelanggan Loyal (Berdasarkan Frekuensi Order - Sesuai PRD)
        $topCustomers = Order::select('customer_id', DB::raw('COUNT(id) as order_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->with('customer')
            ->where('status', '!=', 'cancelled')
            ->groupBy('customer_id')
            ->orderByDesc('order_count') // Prioritas: Frekuensi
            ->orderByDesc('total_spent') // Secondary: Total Belanja
            ->limit(10)
            ->get();

        // 2. Sebaran Geografis (Berdasarkan Kota)
        // Menggunakan kolom 'city' yang sudah ada di tabel customers
        $geoDistribution = Customer::select('city', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('pages.report.index', compact('topCustomers', 'geoDistribution'));
    }
}
