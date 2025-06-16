<?php



namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Exceptions\UserNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $users = $this->userService->getPaginatedUsers($perPage);

        return view('users.index', compact('users'));
    }

    public function show(int $id): View
    {
        try {
            $user = $this->userService->getUserById($id);
            
            if (!$user) {
                abort(404, 'User tidak ditemukan.');
            }

            return view('users.show', compact('user'));
        } catch (UserNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());
            
            return redirect()->route('users.show', $user->id)
                           ->with('success', 'User berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->withErrors(['error' => 'Gagal membuat user. Silakan coba lagi.']);
        }
    }

    public function edit(int $id): View
    {
        try {
            $user = $this->userService->getUserById($id);
            
            if (!$user) {
                abort(404, 'User tidak ditemukan.');
            }

            return view('users.edit', compact('user'));
        } catch (UserNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());
            
            return redirect()->route('users.show', $user->id)
                           ->with('success', 'User berhasil diupdate.');
        } catch (UserNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()
                        ->withErrors(['error' => 'Gagal mengupdate user. Silakan coba lagi.']);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->userService->deleteUser($id);
            
            if ($deleted) {
                return redirect()->route('users.index')
                               ->with('success', 'User berhasil dihapus.');
            }
            
            return back()->withErrors(['error' => 'Gagal menghapus user.']);
        } catch (UserNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus user. Silakan coba lagi.']);
        }
    }
}
