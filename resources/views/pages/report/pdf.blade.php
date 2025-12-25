<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Analitik Bisnis ROMS</title>
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            line-height: 1.2;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 10pt;
            margin-top: 5px;
        }
        .meta {
            margin-top: 10px;
            font-size: 9pt;
        }
        .section {
            margin-top: 20px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px dashed #000;
            margin-bottom: 10px;
            padding-bottom: 3px;
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 5px;
            text-transform: uppercase;
        }
        td {
            padding: 5px;
            vertical-align: top;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .summary-box {
            border: 1px dashed #000;
            padding: 10px;
            margin-bottom: 20px;
        }
        .summary-row {
            display: table;
            width: 100%;
        }
        .summary-col {
            display: table-cell;
            width: 25%;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 8pt;
            text-align: center;
        }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ROMS - REPEAT ORDER MANAGEMENT SYSTEM</div>
        <div class="subtitle">LAPORAN ANALITIK BISNIS</div>
        <div class="meta">
            TANGGAL : {{ now()->format('d/m/Y H:i') }} WIB<br>
            PERIODE : ALL TIME<br>
            STATUS  : INTERNAL USE ONLY
        </div>
    </div>

    <div class="summary-box">
        <div style="text-align: center; margin-bottom: 10px; font-weight: bold;">RINGKASAN EKSEKUTIF</div>
        <div class="summary-row">
            <div class="summary-col">
                PENDAPATAN<br>
                <span class="bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
            <div class="summary-col">
                TOTAL ORDER<br>
                <span class="bold">{{ number_format($totalOrders) }}</span>
            </div>
            <div class="summary-col">
                PELANGGAN<br>
                <span class="bold">{{ number_format($totalCustomers) }}</span>
            </div>
            <div class="summary-col">
                AVG. ORDER<br>
                <span class="bold">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">I. TOP 10 PELANGGAN LOYAL</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="35%">NAMA PELANGGAN</th>
                    <th width="20%">TELEPON</th>
                    <th width="15%" class="text-center">ORDER</th>
                    <th width="25%" class="text-right">TOTAL (RP)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topCustomers as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ strtoupper($data->customer->name ?? 'UNKNOWN') }}</td>
                    <td>{{ $data->customer->clean_phone ?? '-' }}</td>
                    <td class="text-center">{{ $data->order_count }}</td>
                    <td class="text-right">{{ number_format($data->total_spent, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">TIDAK ADA DATA</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">II. SEBARAN WILAYAH (TOP 10)</div>
        <table>
            <thead>
                <tr>
                    <th width="10%">NO</th>
                    <th width="60%">KOTA/KABUPATEN</th>
                    <th width="30%" class="text-right">PELANGGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($geoDistribution as $index => $geo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ strtoupper($geo->city ?: 'TIDAK DIKETAHUI') }}</td>
                    <td class="text-right">{{ $geo->total }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center">TIDAK ADA DATA</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section page-break">
        <div class="section-title">III. TOP 10 PRODUK TERLARIS</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="45%">NAMA PRODUK</th>
                    <th width="20%" class="text-center">TERJUAL</th>
                    <th width="30%" class="text-right">PENDAPATAN (RP)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ strtoupper($product->name) }}</td>
                    <td class="text-center">{{ number_format($product->total_qty) }}</td>
                    <td class="text-right">{{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">TIDAK ADA DATA</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">IV. TRANSAKSI TERBARU (20 TERAKHIR)</div>
        <table>
            <thead>
                <tr>
                    <th width="20%">NO. ORDER</th>
                    <th width="25%">PELANGGAN</th>
                    <th width="20%">TANGGAL</th>
                    <th width="10%" class="text-center">STS</th>
                    <th width="25%" class="text-right">TOTAL (RP)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ strtoupper($order->customer->name ?? 'N/A') }}</td>
                    <td>{{ $order->created_at->format('d/m/y H:i') }}</td>
                    <td class="text-center">{{ strtoupper(substr($order->status, 0, 3)) }}</td>
                    <td class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">TIDAK ADA DATA</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        ROMS - REPEAT ORDER MANAGEMENT SYSTEM<br>
        DICETAK OLEH SYSTEM PADA {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
