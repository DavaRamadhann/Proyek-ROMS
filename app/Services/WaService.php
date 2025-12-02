<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaService
{
    protected $baseUrl;
    protected $apiKey;
    protected $clientId;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.whatsapp.url'), '/');
        $this->apiKey = config('services.whatsapp.api_key');
        $this->clientId = 'official_business'; // Bisa dibuat config juga jika perlu
    }

    /**
     * Kirim pesan teks (atau dengan attachment link) ke nomor tujuan.
     */
    public function sendMessage(string $to, string $message, ?string $attachmentUrl = null)
    {
        // Jika ada attachment, kita gabungkan ke text untuk sementara (fallback)
        // Idealnya API support media endpoint terpisah atau parameter media
        $finalText = $message;
        if ($attachmentUrl) {
            $finalText .= "\n\n[Attachment]: " . $attachmentUrl;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/messages", [
                'clientId' => $this->clientId,
                'to' => $to,
                'text' => $finalText,
            ]);

            if ($response->failed()) {
                Log::error("WaService Error (HTTP {$response->status()}): {$response->body()}");
                throw new \Exception("Gagal mengirim pesan ke {$to}: " . $response->body());
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('WaService Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
