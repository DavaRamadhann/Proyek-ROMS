@extends('layout.main')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Buat Broadcast Baru</h1>


    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i> Form Broadcast
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('broadcast.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Judul Broadcast</label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Contoh: Promo Lebaran 2024">
                </div>

                <div class="mb-3">
                    <label for="target_segment" class="form-label">Target Segmen</label>
                    <select class="form-select" id="target_segment" name="target_segment" required>
                        <option value="all">Semua Pelanggan</option>
                        <option value="loyal">Pelanggan Loyal (> 2 Pesanan)</option>
                        <option value="inactive">Pelanggan Tidak Aktif (No Order > 90 Hari)</option>
                        <option value="new">Pelanggan Baru (Terdaftar < 30 Hari)</option>
                    </select>
                    <div class="form-text">Pilih kelompok pelanggan yang akan menerima pesan ini.</div>
                </div>

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
                    <label for="message_content" class="form-label">Isi Pesan</label>
                    <textarea class="form-control" id="message_content" name="message_content" rows="5" required placeholder="Halo {customer_name}, kami ada promo spesial..."></textarea>
                    <div class="form-text">Gunakan <strong>{customer_name}</strong> untuk menyisipkan nama pelanggan secara otomatis.</div>
                </div>

                <script>
                    document.getElementById('template_id').addEventListener('change', function() {
                        const content = this.value;
                        if (content) {
                            document.getElementById('message_content').value = content;
                        }
                    });
                </script>

                <div class="mb-3">
                    <label for="attachment" class="form-label">Lampiran (Opsional)</label>
                    <input class="form-control" type="file" id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx">
                    <div class="form-text">Bisa berupa gambar (JPG/PNG) atau dokumen (PDF).</div>
                </div>

                <div class="mb-3">
                    <label for="scheduled_at" class="form-label">Jadwalkan Pengiriman (Opsional)</label>
                    <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at">
                    <div class="form-text">Kosongkan jika ingin mengirim SEKARANG juga.</div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('broadcast.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Simpan & Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
