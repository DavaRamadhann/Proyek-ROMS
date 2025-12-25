<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ApiIntegrationController extends Controller
{
    /**
     * Menampilkan halaman dokumentasi Integrasi API
     */
    public function index()
    {
        $apiUrl = url('/api/v1/orders');
        $webhookUrl = url('/api/v1/webhooks');
        
        // Get current API key from env or config
        $apiKey = config('app.api_key', env('API_KEY', 'someah-secret-key'));
        
        // Get API usage statistics (if you have a logs table)
        $apiStats = $this->getApiStatistics();
        
        return view('pages.api.index', compact('apiUrl', 'webhookUrl', 'apiKey', 'apiStats'));
    }

    /**
     * Generate a new API key
     */
    public function generateApiKey(Request $request)
    {
        // Generate a secure random API key
        $newApiKey = 'roms_' . Str::random(40);
        
        // Here you should store this in a database table for production
        // For now, we'll just return it to be manually added to .env
        
        return response()->json([
            'success' => true,
            'api_key' => $newApiKey,
            'message' => 'API Key baru berhasil dibuat. Simpan di file .env Anda dengan key API_KEY'
        ]);
    }

    /**
     * Get API usage statistics
     */
    private function getApiStatistics()
    {
        // This is a placeholder - in production you'd track actual API calls
        return [
            'total_requests' => 0,
            'today_requests' => 0,
            'last_request' => null,
            'success_rate' => 100
        ];
    }
}
