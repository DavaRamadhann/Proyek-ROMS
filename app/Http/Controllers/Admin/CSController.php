<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CSController extends Controller
{
    /**
     * Display a listing of CS users.
     */
    public function index()
    {
        $csUsers = User::where('role', 'cs')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.cs.index', compact('csUsers'));
    }

    /**
     * Show the form for creating a new CS user.
     */
    public function create()
    {
        return view('admin.cs.create');
    }

    /**
     * Store a newly created CS user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'cs',
            'email_verified_at' => now(), // CS langsung terverifikasi
        ]);

        return redirect()->route('admin.cs.index')
            ->with('success', 'Akun CS berhasil didaftarkan!');
    }

    /**
     * Show the form for editing the specified CS user.
     */
    public function edit($id)
    {
        $csUser = User::where('role', 'cs')->findOrFail($id);
        return view('admin.cs.edit', compact('csUser'));
    }

    /**
     * Update the specified CS user in storage.
     */
    public function update(Request $request, $id)
    {
        $csUser = User::where('role', 'cs')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $csUser->name = $validated['name'];
        $csUser->email = $validated['email'];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $csUser->password = Hash::make($validated['password']);
        }

        $csUser->save();

        return redirect()->route('admin.cs.index')
            ->with('success', 'Data CS berhasil diperbarui!');
    }

    /**
     * Remove the specified CS user from storage.
     */
    public function destroy($id)
    {
        $csUser = User::where('role', 'cs')->findOrFail($id);
        $csUser->delete();

        return redirect()->route('admin.cs.index')
            ->with('success', 'Akun CS berhasil dihapus!');
    }
}
