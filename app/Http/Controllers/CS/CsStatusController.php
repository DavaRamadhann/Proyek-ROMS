<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CsStatusController extends Controller
{
    public function toggle(Request $request)
    {
        $user = auth()->user();
        
        // Toggle status using Query Builder to bypass model casting
        $newStatus = !$user->is_online;
        \App\Models\User::where('id', $user->id)->update(['is_online' => DB::raw($newStatus ? 'true' : 'false')]);
        
        // Refresh model
        $user->refresh();

        $status = $user->is_online ? 'Online' : 'Offline';
        $message = "Status Anda sekarang: $status";

        return back()->with('success', $message);
    }
}
