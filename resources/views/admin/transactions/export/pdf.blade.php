<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Transaksi</title>
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
        .status-settlement { background: #D1FAE5; color: #065F46; }
        .status-capture { background: #DBEAFE; color: #1E40AF; }
        .status-deny { background: #FEE2E2; color: #991B1B; }
        .status-cancel { background: #FEE2E2; color: #991B1B; }
        .status-expire { background: #F3F4F6; color: #374151; }
        .status-failure { background: #FEE2E2; color: #991B1B; }
        
        .payment-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            background: #E5E7EB;
            color: #374151;
        }
        
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
        
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA TRANSAKSI</h1>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }}</p>
    </div>

    @if(!empty(array_filter($filters)))
    <div class="filters">
        <h3>Filter yang Diterapkan:</h3>
        @if(!empty($filters['status']))
            <div class="filter-item">
                <span class="filter-label">Status:</span> {{ strtoupper($filters['status']) }}
            </div>
        @endif
        @if(!empty($filters['payment_type']))
            <div class="filter-item">
                <span class="filter-label">Tipe Pembayaran:</span> {{ strtoupper(str_replace('_', ' ', $filters['payment_type'])) }}
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
        <h3>Total Transaksi: {{ $transactions->count() }} | Total Settlement: Rp {{ number_format($total_amount, 0, ',', '.') }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No.</th>
                <th width="10%">ID Transaksi</th>
                <th width="10%">Order ID</th>
                <th width="12%">Customer</th>
                <th width="8%">Status</th>
                <th width="10%">Pembayaran</th>
                <th width="10%">Jumlah</th>
                <th width="10%">Tanggal Transaksi</th>
                <th width="10%">Tanggal Settlement</th>
                <th width="8%">Fraud Status</th>
                <th width="8%">Currency</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaction->transaction_id }}</td>
                    <td>{{ $transaction->order->order_number ?? $transaction->order_id_midtrans }}</td>
                    <td>
                        <strong>{{ $transaction->order->user->name ?? 'N/A' }}</strong><br>
                        <small>{{ $transaction->order->user->email ?? 'N/A' }}</small>
                    </td>
                    <td class="text-center">
                        <span class="status-badge status-{{ $transaction->status->value }}">
                            {{ $transaction->status->label() }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="payment-badge">
                            {{ $transaction->payment_type ? $transaction->payment_type->label() : 'N/A' }}
                        </span>
                    </td>
                    <td class="text-right currency">
                        <strong>Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-center">
                        {{ $transaction->transaction_time ? $transaction->transaction_time->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="text-center">
                        {{ $transaction->settlement_time ? $transaction->settlement_time->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="text-center">{{ $transaction->fraud_status ?? '-' }}</td>
                    <td class="text-center">{{ $transaction->currency }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($status_summary->count() > 0)
    <div style="margin-top: 20px;">
        <h3 style="color: #4F46E5; font-size: 12px;">Ringkasan Status:</h3>
        <table style="width: 50%; margin-top: 10px;">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($status_summary as $status => $count)
                <tr>
                    <td>{{ strtoupper($status) }}</td>
                    <td class="text-center">{{ $count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>© {{ date('Y') }} - Sistem Manajemen Transaksi | Halaman {PAGE_NUM} dari {PAGE_COUNT}</p>
    </div>
</body>
</html>