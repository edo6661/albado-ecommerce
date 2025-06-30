<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Order</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: #4F46E5;
            font-size: 18px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .filters {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #4F46E5;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 5px;
        }
        
        .filter-label {
            font-weight: bold;
            color: #333;
        }
        
        .summary {
            background: #e0e7ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .summary h3 {
            margin: 0;
            color: #4F46E5;
            font-size: 12px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        
        th {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-paid { background: #D1FAE5; color: #065F46; }
        .status-processing { background: #DDD6FE; color: #5B21B6; }
        .status-shipped { background: #DBEAFE; color: #1E40AF; }
        .status-delivered { background: #D1FAE5; color: #047857; }
        .status-cancelled { background: #FEE2E2; color: #991B1B; }
        .status-failed { background: #FEE2E2; color: #991B1B; }
        
        .currency {
            font-family: 'Courier New', monospace;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 8px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA ORDER</h1>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }}</p>
    </div>

    @if(!empty(array_filter($filters)))
    <div class="filters">
        <h3>Filter yang Diterapkan:</h3>
        @if(!empty($filters['status']))
            <div class="filter-item">
                <span class="filter-label">Status:</span> {{ ucfirst($filters['status']) }}
            </div>
        @endif
        @if(!empty($filters['date_from']))
            <div class="filter-item">
                <span class="filter-label">Dari Tanggal:</span> {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }}
            </div>
        @endif
        @if(!empty($filters['date_to']))
            <div class="filter-item">
                <span class="filter-label">Sampai Tanggal:</span> {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}
            </div>
        @endif
        @if(!empty($filters['search']))
            <div class="filter-item">
                <span class="filter-label">Pencarian:</span> {{ $filters['search'] }}
            </div>
        @endif
    </div>
    @endif

    <div class="summary">
        <h3>Total Order: {{ $orders->count() }} | Total Nilai: Rp {{ number_format($orders->sum('total'), 0, ',', '.') }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="12%">Nomor Order</th>
                <th width="15%">Customer</th>
                <th width="8%">Status</th>
                <th width="12%">Tanggal</th>
                <th width="12%">Subtotal</th>
                <th width="8%">Pajak</th>
                <th width="12%">Total</th>
                <th width="6%">Item</th>
                <th width="10%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>
                        <strong>{{ $order->user->name ?? 'N/A' }}</strong><br>
                        <small>{{ $order->user->email ?? 'N/A' }}</small>
                    </td>
                    <td class="text-center">
                        <span class="status-badge status-{{ $order->status->value }}">
                            {{ $order->status->label() }}
                        </span>
                    </td>
                    <td class="text-center">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-right currency">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right currency">Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
                    <td class="text-right currency"><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                    <td class="text-center">{{ $order->items->sum('quantity') }}</td>
                    <td>{{ $order->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data order</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} - Sistem Manajemen Order | Halaman {PAGE_NUM} dari {PAGE_COUNT}</p>
    </div>
</body>
</html>