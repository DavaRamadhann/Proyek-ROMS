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

        // 2. CEK: Apakah ini event ACK (Status Update)?
        if ($request->input('type') === 'ack') {
            return $this->handleAck($request);
        }

        // 3. CEK AWAL: Apakah ini chat beneran?
        if (!$request->has('message_body') || !$request->input('message_body')) {
            return response()->json(['status' => 'ignored', 'message' => 'Not a chat message (ACK/Status)']);
        }

        // 4. Validasi Manual Chat
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
            // 5. Lempar ke Service Utama
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

    /**
     * Handle Message Acknowledgment (Status Update)
     */
    protected function handleAck(Request $request): JsonResponse
    {
        $ack = $request->input('ack'); // 1=SENT, 2=DELIVERED, 3=READ
        $messageId = $request->input('message_id'); // e.g., "true_628123456789@c.us_3EB0..."

        if (!$ack || !$messageId) {
            return response()->json(['status' => 'ignored', 'message' => 'Missing ack or message_id']);
        }

        // Map ACK to Status String
        $status = 'pending';
        switch ($ack) {
            case 1: $status = 'sent'; break;
            case 2: $status = 'delivered'; break;
            case 3: $status = 'read'; break;
            case 4: $status = 'read'; break; // Played (Voice Note) -> Read
            case -1: $status = 'failed'; break;
            default: return response()->json(['status' => 'ignored', 'message' => 'Unknown ACK code']);
        }

        try {
            // Update status via Service
            // Note: We need a method in ChatService for this, or call Repo directly.
            // For now, let's assume we can add handleAck to ChatService or do it here.
            // Doing it here for simplicity if ChatService doesn't have it yet, 
            // but better to delegate to Service.
            $this->chatService->handleMessageStatusUpdate($messageId, $status);

            return response()->json(['status' => 'success', 'message' => "Status updated to $status"]);

        } catch (\Exception $e) {
            Log::error('❌ Error Processing ACK: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}