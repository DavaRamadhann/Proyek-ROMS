@extends('layouts.app')

@section('title', 'Import Pesanan')

@section('content')
    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="upload-cloud" class="h-6 w-6 text-[#84994F]"></i> Import Pesanan
                </h1>
                <p class="text-sm text-slate-500 mt-1">Upload file CSV untuk membuat banyak pesanan sekaligus.</p>
            </div>
            <a href="{{ route('orders.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-1 transition">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </div>

        {{-- Alert Errors --}}
        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 flex items-start gap-3">
                <i data-lucide="alert-circle" class="h-5 w-5 mt-0.5 shrink-0"></i>
                <div>
                    <h3 class="font-bold text-sm">Gagal Import Data</h3>
                    <p class="text-xs mt-1 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700">
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 md:p-8">
                
                {{-- Step 1: Download Template --}}
                <div class="mb-8 border-b border-slate-100 pb-8">
                    <h2 class="text-lg font-bold text-slate-800 mb-2">1. Download Template CSV</h2>
                    <p class="text-sm text-slate-500 mb-4">Gunakan template ini untuk memastikan format data sesuai dengan sistem.</p>
                    
                    <a href="{{ route('orders.download-template') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900 rounded-lg text-sm font-bold transition">
                        <i data-lucide="download" class="h-4 w-4"></i> Download Template .CSV
                    </a>

                    <div class="mt-4 bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                        <h4 class="text-xs font-bold text-yellow-800 mb-2 uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="info" class="h-3 w-3"></i> Catatan Penting:
                        </h4>
                        <ul class="text-xs text-yellow-800/80 space-y-1.5 list-disc list-inside">
                            <li>Jangan ubah <strong>nama kolom header</strong> di baris pertama.</li>
                            <li>Kolom <strong>order_number</strong> opsional. Jika diisi sama pada beberapa baris, baris tersebut akan dianggap satu pesanan (berisi beberapa produk).</li>
                            <li>Jika <strong>order_number</strong> kosong, sistem akan membuat pesanan baru untuk setiap baris.</li>
                            <li>Pastikan nama produk <strong>sama persis</strong> dengan data produk di sistem (stok akan otomatis berkurang).</li>
                        </ul>
                    </div>
                </div>

                {{-- Step 2: Upload File --}}
                <div>
                    <h2 class="text-lg font-bold text-slate-800 mb-2">2. Upload File CSV</h2>
                    <p class="text-sm text-slate-500 mb-4">Pilih file CSV yang sudah diisi data pesanan.</p>

                    <form action="{{ route('orders.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div class="relative group">
                            <label for="file-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 hover:border-[#84994F] transition group-hover:bg-slate-50 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i data-lucide="file-spreadsheet" class="w-8 h-8 mb-3 text-slate-400 group-hover:text-[#84994F] transition"></i>
                                    <p class="mb-2 text-sm text-slate-500"><span class="font-bold text-slate-700">Klik untuk upload</span> atau drag and drop</p>
                                    <p class="text-xs text-slate-400">CSV, TXT (Max. 2MB)</p>
                                </div>
                                <input id="file-upload" name="file" type="file" class="hidden" accept=".csv, .txt" required />
                            </label>
                        </div>
                        <div id="file-name" class="text-center text-sm text-slate-600 font-medium hidden mt-2"></div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-[#84994F] hover:bg-[#6b7c40] text-white px-6 py-2.5 rounded-lg font-bold shadow-lg shadow-green-100 transition flex items-center gap-2">
                                <i data-lucide="upload-cloud" class="h-4 w-4"></i> Proses Import
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file-upload');
            const fileNameDisplay = document.getElementById('file-name');

            fileInput.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name;
                if (fileName) {
                    fileNameDisplay.textContent = 'File terpilih: ' + fileName;
                    fileNameDisplay.classList.remove('hidden');
                } else {
                    fileNameDisplay.classList.add('hidden');
                }
            });
        });
    </script>
    @endpush
@endsection
