<x-layouts.plain-app>
    <div class="max-w-2xl mx-auto p-6 text-center">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mb-6">
                <i class="fa-solid fa-clock text-6xl text-yellow-500 mb-4"></i>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Belum Selesai</h1>
            </div>
            
            <div class="space-y-4">
                <a href="{{ route('home') }}" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                    <i class="fa-solid fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-layouts.plain-app>