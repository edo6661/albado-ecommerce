
{{-- resources/views/emails/reset-password.blade.php --}}
<x-layouts.plain-app>
    <x-slot:title>Reset Password</x-slot:title>
    
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8fafc;">
        <div style="background-color: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #1f2937; font-size: 28px; margin: 0;">{{ config('app.name') }}</h1>
                <p style="color: #6b7280; margin-top: 10px;">Reset Password</p>
            </div>

            <!-- Content -->
            <div style="margin-bottom: 30px;">
                <p style="color: #374151; font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                    Halo <strong>{{ $user->name }}</strong>,
                </p>
                
                <p style="color: #374151; font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                    Kami menerima permintaan untuk mereset password akun Anda di {{ config('app.name') }}. 
                    Klik tombol di bawah ini untuk membuat password baru:
                </p>

                <!-- Reset Button -->
                <div style="text-align: center; margin: 40px 0;">
                    <a href="{{ $resetUrl }}" 
                       style="display: inline-block; padding: 15px 30px; background-color: #ef4444; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;">
                        Reset Password
                    </a>
                </div>

                <p style="color: #6b7280; font-size: 14px; line-height: 1.5;">
                    Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan menempel link berikut ke browser Anda:
                </p>
                
                <p style="word-break: break-all; color: #3b82f6; font-size: 14px; background-color: #f3f4f6; padding: 10px; border-radius: 4px;">
                    {{ $resetUrl }}
                </p>

                <div style="background-color: #fef3cd; border: 1px solid #fbbf24; border-radius: 6px; padding: 15px; margin: 20px 0;">
                    <p style="color: #92400e; font-size: 14px; margin: 0; font-weight: 600;">
                        ⚠️ Peringatan Keamanan
                    </p>
                    <p style="color: #92400e; font-size: 14px; margin: 5px 0 0 0;">
                        Link reset password ini akan kedaluwarsa dalam 60 menit. Jika Anda tidak merasa meminta reset password, 
                        abaikan email ini dan password Anda akan tetap aman.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
            
            <div style="text-align: center;">
                <p style="color: #6b7280; font-size: 12px; margin: 0;">
                    Email ini dikirim secara otomatis karena ada permintaan reset password untuk akun Anda.
                </p>
                
                <p style="color: #6b7280; font-size: 12px; margin: 10px 0 0 0;">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</x-layouts.plain-app>