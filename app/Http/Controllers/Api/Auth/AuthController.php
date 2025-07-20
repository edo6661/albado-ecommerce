<?php
namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use App\Contracts\Services\AuthServiceInterface;
use App\Events\Auth\EmailVerificationRequested;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}
    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil. Silakan cek email Anda untuk verifikasi.',
                'data' => [
                    'user' => new UserResource($user),
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal. Silakan coba lagi.',
                'error' => $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
    /**
     * Login user and generate access token
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');
            $user = $this->authService->login(array_merge($credentials, ['remember' => $remember]));
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                ], 401);
            }
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Logout user and revoke access token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get authenticated user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil diambil',
                'data' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pengguna',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Send password reset link
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $sent = $this->authService->sendPasswordResetLink($request->email);
            if ($sent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Link reset password telah dikirim ke email Anda.'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengirim link reset password.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim link reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Reset password with token
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $reset = $this->authService->resetPassword($request->validated());
            if ($reset) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password berhasil direset.'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset password.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Verify email address
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email sudah terverifikasi sebelumnya.'
                ]);
            }
            $verified = $this->authService->verifyEmail($user);
            if ($verified) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email berhasil diverifikasi!'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi email.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Resend email verification notification
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resendVerification(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus login terlebih dahulu.'
                ], 401);
            }
            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email Anda sudah diverifikasi.'
                ]);
            }
            event(new EmailVerificationRequested($user));
            return response()->json([
                'success' => true,
                'message' => 'Link verifikasi email telah dikirim ulang ke ' . $user->email . '!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang verifikasi email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Verify email from email link (untuk API)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyEmailFromLink(Request $request): JsonResponse
    {
        try {
            if (!$request->hasValidSignature()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link verifikasi tidak valid atau sudah kedaluwarsa.'
                ], 400);
            }
            $user = User::findOrFail($request->route('id'));
            if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link verifikasi tidak valid.'
                ], 400);
            }
            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email sudah terverifikasi sebelumnya.'
                ]);
            }
            $verified = $this->authService->verifyEmail($user);
            if ($verified) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email berhasil diverifikasi!'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi email.'
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}