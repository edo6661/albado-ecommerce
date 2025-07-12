<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ $title ?? config('app.name', 'Laravel') }}
    </title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=poppins:500,600,700" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="description" content="Albado - Platform E-commerce terpercaya dengan produk berkualitas">
    <meta name="keywords" content="ecommerce, online shop, albado, belanja online">
    <meta name="author" content="Albado">
    <meta property="og:title" content="{{ $title ?? 'Albado - E-commerce Platform' }}">
    <meta property="og:description" content="Platform E-commerce terpercaya dengan produk berkualitas">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/albado-og-image.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'Albado - E-commerce Platform' }}">
    <meta name="twitter:description" content="Platform E-commerce terpercaya dengan produk berkualitas">
    <meta name="twitter:image" content="{{ asset('images/albado-twitter-image.jpg') }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="msapplication-TileColor" content="#2563eb">
    <style>
        [x-cloak] { display: none !important; }
        .loading-spinner {
            border: 2px solid #f3f4f6;
            border-top: 2px solid #2563eb;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $slot }}
</head>