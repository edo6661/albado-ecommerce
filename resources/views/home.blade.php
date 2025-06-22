<x-layouts.plain-app>
    <p class="text-red-500">
        test
    </p>
    @if(session('email_not_verified') || (auth()->check() && !auth()->user()->hasVerifiedEmail()))
    <div class="rounded-md bg-yellow-50 p-4 mb-6 border border-yellow-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Email Belum Diverifikasi
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>
                        @if(session('warning'))
                            {{ session('warning') }}
                        @else
                            Email Anda belum diverifikasi. Silakan cek inbox email Anda untuk verifikasi akun.
                        @endif
                    </p>
                </div>
                <div class="mt-4">
                    <div class="flex">
                        <form method="POST" action="{{ route('verification.send') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-sm font-medium py-2 px-4 rounded-md border border-yellow-300 transition duration-150 ease-in-out">
                                Kirim Ulang Email Verifikasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

</x-layouts.plain-app>