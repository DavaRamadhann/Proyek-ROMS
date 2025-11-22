<?php

namespace App\Domains\Chat\Http\Controllers;

use App\Domains\Chat\Services\ChatService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // <-- Tambah ini

class InboundWebhookController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function handle(Request $request): JsonResponse
    {
        // 1. Log payload mentah untuk debugging (biar kita tau isinya apa)
        Log::info('📥 Webhook Masuk:', $request->all());

        // 2. CEK AWAL: Apakah ini event status (ACK) atau chat beneran?
        // Kalau tidak ada 'message_body', kita anggap ini laporan status biasa.
        // Kita return 200 OK biar wa_service senang, tapi kita gak proses apa-apa.
        if (!$request->has('message_body') || !$request->input('message_body')) {
            return response()->json(['status' => 'ignored', 'message' => 'Not a chat message (ACK/Status)']);
        }

        // 3. Validasi Manual (Biar gak auto-throw 422 kalau ada format aneh)
        $validator = Validator::make($request->all(), [
            'from' => 'required|string',
            'message_body' => 'required|string',
            'sender_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('⚠️ Webhook Format Salah:', $validator->errors()->toArray());
            // Return 422 tapi JSON-nya jelas
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            // 4. Lempar ke Service Utama
            $this->chatService->handleInboundMessage($validatedData);

            return response()->json(['status' => 'success', 'message' => 'Message processed']);

        } catch (\Exception $e) {
            Log::error('❌ Error Processing Chat: ' . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}