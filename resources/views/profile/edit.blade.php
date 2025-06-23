<x-layouts.plain-app>
    <x-slot:title>Edit Profil</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900">Edit Profil</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Perbarui informasi akun dan profil Anda
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
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8">
                        <div class="flex items-center justify-center">
                            <div class="relative">
                                @if($user->profile?->avatar)
                                    <img class="h-24 w-24 rounded-full border-4 border-white shadow-lg" 
                                         src="{{ $user->profile->avatar_url }}" 
                                         alt="Avatar {{ $user->name }}"
                                         id="avatar-preview">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-white border-4 border-white shadow-lg flex items-center justify-center" id="avatar-preview">
                                        <svg class="h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <p class="text-indigo-100 text-sm">Foto Profil</p>
                        </div>
                    </div>

                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Profil</h3>
                            
                            <div>
                                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Image') }}
                                </label>
                                
                                <div class="mt-1 flex items-center">
                                    <div id="imagePreviewContainer" class="w-32 h-32 bg-gray-100 rounded-md overflow-hidden">
                                        <img 
                                            id="imagePreview"
                                            src="{{ $profile->avatar_url }}" 
                                            alt="{{ $user->name }}" 
                                            class="w-full h-full object-cover"
                                        >
                                    </div>
                                    
                                    <div class="ml-5">
                                        <input
                                            type="file"
                                            name="avatar"
                                            id="image"
                                            accept="image/*"
                                            class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            onchange="previewImage(this)"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ __('
                                                Empty if you do not want to change the image.
                                            ') }}
                                        </p>
                                    </div>
                                </div>
                                
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('Format: JPG, PNG, JPEG, GIF. Max 2MB.') }}
                                </p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Akun</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Nama Lengkap
                                    </label>
                                    <div class="mt-1">
                                        <input id="name" 
                                               name="name" 
                                               type="text" 
                                               autocomplete="name"
                                               class="appearance-none block w-full px-3 py-2 border @error('name') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                               placeholder="Masukkan nama lengkap"
                                               value="{{ old('name', $user->name) }}">
                                    </div>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Alamat Email
                                    </label>
                                    <div class="mt-1">
                                        <input id="email" 
                                               name="email" 
                                               type="email" 
                                               autocomplete="email"
                                               class="appearance-none block w-full px-3 py-2 border @error('email') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                               placeholder="nama@example.com"
                                               value="{{ old('email', $user->email) }}">
                                    </div>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            @php
                                $hasPassword = !is_null($user->password);
                            @endphp
                            
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ $hasPassword ? 'Ubah Password' : 'Atur Password' }}
                            </h3>
                            
                            <p class="text-sm text-gray-600 mb-4">
                                @if($hasPassword)
                                    Kosongkan jika tidak ingin mengubah password
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-2">
                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        Login dengan Google
                                    </span>
                                    <br>
                                    Anda masuk menggunakan Google. Anda dapat mengatur password untuk memungkinkan login dengan email dan password.
                                @endif
                            </p>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">
                                        {{ $hasPassword ? 'Password Baru' : 'Password' }}
                                    </label>
                                    <div class="mt-1">
                                        <input id="password" 
                                               name="password" 
                                               type="password" 
                                               autocomplete="new-password"
                                               class="appearance-none block w-full px-3 py-2 border @error('password') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                               placeholder="{{ $hasPassword ? 'Masukkan password baru' : 'Masukkan password' }}">
                                    </div>
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                        Konfirmasi Password
                                    </label>
                                    <div class="mt-1">
                                        <input id="password_confirmation" 
                                               name="password_confirmation" 
                                               type="password" 
                                               autocomplete="new-password"
                                               class="appearance-none block w-full px-3 py-2 border @error('password_confirmation') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                               placeholder="{{ $hasPassword ? 'Konfirmasi password baru' : 'Konfirmasi password' }}">
                                    </div>
                                    @error('password_confirmation')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('profile.show') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const defaultIcon = document.getElementById('defaultIcon');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    defaultIcon.classList.add('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layouts.plain-app>