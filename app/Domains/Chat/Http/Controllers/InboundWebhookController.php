<?php
// app/Domains/Chat/Http/Controllers/InboundWebhookController.php

namespace App\Domains\Chat\Http\Controllers;

use App\Domains\Chat\Services\ChatService;
use App\Http\Controllers\Controller; // Controller dasar Laravel
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log; // Pastikan Log di-import

class InboundWebhookController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Handle panggilan webhook dari wa-service.
     *
     */
    public function handle(Request $request): JsonResponse
    {
        // Validasi data (minimal)
        // [PERBAIKAN] Tambahkan 'sender_name' ke validasi
        $validatedData = $request->validate([
            'from' => 'required|string',
            'message_body' => 'required|string',
            'sender_name' => 'nullable|string|max:255', // Izinkan field nama
        ]);

        try {
            // Lempar data ke "otak" kita (ChatService)
            // ChatService.php (dari langkah kita sebelumnya) sudah siap menangani array ini
            $this->chatService->handleInboundMessage($validatedData);

            // Kirim balasan 200 OK ke wa-service
            return response()->json(['status' => 'success', 'message' => 'Message processed']);

        } catch (\Exception $e) {
            Log::error('Gagal memproses webhook WA: ' . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}