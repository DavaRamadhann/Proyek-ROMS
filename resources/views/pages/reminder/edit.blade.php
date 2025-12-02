@extends('layout.main')

@section('title', 'Edit Reminder - ROMS')

@section('main-content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Reminder</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reminders.update', $reminder->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Nama Reminder --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Reminder <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $reminder->name) }}" 
                               placeholder="Contoh: Reminder Biji Kopi 25 Hari" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Produk --}}
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Produk</label>
                        <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
                            <option value="">Semua Produk</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    {{ old('product_id', $reminder->product_id) == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kosongkan jika reminder berlaku untuk semua produk</small>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Hari Setelah Delivery --}}
                    <div class="mb-3">
                        <label for="days_after_delivery" class="form-label">Hari Setelah Delivery <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('days_after_delivery') is-invalid @enderror" 
                               id="days_after_delivery" name="days_after_delivery" 
                               value="{{ old('days_after_delivery', $reminder->days_after_delivery) }}" 
                               min="1" max="365" required>
                        <small class="text-muted">Reminder akan dikirim X hari setelah order delivered</small>
                        @error('days_after_delivery')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Template Pesan --}}
                    <div class="mb-3">
                        <label for="template_id" class="form-label">Pilih Template (Opsional)</label>
                        <select class="form-select" id="template_id">
                            <option value="">-- Pilih Template --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->content }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="message_template" class="form-label">Template Pesan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('message_template') is-invalid @enderror" 
                                  id="message_template" name="message_template" 
                                  rows="6" required>{{ old('message_template', $reminder->message_template) }}</textarea>
                        <small class="text-muted">
                            <strong>Variable yang tersedia:</strong> 
                            <code>{customer_name}</code>, 
                            <code>{product_name}</code>, 
                            <code>{order_date}</code>, 
                            <code>{days_since}</code>
                        </small>
                        @error('message_template')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <script>
                        document.getElementById('template_id').addEventListener('change', function() {
                            const content = this.value;
                            if (content) {
                                document.getElementById('message_template').value = content;
                            }
                        });
                    </script>

                    {{-- Status Aktif --}}
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                               {{ old('is_active', $reminder->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Aktifkan reminder ini
                        </label>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>Update Reminder
                        </button>
                        <a href="{{ route('reminders.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection