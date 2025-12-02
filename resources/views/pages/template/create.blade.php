@extends('layout.main')

@section('title', isset($template) ? 'Edit Template' : 'Buat Template Baru')

@section('main-content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="fw-bold text-dark mb-0">{{ isset($template) ? 'Edit Template' : 'Buat Template Baru' }}</h2>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ isset($template) ? route('admin.templates.update', $template->id) : route('admin.templates.store') }}" method="POST">
                        @csrf
                        @if(isset($template))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Template</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $template->name ?? '') }}" placeholder="Contoh: Promo Gajian, Reminder H-3" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipe Penggunaan</label>
                            <select name="type" class="form-select" required>
                                <option value="general" {{ (old('type', $template->type ?? '') == 'general') ? 'selected' : '' }}>Umum</option>
                                <option value="broadcast" {{ (old('type', $template->type ?? '') == 'broadcast') ? 'selected' : '' }}>Broadcast Kampanye</option>
                                <option value="reminder" {{ (old('type', $template->type ?? '') == 'reminder') ? 'selected' : '' }}>Reminder Otomatis</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Konten Pesan</label>
                            <div class="mb-2">
                                <small class="text-muted me-2">Klik variabel untuk menyisipkan:</small>
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1 mb-1" onclick="insertVar('{customer_name}')">{customer_name}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1 mb-1" onclick="insertVar('{order_number}')">{order_number}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1 mb-1" onclick="insertVar('{product_name}')">{product_name}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1 mb-1" onclick="insertVar('{days_since}')">{days_since}</button>
                            </div>
                            <textarea name="content" id="contentArea" rows="8" class="form-control" placeholder="Tulis pesan Anda di sini..." required>{{ old('content', $template->content ?? '') }}</textarea>
                            <div class="form-text">Variabel akan otomatis diganti dengan data pelanggan saat pesan dikirim.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.templates.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Template</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function insertVar(text) {
        const textarea = document.getElementById('contentArea');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const textBefore = textarea.value.substring(0, start);
        const textAfter = textarea.value.substring(end, textarea.value.length);
        
        textarea.value = textBefore + text + textAfter;
        textarea.selectionStart = textarea.selectionEnd = start + text.length;
        textarea.focus();
    }
</script>
@endpush
@endsection
