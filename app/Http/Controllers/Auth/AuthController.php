<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Contracts\Services\AuthServiceInterface;
use App\Events\Auth\EmailVerificationRequested;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            $user = $this->authService->register($request->validated());
            
            return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan cek email Anda untuk verifikasi.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registrasi gagal. Silakan coba lagi.']);
        }
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        
        $user = $this->authService->login(array_merge($credentials, ['remember' => $remember]));
        
        if (!$user) {
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }
        $request->session()->regenerate();
        return redirect()->intended($this->authService->redirectAfterLogin());
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
    
    public function showVerificationNotice(): View
    {
        return view('auth.verify-email');
    }

    public function verify(Request $request): RedirectResponse
    {
        $this->authService->verifyEmail($request->user());
        return redirect()->intended($this->authService->redirectAfterLogin())
            ->with('success', 'Email berhasil diverifikasi!');
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Anda harus login terlebih dahulu.']);
        }
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->authService->redirectAfterLogin())
                ->with('info', 'Email Anda sudah diverifikasi.');
        }

        event(new EmailVerificationRequested($user));
        
        return back()->with('status', 'Link verifikasi email telah dikirim ulang ke ' . $user->email . '!');
    }
    
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $sent = $this->authService->sendPasswordResetLink($request->email);
        
        if ($sent) {
            return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
        }
        
        return back()->withErrors(['email' => 'Tidak dapat mengirim link reset password.']);
    }

    public function showResetPasswordForm(Request $request): View
    {
        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $reset = $this->authService->resetPassword($request->validated());
        
        if ($reset) {
            return redirect()->route('login')->with('status', 'Password berhasil direset.');
        }
        
        return back()->withErrors(['email' => 'Gagal mereset password.']);
    }
}