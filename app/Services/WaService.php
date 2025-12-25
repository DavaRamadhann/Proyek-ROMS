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
    public function sendMessage(string $to, string $message, ?string $attachmentUrl = null, ?string $attachmentType = null, ?string $attachmentName = null)
    {
        $payload = [
            'clientId' => $this->clientId,
            'to' => $to,
            'text' => $message,
        ];

        if ($attachmentUrl) {
            // Use the attachment URL directly as we are running locally
            $dockerUrl = $attachmentUrl;
            
            $mediaPayload = ['url' => $dockerUrl];
            if ($attachmentName) {
                $mediaPayload['filename'] = $attachmentName;
            }
            
            $payload['media'] = $mediaPayload;
            
            // Use the message as caption if media is present
            if (!empty($message)) {
                $payload['options'] = ['caption' => $message];
            }

            // Set specific media options based on type
            if ($attachmentType === 'document') {
                $payload['options']['sendMediaAsDocument'] = true;
            }
        }

        try {
            $response = Http::timeout(120)->withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/messages", $payload);

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

    /**
     * Get connection status from WhatsApp service
     */
    public function getConnectionStatus()
    {
        try {
            $response = Http::timeout(5)->withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/accounts");

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                $account = collect($data)->firstWhere('clientId', $this->clientId);
                return $account['status'] ?? 'DISCONNECTED';
            }
            
            return 'DISCONNECTED';
        } catch (\Exception $e) {
            Log::error('WaService Status Check Error: ' . $e->getMessage());
            return 'DISCONNECTED';
        }
    }
}
