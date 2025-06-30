<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produk</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .summary-section {
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .summary-item {
            text-align: center;
            background: white;
            padding: 8px;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }
        
        .summary-item h4 {
            font-size: 12px;
            color: #007bff;
            margin-bottom: 3px;
        }
        
        .summary-item span {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .filters-section {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #e9ecef;
            border-radius: 3px;
        }
        
        .filters-section h3 {
            font-size: 12px;
            margin-bottom: 5px;
            color: #495057;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 15px;
            font-size: 9px;
        }
        
        .table-container {
            margin-top: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
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
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .stock-status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        
        .stock-good {
            background-color: #d4edda;
            color: #155724;
        }
        
        .stock-low {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .stock-out {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN DATA PRODUK</h1>
        <p>Tanggal Export: {{ $export_date }}</p>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-item">
                <h4>Total Produk</h4>
                <span>{{ number_format($total_products) }}</span>
            </div>
            <div class="summary-item">
                <h4>Nilai Inventory</h4>
                <span>Rp {{ number_format($total_value, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <h4>Produk Aktif</h4>
                <span>{{ number_format($status_summary['active']) }}</span>
            </div>
            <div class="summary-item">
                <h4>Stok Habis</h4>
                <span>{{ number_format($stock_summary['out_of_stock']) }}</span>
            </div>
        </div>
    </div>
    
    <!-- Filters Applied -->
    @if(array_filter($filters))
    <div class="filters-section">
        <h3>Filter yang Diterapkan:</h3>
        @if($filters['search'])
            <span class="filter-item"><strong>Pencarian:</strong> {{ $filters['search'] }}</span>
        @endif
        @if($filters['category'])
            <span class="filter-item"><strong>Kategori:</strong> {{ $filters['category'] }}</span>
        @endif
        @if($filters['status'])
            <span class="filter-item"><strong>Status:</strong> {{ ucfirst($filters['status']) }}</span>
        @endif
    </div>
    @endif
    
    <!-- Products Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 25%;">Nama Produk</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 12%;">Harga</th>
                    <th style="width: 12%;">Harga Diskon</th>
                    <th style="width: 8%;">Stok</th>
                    <th style="width: 10%;">Status Stok</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%;">Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->description)
                            <br><small style="color: #666;">{{ Str::limit($product->description, 60) }}</small>
                        @endif
                    </td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if($product->discount_price)
                            Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($product->stock) }}</td>
                    <td class="text-center">
                        @if($product->stock > 10)
                            <span class="stock-status stock-good">Baik</span>
                        @elseif($product->stock > 0)
                            <span class="stock-status stock-low">Rendah</span>
                        @else
                            <span class="stock-status stock-out">Habis</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="status-badge {{ $product->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="text-center">{{ $product->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #666;">
                        Tidak ada data produk yang ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Summary by Category -->
    @if($category_summary->count() > 0)
    <div style="margin-top: 20px;">
        <h3 style="font-size: 12px; margin-bottom: 10px; color: #495057;">Ringkasan per Kategori:</h3>
        <table style="width: 50%;">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah Produk</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category_summary as $category => $count)
                <tr>
                    <td>{{ $category ?: 'Tanpa Kategori' }}</td>
                    <td class="text-center">{{ $count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis pada {{ $export_date }}</p>
        <p>Total halaman: <span class="pagenum"></span></p>
    </div>
</body>
</html>