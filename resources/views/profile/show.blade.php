<x-layouts.plain-app>
    <x-slot:title>Profil Saya</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900">Profil Saya</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Informasi akun dan profil Anda
                </p>
            </div>

            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8">
                    <div class="flex items-center justify-center">
                        <div class="relative">
                            @if($user->profile?->avatar)
                                <img class="h-24 w-24 rounded-full border-4 border-white shadow-lg" 
                                     src="{{ $user->profile->avatar_url }}" 
                                     alt="Avatar {{ $user->name }}">
                            @else
                                <div class="h-24 w-24 rounded-full bg-white border-4 border-white shadow-lg flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                        <p class="text-indigo-100 text-sm mt-1">{{ $user->email }}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white text-indigo-800">
                                {{ $user->role->value === 'admin' ? 'Administrator' : 'Pengguna' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Alamat Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status Email</dt>
                                    <dd class="mt-1 text-sm">
                                        @if($user->email_verified_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3"></circle>
                                                </svg>
                                                Terverifikasi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3"></circle>
                                                </svg>
                                                Belum Terverifikasi
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Bergabung Sejak</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d F Y') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Akun</h3>
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Peran</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $user->role->value === 'admin' ? 'Administrator' : 'Pengguna' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $user->updated_at->format('d F Y, H:i') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('home') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Home
                        </a>
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Profil
                        </a>
                        <a href="{{ route('profile.addresses.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Addresses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>