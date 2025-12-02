@extends('layout.main')

@section('title', 'Buat Pesanan Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">Buat Pesanan Baru</h2>
        <p class="text-muted">Input manual pesanan dari pelanggan.</p>
    </div>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('orders.store') }}" method="POST" id="orderForm">
    @csrf
    <div class="row g-4">
        <!-- Kolom Kiri: Info Pelanggan -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0" style="color: #84994F;">
                        <i class="bi bi-person-circle me-2"></i>Informasi Pelanggan
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div class="mb-4">
                        <label for="customer_id" class="form-label fw-semibold">Pilih Pelanggan</label>
                        <select name="customer_id" id="customer_id" class="form-select form-select-lg" required>
                            <option value="">-- Cari Pelanggan --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pastikan pelanggan sudah terdaftar.</div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Catatan Pesanan</label>
                        <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Contoh: Dikirim siang hari, bungkus kado, dll."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Item Pesanan -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0" style="color: #B45253;">
                        <i class="bi bi-cart-fill me-2"></i>Item Pesanan
                    </h5>
                    <button type="button" class="btn btn-sm btn-success text-white" id="addItemBtn" style="background-color: #84994F; border-color: #84994F;">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle" id="itemsTable">
                            <thead class="text-muted border-bottom">
                                <tr>
                                    <th width="40%">Produk</th>
                                    <th width="20%">Harga (Rp)</th>
                                    <th width="15%">Qty</th>
                                    <th width="20%" class="text-end">Subtotal</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsContainer">
                                <!-- Items will be added here via JS -->
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="3" class="text-end pt-4">
                                        <span class="text-muted">Total Pembayaran:</span>
                                    </td>
                                    <td colspan="2" class="pt-4 text-end">
                                        <h4 class="fw-bold text-primary mb-0" id="grandTotal" style="color: #B45253 !important;">Rp 0</h4>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5" style="background-color: #B45253; border-color: #B45253; border-radius: 50px;">
                        <i class="bi bi-save me-2"></i> Simpan Pesanan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<template id="itemRowTemplate">
    <tr class="item-row border-bottom">
        <td class="py-3">
            <select name="items[{index}][product_name]" class="form-select product-select" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($products as $product)
                    <option value="{{ $product->name }}" data-price="{{ $product->price ?? 0 }}">
                        {{ $product->name }}
                    </option>
                @endforeach
                <option value="custom" class="fw-bold text-primary">+ Produk Custom</option>
            </select>
            <input type="text" name="items[{index}][product_name_custom]" class="form-control mt-2 d-none custom-product-input" placeholder="Nama produk custom...">
        </td>
        <td class="py-3">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">Rp</span>
                <input type="number" name="items[{index}][price]" class="form-control price-input border-start-0 ps-1" min="0" value="0" required>
            </div>
        </td>
        <td class="py-3">
            <input type="number" name="items[{index}][quantity]" class="form-control qty-input text-center" min="1" value="1" required>
        </td>
        <td class="subtotal-display text-end fw-bold py-3" style="color: #555;">Rp 0</td>
        <td class="text-end py-3">
            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle remove-item-btn" title="Hapus Item">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    const container = document.getElementById('itemsContainer');
    const template = document.getElementById('itemRowTemplate').innerHTML;
    const addItemBtn = document.getElementById('addItemBtn');
    const grandTotalEl = document.getElementById('grandTotal');

    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('#itemsContainer tr').forEach(row => {
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const subtotal = price * qty;
            
            row.querySelector('.subtotal-display').textContent = formatRupiah(subtotal);
            total += subtotal;
        });
        grandTotalEl.textContent = formatRupiah(total);
    }

    function addItem() {
        const html = template.replace(/{index}/g, itemIndex++);
        container.insertAdjacentHTML('beforeend', html);
        calculateTotal();
    }

    // Add initial item
    addItem();

    addItemBtn.addEventListener('click', addItem);

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item-btn')) {
            e.target.closest('tr').remove();
            calculateTotal();
        }
    });

    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('tr');
            const customInput = row.querySelector('.custom-product-input');
            const priceInput = row.querySelector('.price-input');
            
            if (e.target.value === 'custom') {
                customInput.classList.remove('d-none');
                customInput.required = true;
                e.target.removeAttribute('name'); // Don't submit select value
                customInput.setAttribute('name', `items[${row.rowIndex - 1}][product_name]`);
                priceInput.value = 0;
            } else {
                customInput.classList.add('d-none');
                customInput.required = false;
                customInput.removeAttribute('name');
                e.target.setAttribute('name', `items[${row.rowIndex - 1}][product_name]`);
                
                // Set dummy price or fetch real price if available (currently 0 from option data)
                // In a real app, you'd put the price in data-price attribute
                const selectedOption = e.target.options[e.target.selectedIndex];
                // priceInput.value = selectedOption.dataset.price || 0; 
            }
            calculateTotal();
        }
    });

    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('price-input') || e.target.classList.contains('qty-input')) {
            calculateTotal();
        }
    });
});
</script>
@endpush