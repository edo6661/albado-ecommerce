{{-- resources/views/emails/verify-email.blade.php --}}
<x-layouts.plain-app>
    <x-slot:title>Verifikasi Email</x-slot:title>
    
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8fafc;">
        <div style="background-color: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #1f2937; font-size: 28px; margin: 0;">{{ config('app.name') }}</h1>
                <p style="color: #6b7280; margin-top: 10px;">Verifikasi Email Anda</p>
            </div>

            <!-- Content -->
            <div style="margin-bottom: 30px;">
                <p style="color: #374151; font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                    Halo <strong>{{ $user->name }}</strong>,
                </p>
                
                <p style="color: #374151; font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                    Terima kasih telah mendaftar di {{ config('app.name') }}! Untuk melengkapi proses registrasi, 
                    silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini:
                </p>

                <!-- Verification Button -->
                <div style="text-align: center; margin: 40px 0;">
                    <a href="{{ $verificationUrl }}" 
                       style="display: inline-block; padding: 15px 30px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;">
                        Verifikasi Email Saya
                    </a>
                </div>

                <p style="color: #6b7280; font-size: 14px; line-height: 1.5;">
                    Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan menempel link berikut ke browser Anda:
                </p>
                
                <p style="word-break: break-all; color: #3b82f6; font-size: 14px; background-color: #f3f4f6; padding: 10px; border-radius: 4px;">
                    {{ $verificationUrl }}
                </p>

                <p style="color: #6b7280; font-size: 14px; line-height: 1.5; margin-top: 20px;">
                    <strong>Catatan:</strong> Link verifikasi ini akan kedaluwarsa dalam 60 menit untuk keamanan akun Anda.
                </p>
            </div>

            <!-- Footer -->
            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
            
            <div style="text-align: center;">
                <p style="color: #6b7280; font-size: 12px; margin: 0;">
                    Email ini dikirim secara otomatis. Jika Anda tidak merasa mendaftar di {{ config('app.name') }}, 
                    Anda dapat mengabaikan email ini.
                </p>
                
                <p style="color: #6b7280; font-size: 12px; margin: 10px 0 0 0;">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</x-layouts.plain-app>
