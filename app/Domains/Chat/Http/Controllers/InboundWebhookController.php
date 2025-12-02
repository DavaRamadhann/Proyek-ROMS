<?php

namespace App\Domains\Chat\Http\Controllers;

use App\Domains\Chat\Services\ChatService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InboundWebhookController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function handle(Request $request): JsonResponse
    {
        // 1. Log payload mentah untuk debugging
        Log::info('📥 Webhook Masuk:', $request->all());

        // 2. CEK AWAL: Apakah ini event status (ACK) atau chat beneran?
        if (!$request->has('message_body') || !$request->input('message_body')) {
            return response()->json(['status' => 'ignored', 'message' => 'Not a chat message (ACK/Status)']);
        }

        // 3. Validasi Manual
        $validator = Validator::make($request->all(), [
            'from' => 'required|string',
            'message_body' => 'required|string',
            'sender_name' => 'nullable|string|max:255',
            'pushName' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('⚠️ Webhook Format Salah:', $validator->errors()->toArray());
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