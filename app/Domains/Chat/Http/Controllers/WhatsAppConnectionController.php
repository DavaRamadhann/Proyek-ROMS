<?php

namespace App\Domains\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Controller ini bertindak sebagai jembatan (proxy) aman antara
 * frontend ROMS (admin dashboard) dan whatsapp-service API.
 * Ini memastikan API Key layanan WA tidak terekspos ke browser.
 */
class WhatsAppConnectionController extends Controller
{
    private $waServiceUrl;
    private $waServiceApiKey;
    private $clientId = 'official_business'; // ID Klien terpusat kita

    /**
     * Muat konfigurasi dari .env saat controller di-inisialisasi.
     */
    public function __construct()
    {
        // Ambil URL dan API Key dari config/services.php
        $this->waServiceUrl = rtrim(config('services.whatsapp.url'), '/');
        $this->waServiceApiKey = config('services.whatsapp.api_key');
    }

    /**
     * Helper privat untuk membuat instance HTTP Client
     * yang sudah terautentikasi dengan API Key.
     */
    private function waClient()
    {
        if (!$this->waServiceUrl || !$this->waServiceApiKey) {
            Log::error('WA Service URL atau API Key belum diatur di config/services.php');
            throw new \Exception('WhatsApp Service tidak terkonfigurasi.');
        }

        // Menggunakan API key sesuai yang diharapkan oleh whatsapp-service
        return Http::withHeaders([
            'x-api-key' => $this->waServiceApiKey,
            'Accept' => 'application/json',
        ])->timeout(15); // Timeout 15 detik
    }

    /**
     * METHOD 1: Mengambil status koneksi saat ini.
     * Akan memanggil: GET /accounts
     */
    public function getStatus(): JsonResponse
    {
        try {
            // Panggil endpoint /accounts di whatsapp-service
            $response = $this->waClient()->get("{$this->waServiceUrl}/accounts");

            if ($response->failed()) {
                return response()->json(['status' => 'SERVICE_DOWN', 'message' => 'WA Service tidak merespon.'], 500);
            }

            // Endpoint /accounts mengembalikan array
            // Kita cari data untuk clientId 'official_business'
            $account = collect($response->json())->firstWhere('clientId', $this->clientId);

            if (!$account) {
                // Jika belum ada, anggap sebagai DISCONNECTED
                return response()->json(['status' => 'DISCONNECTED', 'message' => 'Client ID belum terdaftar di service.']);
            }

            // Kembalikan status yang ditemukan (mis: 'READY', 'QR', 'DISCONNECTED')
            return response()->json([
                'status' => $account['status'] ?? 'UNKNOWN',
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal mengambil status WA: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * METHOD 2: Meminta dan mengambil QR code yang aktif.
     * Akan memanggil: GET /accounts/{clientId}/qr
     */
    public function getQrCode(): JsonResponse
    {
        try {
            $url = "{$this->waServiceUrl}/accounts/{$this->clientId}/qr";
            // Panggil endpoint /qr
            $response = $this->waClient()->get($url);

            if ($response->status() === 404) {
                // 404 berarti tidak ada QR (mungkin sudah ready)
                return response()->json(['error' => 'Tidak ada QR Code aktif.'], 404);
            }

            if ($response->failed()) {
                return response()->json(['error' => 'Gagal mengambil QR code.'], $response->status());
            }

            // Sukses, kirim data { clientId, qr } ke frontend
            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Gagal mengambil QR WA: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * METHOD 3: Meminta whatsapp-service untuk generate QR baru.
     * Akan memanggil: POST /accounts/{clientId}/reconnect
     */
    public function requestReconnect(): JsonResponse
    {
        try {
            $url = "{$this->waServiceUrl}/accounts/{$this->clientId}/reconnect";
            // Panggil endpoint /reconnect
            $response = $this->waClient()->post($url);

            if ($response->failed()) {
                return response()->json(['error' => 'Gagal meminta reconnect.'], $response->status());
            }

            // Sukses, kembalikan status baru (biasanya status 'QR')
            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Gagal reconnect WA: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], 500);
        }
    }
}