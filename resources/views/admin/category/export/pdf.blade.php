<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kategori</title>
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
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            color: #28a745;
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
            grid-template-columns: repeat(3, 1fr);
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
            color: #28a745;
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
            background-color: #28a745;
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
        
        .image-cell {
            text-align: center;
            padding: 4px;
        }
        
        .category-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }
        
        .no-image {
            width: 40px;
            height: 40px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6px;
            color: #666;
            margin: 0 auto;
        }
        
        .product-count-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            background-color: #28a745;
            color: white;
        }
        
        .no-products {
            background-color: #6c757d;
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
        
        .category-detail {
            font-size: 7px;
            color: #666;
            line-height: 1.2;
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
        <h1>LAPORAN DATA KATEGORI</h1>
        <p>Tanggal Export: {{ $export_date }}</p>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-item">
                <h4>Total Kategori</h4>
                <span>{{ number_format($total_categories) }}</span>
            </div>
            <div class="summary-item">
                <h4>Total Produk</h4>
                <span>{{ number_format($total_products) }}</span>
            </div>
            <div class="summary-item">
                <h4>Rata-rata Produk</h4>
                <span>{{ $total_categories > 0 ? number_format($total_products / $total_categories, 1) : '0' }}</span>
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
    </div>
    @endif
    
    <!-- Categories Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">Gambar</th>
                    <th style="width: 30%;">Nama Kategori</th>
                    <th style="width: 25%;">Slug</th>
                    <th style="width: 10%;">Jumlah Produk</th>
                    <th style="width: 20%;">Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $category)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="image-cell">
                        @if($category->image)
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="category-image">
                        @else
                            <div class="no-image">No Image</div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $category->name }}</strong>
                    </td>
                    <td>
                        <code style="background-color: #f8f9fa; padding: 2px 4px; border-radius: 2px; font-size: 7px;">
                            {{ $category->slug }}
                        </code>
                    </td>
                    <td class="text-center">
                        <span class="product-count-badge {{ $category->products->count() === 0 ? 'no-products' : '' }}">
                            {{ number_format($category->products->count()) }}
                        </span>
                    </td>
                    <td class="text-center">{{ $category->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #666;">
                        Tidak ada data kategori yang ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Category Summary -->
    @if($category_summary->count() > 0)
    <div style="margin-top: 20px;">
        <h3 style="font-size: 12px; margin-bottom: 10px; color: #495057;">Ringkasan Kategori:</h3>
        <table style="width: 60%;">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Jumlah Produk</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category_summary as $summary)
                <tr>
                    <td>{{ $summary['name'] }}</td>
                    <td class="text-center">{{ number_format($summary['products_count']) }}</td>
                    <td class="text-center">
                        {{ $total_products > 0 ? number_format(($summary['products_count'] / $total_products) * 100, 1) : '0' }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Statistics -->
    <div style="margin-top: 20px; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
        <h3 style="font-size: 12px; margin-bottom: 8px; color: #495057;">Statistik Kategori:</h3>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; font-size: 9px;">
            <div>
                <strong>Kategori dengan Produk Terbanyak:</strong><br>
                @if($categories->count() > 0)
                    {{ $categories->sortByDesc(function($cat) { return $cat->products->count(); })->first()->name }}
                    ({{ $categories->sortByDesc(function($cat) { return $cat->products->count(); })->first()->products->count() }} produk)
                @else
                    -
                @endif
            </div>
            <div>
                <strong>Kategori Tanpa Produk:</strong><br>
                {{ $categories->filter(function($cat) { return $cat->products->count() === 0; })->count() }} kategori
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis pada {{ $export_date }}</p>
        <p>Total halaman: <span class="pagenum"></span></p>
    </div>
</body>
</html>