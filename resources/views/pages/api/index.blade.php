@extends('layouts.app')

@section('title', 'Integrasi API - ROMS')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="webhook" class="h-6 w-6 text-[#84994F]"></i> Integrasi API
            </h1>
            <p class="text-sm text-slate-500 mt-1">Hubungkan toko online atau sistem eksternal Anda dengan ROMS.</p>
        </div>
        
        <button onclick="generateNewApiKey()" class="bg-[#B45253] hover:bg-[#9a4243] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-red-100 transition flex items-center gap-2">
            <i data-lucide="key" class="h-4 w-4"></i> Generate API Key Baru
        </button>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- Card 1: Status API -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Status API</p>
                <h3 class="text-xl font-bold text-green-600">Aktif</h3>
            </div>
        </div>

        <!-- Card 2: Total Requests -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="activity" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Total Requests</p>
                <h3 class="text-xl font-bold text-blue-600">{{ number_format($apiStats['total_requests']) }}</h3>
            </div>
        </div>

        <!-- Card 3: Hari Ini -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center">
                <i data-lucide="calendar-clock" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Request Hari Ini</p>
                <h3 class="text-xl font-bold text-cyan-600">{{ number_format($apiStats['today_requests']) }}</h3>
            </div>
        </div>

        <!-- Card 4: Success Rate -->
        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-[#FCB53B]/10 text-[#FCB53B] flex items-center justify-center">
                <i data-lucide="percent" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Success Rate</p>
                <h3 class="text-xl font-bold text-[#FCB53B]">{{ $apiStats['success_rate'] }}%</h3>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT WITH TABS --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 mb-10">
        
        {{-- Tabs Header --}}
        <div class="border-b border-slate-200 px-6 pt-4">
            <nav class="flex gap-6" aria-label="Tabs">
                <button onclick="switchTab('quickstart')" id="tab-quickstart" class="tab-btn active pb-4 text-sm font-bold text-[#84994F] border-b-2 border-[#84994F] flex items-center gap-2 transition">
                    <i data-lucide="rocket" class="h-4 w-4"></i> Quick Start
                </button>
                <button onclick="switchTab('endpoints')" id="tab-endpoints" class="tab-btn pb-4 text-sm font-bold text-slate-500 hover:text-slate-700 border-b-2 border-transparent hover:border-slate-300 flex items-center gap-2 transition">
                    <i data-lucide="code-2" class="h-4 w-4"></i> Endpoints
                </button>
                <button onclick="switchTab('authentication')" id="tab-authentication" class="tab-btn pb-4 text-sm font-bold text-slate-500 hover:text-slate-700 border-b-2 border-transparent hover:border-slate-300 flex items-center gap-2 transition">
                    <i data-lucide="shield-check" class="h-4 w-4"></i> Authentication
                </button>
                <button onclick="switchTab('testing')" id="tab-testing" class="tab-btn pb-4 text-sm font-bold text-slate-500 hover:text-slate-700 border-b-2 border-transparent hover:border-slate-300 flex items-center gap-2 transition">
                    <i data-lucide="zap" class="h-4 w-4"></i> Test API
                </button>
            </nav>
        </div>

        {{-- Tabs Content --}}
        <div class="p-6 md:p-8">
            
            {{-- 1. QUICK START --}}
            <div id="content-quickstart" class="tab-content block">
                <h5 class="text-lg font-bold text-slate-800 mb-4">🚀 Mulai Integrasi dalam 3 Langkah</h5>
                
                <div class="mb-6 p-4 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 flex items-start gap-3">
                    <i data-lucide="info" class="h-5 w-5 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm">
                        <strong>Kebutuhan:</strong> Pastikan Anda familiar dengan REST API dan format JSON.
                    </div>
                </div>

                <div class="space-y-6">
                    
                    {{-- Step 1 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold">1</div>
                        <div class="flex-1">
                            <h6 class="font-bold text-slate-800">Dapatkan API Key Anda</h6>
                            <p class="text-sm text-slate-500 mb-3">API Key digunakan untuk autentikasi setiap request.</p>
                            
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1 max-w-md">
                                    <input type="password" id="apiKeyDisplay" value="{{ $apiKey }}" readonly
                                        class="w-full pl-4 pr-12 py-2.5 bg-slate-100 border border-slate-200 rounded-lg text-sm font-mono text-slate-600 focus:outline-none">
                                    <button onclick="toggleApiKeyVisibility()" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                                        <i data-lucide="eye" class="h-5 w-5" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <button onclick="copyToClipboard('{{ $apiKey }}')" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg hover:border-[#84994F] hover:text-[#84994F] transition flex items-center gap-2 text-sm font-bold">
                                    <i data-lucide="copy" class="h-4 w-4"></i> Copy
                                </button>
                            </div>
                            <p class="text-xs text-red-500 mt-2 flex items-center gap-1">
                                <i data-lucide="alert-triangle" class="h-3 w-3"></i> Jangan bagikan API Key Anda kepada siapapun!
                            </p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold">2</div>
                        <div class="flex-1">
                            <h6 class="font-bold text-slate-800">Kirim Request Pertama</h6>
                            <p class="text-sm text-slate-500 mb-3">Gunakan endpoint POST untuk membuat order baru.</p>
                            
                            <div class="bg-slate-900 text-slate-300 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800">
<pre><code>curl -X POST {{ $apiUrl }} \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: YOUR_API_KEY" \
  -d '{
    "customer": {
      "phone": "081234567890",
      "name": "John Doe"
    },
    "items": [{
      "product_name": "Product A",
      "quantity": 1,
      "price": 100000
    }]
  }'</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[#84994F] text-white flex items-center justify-center font-bold">3</div>
                        <div class="flex-1">
                            <h6 class="font-bold text-slate-800">Verifikasi Response</h6>
                            <p class="text-sm text-slate-500 mb-3">Response sukses akan mengembalikan data order yang telah dibuat.</p>
                            
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 font-mono text-xs text-slate-600">
<pre><code>{
  "success": true,
  "message": "Order berhasil dibuat",
  "data": {
    "id": 123,
    "order_number": "ORD-20250130-001",
    "status": "pending"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. ENDPOINTS --}}
            <div id="content-endpoints" class="tab-content hidden">
                <h5 class="text-lg font-bold text-slate-800 mb-4">📡 Available Endpoints</h5>

                {{-- Create Order --}}
                <div class="border border-slate-200 rounded-xl p-4 mb-4 hover:border-green-200 transition">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-md border border-green-200">POST</span>
                                <code class="text-sm font-mono text-slate-700 bg-slate-100 px-2 py-1 rounded">/api/v1/orders</code>
                            </div>
                            <p class="text-sm text-slate-600 mb-3">Create a new order from external source.</p>
                            
                            <details class="group">
                                <summary class="text-xs font-bold text-[#84994F] cursor-pointer hover:underline flex items-center gap-1">
                                    View Request Body <i data-lucide="chevron-down" class="h-3 w-3 group-open:rotate-180 transition-transform"></i>
                                </summary>
                                <div class="mt-3 bg-slate-900 text-slate-300 p-4 rounded-lg font-mono text-xs overflow-x-auto">
<pre><code>{
  "customer": {
    "phone": "string (required)",
    "name": "string (optional)",
    "email": "string (optional)", 
    "address": "string (optional)"
  },
  "items": [
    {
      "product_name": "string (required)",
      "quantity": "integer (required)",
      "price": "integer (required)"
    }
  ],
  "notes": "string (optional)",
  "external_id": "string (optional)"
}</code></pre>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>

                {{-- Get Order --}}
                <div class="border border-slate-200 rounded-xl p-4 mb-4 hover:border-blue-200 transition">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-md border border-blue-200">GET</span>
                        <code class="text-sm font-mono text-slate-700 bg-slate-100 px-2 py-1 rounded">/api/v1/orders/{id}</code>
                    </div>
                    <p class="text-sm text-slate-600">Get order details by ID.</p>
                </div>

                {{-- Webhook --}}
                <div class="border border-slate-200 rounded-xl p-4 hover:border-yellow-200 transition bg-yellow-50/30">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-[#FCB53B]/20 text-[#e09d22] text-xs font-bold px-2.5 py-1 rounded-md border border-[#FCB53B]/30">WEBHOOK</span>
                        <code class="text-sm font-mono text-slate-700 bg-white px-2 py-1 rounded border border-slate-200">{{ $webhookUrl }}</code>
                    </div>
                    <p class="text-sm text-slate-600 mb-1">Receive status updates (configure in your system).</p>
                    <p class="text-xs text-slate-400">Events: order.created, order.updated, order.completed</p>
                </div>
            </div>

            {{-- 3. AUTHENTICATION --}}
            <div id="content-authentication" class="tab-content hidden">
                <h5 class="text-lg font-bold text-slate-800 mb-4">🔐 Authentication & Security</h5>

                <div class="mb-6 p-4 bg-yellow-50 text-yellow-800 rounded-xl border border-yellow-200 flex items-start gap-3">
                    <i data-lucide="shield-alert" class="h-5 w-5 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm">
                        <strong>Penting:</strong> Semua requests harus menyertakan API Key di header untuk alasan keamanan.
                    </div>
                </div>

                <h6 class="font-bold text-slate-700 mb-3 text-sm uppercase tracking-wider">Required Headers</h6>
                <div class="overflow-hidden rounded-xl border border-slate-200 mb-8">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 border-b border-slate-200">Header Name</th>
                                <th class="px-4 py-3 border-b border-slate-200">Value</th>
                                <th class="px-4 py-3 border-b border-slate-200">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="px-4 py-3 font-mono text-slate-600">Content-Type</td>
                                <td class="px-4 py-3 font-mono text-blue-600">application/json</td>
                                <td class="px-4 py-3 text-slate-500">Format data yang dikirim</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono text-slate-600">Accept</td>
                                <td class="px-4 py-3 font-mono text-blue-600">application/json</td>
                                <td class="px-4 py-3 text-slate-500">Format response</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-mono text-slate-600">X-API-KEY</td>
                                <td class="px-4 py-3 font-mono text-red-500">{{ Str::limit($apiKey, 20) }}...</td>
                                <td class="px-4 py-3 text-slate-500">Your unique API key</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="font-bold text-slate-700 mb-3 text-sm uppercase tracking-wider">Error Responses</h6>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 font-mono text-xs text-slate-600 overflow-x-auto">
<pre><code>// 401 Unauthorized - Invalid API Key
{
  "error": "Unauthorized",
  "message": "Invalid or missing API key"
}

// 422 Validation Error
{
  "error": "Validation failed",
  "errors": {
    "customer.phone": ["Phone is required"]
  }
}</code></pre>
                </div>
            </div>

            {{-- 4. TESTING --}}
            <div id="content-testing" class="tab-content hidden">
                <h5 class="text-lg font-bold text-slate-800 mb-4">⚡ Test Your API</h5>
                
                <div class="mb-6 p-4 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 flex items-start gap-3">
                    <i data-lucide="zap" class="h-5 w-5 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm">
                        Gunakan formulir di bawah ini untuk mengirim <strong>Real Request</strong> ke endpoint API Anda.
                    </div>
                </div>

                <form id="apiTestForm" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Customer Phone <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" placeholder="081234567890" required
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Customer Name</label>
                            <input type="text" name="name" placeholder="John Doe"
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="product_name" placeholder="Product A" required
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Qty <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" value="1" required
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Price <span class="text-red-500">*</span></label>
                                <input type="number" name="price" value="100000" required
                                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Order notes (optional)"
                            class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#84994F] focus:ring-1 focus:ring-[#84994F] transition resize-none"></textarea>
                    </div>

                    <div>
                        <button type="submit" class="w-full md:w-auto px-8 py-3 bg-[#84994F] text-white rounded-xl text-sm font-bold hover:bg-[#6b7d3f] shadow-md shadow-green-100 transition flex items-center justify-center gap-2">
                            <i data-lucide="send" class="h-4 w-4"></i> Send Test Request
                        </button>
                    </div>
                </form>

                {{-- Response Box --}}
                <div id="testResponse" class="mt-8 hidden">
                    <h6 class="font-bold text-slate-700 mb-2 flex items-center gap-2">
                        <i data-lucide="terminal" class="h-4 w-4 text-slate-400"></i> Response
                    </h6>
                    <div class="bg-slate-900 text-green-400 p-4 rounded-xl font-mono text-xs overflow-x-auto border border-slate-800 shadow-inner">
                        <pre id="responseBody" class="mb-0">Waiting for request...</pre>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        // TAB SWITCHING LOGIC
        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Show target content
            document.getElementById('content-' + tabId).classList.remove('hidden');
            
            // Reset all tab buttons style
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('active', 'text-[#84994F]', 'border-[#84994F]');
                el.classList.add('text-slate-500', 'border-transparent');
            });

            // Set active tab button style
            const activeBtn = document.getElementById('tab-' + tabId);
            activeBtn.classList.remove('text-slate-500', 'border-transparent');
            activeBtn.classList.add('active', 'text-[#84994F]', 'border-[#84994F]');
            
            // Re-init Lucide for hidden elements
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        // API ACTIONS
        function toggleApiKeyVisibility() {
            const input = document.getElementById('apiKeyDisplay');
            const icon = document.getElementById('eyeIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                // Change icon logic if needed (Lucide re-render might be needed)
                // For simplicity, we keep it as is or toggle class if using font icons
            } else {
                input.type = 'password';
            }
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('API Key copied to clipboard!');
            });
        }

        function generateNewApiKey() {
            if (!confirm('Generate API Key baru? Key lama akan tidak valid.')) return;
            
            fetch('{{ route("admin.api.generate-key") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('API Key baru berhasil dibuat.\nSilakan simpan di konfigurasi sistem eksternal Anda.');
                    document.getElementById('apiKeyDisplay').value = data.api_key;
                }
            });
        }

        // TEST FORM SUBMIT
        document.getElementById('apiTestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const payload = {
                customer: {
                    phone: formData.get('phone'),
                    name: formData.get('name')
                },
                items: [{
                    product_name: formData.get('product_name'),
                    quantity: parseInt(formData.get('quantity')),
                    price: parseInt(formData.get('price'))
                }],
                notes: formData.get('notes')
            };
            
            // Show loading state in response box
            const responseBox = document.getElementById('testResponse');
            const responseBody = document.getElementById('responseBody');
            responseBox.classList.remove('hidden');
            responseBody.textContent = 'Sending request...';

            fetch('{{ $apiUrl }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-API-KEY': '{{ $apiKey }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                responseBody.textContent = JSON.stringify(data, null, 2);
            })
            .catch(err => {
                responseBody.textContent = 'Error: ' + err.message;
            });
        });

        // INIT
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
    @endpush

@endsection