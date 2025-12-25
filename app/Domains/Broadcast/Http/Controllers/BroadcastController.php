<?php

namespace App\Domains\Broadcast\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Broadcast\Models\Broadcast;
use App\Domains\Broadcast\Jobs\ProcessBroadcast;
use App\Domains\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = Broadcast::with('creator')->latest()->paginate(10);
        return view('pages.broadcast.index', compact('broadcasts'));
    }

    public function create()
    {
        $templates = \App\Domains\Message\Models\MessageTemplate::all();
        return view('pages.broadcast.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message_content' => 'required|string',
            'target_segment' => 'required|string',
        ]);

        if ($request->scheduled_at) {
            $scheduledTime = \Carbon\Carbon::parse($request->scheduled_at);
            if ($scheduledTime->lte(now())) {
                return back()->with('error', 'Waktu jadwal harus lebih besar dari waktu sekarang (minimal 1 menit kedepan).');
            }
        }

        DB::beginTransaction();
        try {
            $attachmentUrl = null;
            $attachmentType = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('broadcast_attachments', 'public');
                $attachmentUrl = asset('storage/' . $path);
                
                $mime = $file->getMimeType();
                $attachmentType = str_contains($mime, 'image') ? 'image' : 'document';
            }

            $status = $request->scheduled_at ? 'scheduled' : 'processing';

            $broadcast = Broadcast::create([
                'name' => $request->name,
                'message_content' => $request->message_content,
                'target_segment' => $request->target_segment,
                'scheduled_at' => $request->scheduled_at,
                'attachment_url' => $attachmentUrl,
                'attachment_type' => $attachmentType,
                'status' => $status,
                'created_by' => auth()->id(),
            ]);

            // Dispatch job ONLY if not scheduled
            if (!$request->scheduled_at) {
                ProcessBroadcast::dispatch($broadcast);
            }

            DB::commit();

            return redirect()->route('broadcast.index')->with('success', 'Broadcast berhasil dibuat dan sedang diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat broadcast: ' . $e->getMessage());
        }
    }

    public function show(Broadcast $broadcast)
    {
        $broadcast->load(['logs.customer', 'creator']);
        return view('pages.broadcast.show', compact('broadcast'));
    }

    public function processScheduled()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('broadcast:process-scheduled');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            return back()->with('success', 'Perintah pemrosesan jadwal berhasil dijalankan. Output: ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menjalankan perintah: ' . $e->getMessage());
        }
    }

    public function destroy(Broadcast $broadcast)
    {
        try {
            $broadcastName = $broadcast->name;
            $broadcast->delete(); // Cascade delete logs via foreign key constraint
            
            return redirect()->route('broadcast.index')
                ->with('success', "Broadcast '{$broadcastName}' berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus broadcast: ' . $e->getMessage());
        }
    }
}
