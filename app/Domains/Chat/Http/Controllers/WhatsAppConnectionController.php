<?php

namespace App\Domains\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Chat\Models\ChatRoom;
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
        ])->timeout(90); // Timeout 15 detik
    }

    /**
     * Helper privat untuk mengambil data akun dari WA Service.
     */
    private function getAccountData()
    {
        try {
            $response = $this->waClient()->get("{$this->waServiceUrl}/accounts");

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                return collect($data)->firstWhere('clientId', $this->clientId);
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data akun WA: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * METHOD 1: Mengambil status koneksi saat ini.
     * Akan memanggil: GET /accounts
     */
    public function getStatus(): JsonResponse
    {
        $account = $this->getAccountData();

        if (!$account) {
            return response()->json([
                'success' => true,
                'account' => [
                    'status' => 'DISCONNECTED'
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'account' => $account
        ]);
    }

    /**
     * METHOD 2: Meminta dan mengambil QR code yang aktif.
     * Akan memanggil: GET /accounts/{clientId}/qr
     */
    public function getQrCode(): JsonResponse
    {
        try {
            $url = "{$this->waServiceUrl}/accounts/{$this->clientId}/qr";
            $response = $this->waClient()->get($url);

            if ($response->status() === 404) {
                return response()->json(['error' => 'Tidak ada QR Code aktif.'], 404);
            }

            if ($response->failed()) {
                return response()->json(['error' => 'Gagal mengambil QR code.'], $response->status());
            }

            return response()->json($response->json()['data']);

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
            $response = $this->waClient()->post($url);

            if ($response->failed()) {
                return response()->json(['error' => 'Gagal meminta reconnect.'], $response->status());
            }

            return response()->json(['success' => true, 'data' => $response->json()]);

        } catch (\Exception $e) {
            Log::error('Gagal reconnect WA: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * METHOD 4: Memulai sesi WhatsApp baru (Start).
     * Akan memanggil: POST /accounts
     */
    /**
     * METHOD 4: Memulai sesi WhatsApp baru (Start).
     * Akan memanggil: POST /sessions (Updated from /accounts)
     */
    public function start(): JsonResponse
    {
        try {
            // [FIX] Endpoint yang benar adalah /sessions, bukan /accounts
            $url = "{$this->waServiceUrl}/sessions";
            $response = $this->waClient()->post($url, [
                'clientId' => $this->clientId
            ]);

            if ($response->failed()) {
                // Jika gagal, coba reconnect siapa tahu sudah ada
                if ($response->status() === 409) { // Conflict / Already exists
                    return $this->requestReconnect();
                }
                
                Log::error("WA Service Start Failed: {$response->status()} - {$response->body()}");
                
                return response()->json([
                    'error' => 'Gagal memulai service WhatsApp (Upstream Error).',
                    'details' => $response->json() ?? $response->body()
                ], 502);
            }

            return response()->json(['success' => true, 'data' => $response->json()]);

        } catch (\Exception $e) {
            Log::error('Gagal start WA: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * METHOD 5: Memutuskan koneksi WhatsApp (Disconnect/Logout).
     * Akan memanggil: DELETE /accounts/{clientId}
     */
    public function disconnect(): JsonResponse
    {
        try {
            $url = "{$this->waServiceUrl}/accounts/{$this->clientId}";
            $response = $this->waClient()->delete($url);

            if ($response->failed() && $response->status() !== 404) {
                return response()->json(['error' => 'Gagal memutuskan koneksi.'], $response->status());
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Gagal disconnect WA: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * METHOD 6: Clear all chat data (rooms and messages)
     * Use when switching WhatsApp accounts
     */
    public function clearAllChats(): JsonResponse
    {
        try {
            \DB::beginTransaction();
            
            // Delete all chat messages
            \App\Domains\Chat\Models\ChatMessage::truncate();
            
            // Delete all chat rooms
            \App\Domains\Chat\Models\ChatRoom::truncate();
            
            // Clear stored phone number
            \DB::table('system_settings')
                ->where('key', 'whatsapp_connected_number')
                ->delete();
            
            \DB::commit();
            
            Log::info('All chat data cleared successfully');
            
            return response()->json([
                'success' => true,
                'message' => 'Semua chat berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Gagal menghapus chat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store connected WhatsApp phone number
     */
    private function storeConnectedNumber(string $phoneNumber): void
    {
        \DB::table('system_settings')->updateOrInsert(
            ['key' => 'whatsapp_connected_number'],
            [
                'value' => $phoneNumber,
                'updated_at' => now()
            ]
        );
    }

    /**
     * Get currently connected WhatsApp phone number
     */
    public function getConnectedNumber(): ?string
    {
        $setting = \DB::table('system_settings')
            ->where('key', 'whatsapp_connected_number')
            ->first();
        
        return $setting ? $setting->value : null;
    }

    public function index()
    {
        $account = $this->getAccountData();
        $waServiceUrl = $this->waServiceUrl;
        $connectedNumber = $this->getConnectedNumber();
        
        // Store phone number if connected
        if ($account && isset($account['phoneNumber']) && $account['status'] === 'CONNECTED') {
            $this->storeConnectedNumber($account['phoneNumber']);
        }
        
        // Jika account null (misal service mati), kita buat dummy object agar view tidak error
        if (!$account) {
            $account = ['status' => 'DISCONNECTED'];
        }

        return view('pages.whatsapp.qr', compact('account', 'waServiceUrl', 'connectedNumber'));
    }
}