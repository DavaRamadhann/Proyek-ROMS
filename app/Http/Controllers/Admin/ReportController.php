<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domains\Order\Models\Order;
use App\Domains\Customer\Models\Customer;
use App\Domains\Product\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Menampilkan Dashboard Laporan
     */
    public function index()
    {
        // 1. Top 10 Pelanggan Loyal
        $topCustomers = Order::select('customer_id', DB::raw('COUNT(id) as order_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->with('customer')
            ->where('status', '!=', 'cancelled')
            ->groupBy('customer_id')
            ->orderByDesc('order_count')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // 2. Sebaran Geografis - Extract from address
        $geoDistribution = $this->extractGeographicDistribution();

        // 3. Summary Statistics
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // 4. Top Products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return view('pages.report.index', compact(
            'topCustomers', 
            'geoDistribution', 
            'totalRevenue', 
            'totalOrders', 
            'totalCustomers',
            'totalProducts',
            'avgOrderValue',
            'topProducts'
        ));
    }

    /**
     * Extract geographic distribution from customer addresses
     */
    private function extractGeographicDistribution()
    {
        $customers = Customer::whereNotNull('address')
            ->where('address', '!=', '')
            ->get();

        $cityCount = [];

        foreach ($customers as $customer) {
            $city = $this->extractCityFromAddress($customer->address);
            if ($city) {
                if (!isset($cityCount[$city])) {
                    $cityCount[$city] = 0;
                }
                $cityCount[$city]++;
            }
        }

        // Sort by count descending and take top 10
        arsort($cityCount);
        $cityCount = array_slice($cityCount, 0, 10, true);

        // Convert to collection format
        $result = collect();
        foreach ($cityCount as $city => $total) {
            $result->push((object)[
                'city' => $city,
                'total' => $total
            ]);
        }

        return $result;
    }

    /**
     * Extract city name from address string
     */
    private function extractCityFromAddress($address)
    {
        if (empty($address)) {
            return null;
        }

        $address = strtolower($address);

        // District/Kelurahan to City mapping (for common areas)
        $districtMapping = [
            'padasuka' => 'Bandung',
            'cimahi' => 'Bandung',
            'buah batu' => 'Bandung',
            'dago' => 'Bandung',
            'cileunyi' => 'Bandung',
            'baleendah' => 'Bandung',
            'ciparay' => 'Bandung',
            'soreang' => 'Bandung',
            'margahayu' => 'Bandung',
            'rancaekek' => 'Bandung',
            'kemang' => 'Jakarta',
            'kebayoran' => 'Jakarta',
            'menteng' => 'Jakarta',
            'kelapa gading' => 'Jakarta',
            'pondok indah' => 'Jakarta',
            'serpong' => 'Tangerang',
            'bsd' => 'Tangerang',
            'gading serpong' => 'Tangerang',
            'cikarang' => 'Bekasi',
            'tambun' => 'Bekasi',
            'wonokromo' => 'Surabaya',
            'gubeng' => 'Surabaya',
            'dukuh pakis' => 'Surabaya',
            'lowokwaru' => 'Malang',
            'klojen' => 'Malang',
        ];

        // Check district mapping first
        foreach ($districtMapping as $district => $city) {
            if (stripos($address, $district) !== false) {
                return $city;
            }
        }

        // Common city keywords in Indonesian addresses
        $cityKeywords = ['kota', 'kabupaten', 'kab.', 'kab'];
        
        // Try to find city using common patterns
        foreach ($cityKeywords as $keyword) {
            if (stripos($address, $keyword) !== false) {
                $pattern = '/' . preg_quote($keyword, '/') . '\s+([a-z\s]+)/i';
                if (preg_match($pattern, $address, $matches)) {
                    $cityName = trim($matches[1]);
                    $words = explode(' ', $cityName);
                    return ucwords(implode(' ', array_slice($words, 0, min(2, count($words)))));
                }
            }
        }

        // Look for common Indonesian city names
        $commonCities = [
            'jakarta', 'surabaya', 'bandung', 'medan', 'semarang', 
            'makassar', 'palembang', 'tangerang', 'depok', 'bekasi',
            'bogor', 'yogyakarta', 'jogja', 'malang', 'bali', 'denpasar',
            'balikpapan', 'pontianak', 'manado', 'samarinda', 'batam',
            'solo', 'surakarta', 'cirebon', 'sukabumi', 'tasikmalaya',
            'purwakarta', 'karawang', 'cianjur', 'garut', 'pekanbaru',
            'jambi', 'padang', 'banjarmasin', 'banjarbaru'
        ];

        foreach ($commonCities as $city) {
            if (stripos($address, $city) !== false) {
                return ucfirst($city);
            }
        }

        // Try to extract from comma-separated format
        $parts = array_map('trim', explode(',', $address));
        if (count($parts) >= 2) {
            // Check if second-to-last part contains a city name
            $potentialCity = $parts[count($parts) - 2];
            
            // Clean up (remove postal codes, etc)
            $potentialCity = preg_replace('/\d{5}/', '', $potentialCity);
            $potentialCity = trim($potentialCity);
            
            if (strlen($potentialCity) > 3 && strlen($potentialCity) < 30) {
                return ucwords($potentialCity);
            }
        }

        // Last resort: extract any capitalized word that might be a city
        if (preg_match('/([A-Z][a-z]+(?:\s+[A-Z][a-z]+)?)/', $address, $matches)) {
            return $matches[1];
        }

        return 'Lokasi Tidak Diketahui';
    }

    /**
     * Export comprehensive PDF report
     */
    public function exportPDF()
    {
        // Collect all analytics data
        $topCustomers = Order::select('customer_id', DB::raw('COUNT(id) as order_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->with('customer')
            ->where('status', '!=', 'cancelled')
            ->groupBy('customer_id')
            ->orderByDesc('order_count')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        $geoDistribution = $this->extractGeographicDistribution();

        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $recentOrders = Order::with('customer')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $data = compact(
            'topCustomers', 
            'geoDistribution', 
            'totalRevenue', 
            'totalOrders', 
            'totalCustomers',
            'totalProducts',
            'avgOrderValue',
            'topProducts',
            'recentOrders'
        );

        $pdf = Pdf::loadView('pages.report.pdf', $data)
            ->setPaper('a4', 'portrait');
        
        return $pdf->download('Laporan_Bisnis_' . now()->format('Y-m-d_His') . '.pdf');
    }
}
