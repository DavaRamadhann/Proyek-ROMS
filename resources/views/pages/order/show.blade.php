@extends('layout.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('order.index') }}" class="btn btn-outline-secondary">&laquo; Kembali</a>
            
            {{-- Form Ganti Status --}}
            <form action="{{ route('order.update-status', $order->id) }}" method="POST" class="d-flex gap-2">
                @csrf
                <select name="status" class="form-select form-select-sm">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
            </form>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between">
                <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                <span class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Info Pelanggan</h6>
                        <p class="mb-0">{{ $order->customer->name }}</p>
                        <p class="mb-0 text-muted">{{ $order->customer->phone }}</p>
                        <p class="mb-0 text-muted">{{ $order->customer->address }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="fw-bold">Status Pesanan</h6>
                        <h3>
                            @if($order->status == 'shipped')
                                <span class="badge bg-info text-dark">SEDANG DIKIRIM</span>
                            @elseif($order->status == 'completed')
                                <span class="badge bg-success">SELESAI</span>
                            @else
                                <span class="badge bg-secondary">{{ strtoupper($order->status) }}</span>
                            @endif
                        </h3>
                        @if($order->shipped_at)
                            <small class="text-muted">Dikirim pada: {{ $order->shipped_at->format('d M Y') }}</small>
                        @endif
                    </div>
                </div>

                <h6 class="fw-bold">Rincian Barang</h6>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">GRAND TOTAL</td>
                            <td class="text-end fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer text-end">
                <form action="{{ route('order.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus pesanan ini permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Hapus Pesanan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection