@extends('layout.main')

@section('title', 'Detail Pesanan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Detail Pesanan</h2>
                <p class="text-muted mb-0">Informasi lengkap pesanan #{{ $order->order_number }}</p>
            </div>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header border-bottom-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background-color: #84994F; color: white;">
                <div>
                    <h5 class="mb-0 fw-bold">Order #{{ $order->order_number }}</h5>
                    <small style="opacity: 0.9;">Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }}</small>
                </div>
                <div>
                    @if($order->status == 'shipped')
                        <span class="badge bg-info text-dark px-3 py-2 rounded-pill">SEDANG DIKIRIM</span>
                    @elseif($order->status == 'completed')
                        <span class="badge bg-success px-3 py-2 rounded-pill">SELESAI</span>
                    @elseif($order->status == 'cancelled')
                        <span class="badge bg-danger px-3 py-2 rounded-pill">DIBATALKAN</span>
                    @else
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">{{ strtoupper($order->status) }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-5">
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Informasi Pelanggan</h6>
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="background-color: rgba(132, 153, 79, 0.1); width: 50px; height: 50px;">
                                <i class="bi bi-person-fill" style="color: #84994F; font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">{{ $order->customer->name }}</h5>
                                <p class="mb-1 text-muted"><i class="bi bi-telephone me-2"></i>{{ $order->customer->phone }}</p>
                                <p class="mb-0 text-muted"><i class="bi bi-geo-alt me-2"></i>{{ $order->customer->address ?? 'Alamat tidak tersedia' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-4 mt-md-0">
                        <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Status Pengiriman</h6>
                        @if($order->shipped_at)
                            <p class="mb-1 fw-bold text-success"><i class="bi bi-truck me-2"></i>Pesanan Telah Dikirim</p>
                            <small class="text-muted">Tanggal: {{ $order->shipped_at->format('d M Y') }}</small>
                        @else
                            <div class="alert alert-light d-inline-block text-start border mb-0">
                                <i class="bi bi-info-circle me-2 text-primary"></i>
                                Pesanan belum ditandai dikirim.
                            </div>
                        @endif
                        
                        {{-- Form Ganti Status --}}
                        <div class="mt-3">
                            <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="d-inline-flex gap-2">
                                @csrf
                                <select name="status" class="form-select form-select-sm" style="width: auto;">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm" style="background-color: #B45253; border-color: #B45253;">Update</button>
                            </form>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3" style="color: #333;">Rincian Item</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-borderless align-middle">
                        <thead style="background-color: #f8f9fa; border-bottom: 2px solid #eee;">
                            <tr>
                                <th class="py-3 ps-3">Produk</th>
                                <th class="py-3 text-center">Jumlah</th>
                                <th class="py-3 text-end">Harga Satuan</th>
                                <th class="py-3 text-end pe-3">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr class="border-bottom">
                                <td class="py-3 ps-3 fw-semibold">{{ $item->product_name }}</td>
                                <td class="py-3 text-center">{{ $item->quantity }}</td>
                                <td class="py-3 text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-3 text-end pe-3 fw-bold" style="color: #555;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background-color: #fcfcfc;">
                            <tr>
                                <td colspan="3" class="text-end py-4 fw-bold text-muted">GRAND TOTAL</td>
                                <td class="text-end py-4 pe-3">
                                    <h3 class="fw-bold mb-0" style="color: #B45253;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h3>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($order->notes)
                <div class="alert alert-secondary border-0 bg-light">
                    <h6 class="fw-bold mb-2"><i class="bi bi-sticky me-2"></i>Catatan Pesanan:</h6>
                    <p class="mb-0">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
            
            <div class="card-footer bg-white border-0 py-3 px-4 text-end">
                {{-- Tombol Hapus --}}
                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesanan ini permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash me-1"></i> Hapus Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection