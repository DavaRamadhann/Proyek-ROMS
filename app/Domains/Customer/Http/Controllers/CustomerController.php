<?php

namespace App\Domains\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Customer\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // 1. Base Query with Eager Loading & Aggregates
        $query = Customer::query()
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total_amount');

        // 2. Role-based Filtering
        if ($user && $user->role !== 'admin') {
            $query->whereHas('chatRooms', function($q) use ($user) {
                $q->where('cs_user_id', $user->id);
            });
        }

        // 3. Search Filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // 4. Segment Filter
        if ($request->has('segment') && $request->segment != '' && $request->segment != 'Semua Segmen') {
            $segment = $request->segment;
            if ($segment == 'Big Spender') {
                // Fix for PostgreSQL: Use whereRaw instead of having on alias
                $query->whereRaw('(select coalesce(sum(total_amount), 0) from orders where orders.customer_id = customers.id) > ?', [1000000]);
            } elseif ($segment == 'Loyal') {
                // Fix: Use whereHas for count check
                $query->whereHas('orders', null, '>', 3);
            } elseif ($segment == 'Inactive') {
                $query->where('segment_tag', $segment);
            } elseif ($segment == 'New Member') {
                 $query->where('created_at', '>=', now()->subDays(30));
            }
        }

        // 5. Calculate Stats (Global / Scoped to User)
        $statsQuery = Customer::query();
        if ($user && $user->role !== 'admin') {
            $statsQuery->whereHas('chatRooms', function($q) use ($user) {
                $q->where('cs_user_id', $user->id);
            });
        }
        
        $totalCustomers = $statsQuery->count();
        
        // Fix for Big Spender Count
        $bigSpenderCount = (clone $statsQuery)
            ->whereRaw('(select coalesce(sum(total_amount), 0) from orders where orders.customer_id = customers.id) > ?', [1000000])
            ->count();

        $inactiveCount = (clone $statsQuery)
            ->whereDoesntHave('orders', function($q) {
                $q->where('created_at', '>=', now()->subDays(60));
            })->count();

        // 6. Pagination
        $customers = $query->orderBy('name', 'asc')->paginate(10);

        return view('pages.customer.index', compact('customers', 'totalCustomers', 'bigSpenderCount', 'inactiveCount'));
    }

    public function create(Request $request)
    {
        // Mendapatkan data dari query parameter (untuk auto-fill dari Chat)
        $prefillName = $request->query('name', '');
        $prefillPhone = $request->query('phone', '');

        return view('pages.customer.create', compact('prefillName', 'prefillPhone'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'is_manual_name' => true, // Flag as manually created/named
            'status' => 'active',
            'segment_tag' => 'New Member'
        ]);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('pages.customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Jika nama berubah manual, set flag is_manual_name = true
        if ($customer->name !== $validated['name']) {
            $customer->is_manual_name = true;
        }

        $customer->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diperbarui.',
                'customer' => $customer
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        try {
            // Log deletion attempt
            \Log::info('Attempting to delete customer', [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'user_id' => \Auth::id()
            ]);

            // Check if customer has orders
            $ordersCount = $customer->orders()->count();
            if ($ordersCount > 0) {
                \Log::warning('Customer deletion blocked: has orders', [
                    'customer_id' => $customer->id,
                    'orders_count' => $ordersCount
                ]);
                return redirect()->route('customers.index')
                    ->with('error', "Pelanggan tidak dapat dihapus karena memiliki {$ordersCount} riwayat pesanan.");
            }

            // Check if customer has chat rooms
            $chatRoomsCount = $customer->chatRooms()->count();
            if ($chatRoomsCount > 0) {
                \Log::warning('Customer deletion blocked: has chat rooms', [
                    'customer_id' => $customer->id,
                    'chat_rooms_count' => $chatRoomsCount
                ]);
                return redirect()->route('customers.index')
                    ->with('error', "Pelanggan tidak dapat dihapus karena memiliki riwayat chat.");
            }

            // Perform deletion
            $customerName = $customer->name;
            $customer->delete();

            \Log::info('Customer deleted successfully', [
                'customer_id' => $customer->id,
                'customer_name' => $customerName
            ]);

            return redirect()->route('customers.index')
                ->with('success', "Pelanggan \"{$customerName}\" berhasil dihapus.");

        } catch (\Exception $e) {
            \Log::error('Error deleting customer', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('customers.index')
                ->with('error', 'Terjadi kesalahan saat menghapus pelanggan. Silakan coba lagi.');
        }
    }

}
