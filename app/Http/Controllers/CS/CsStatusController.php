<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CsStatusController extends Controller
{
    public function toggle(Request $request)
    {
        $user = auth()->user();
        
        // Toggle status
        $user->update([
            'is_online' => !$user->is_online
        ]);

        $status = $user->is_online ? 'Online' : 'Offline';
        $message = "Status Anda sekarang: $status";

        return back()->with('success', $message);
    }
}
