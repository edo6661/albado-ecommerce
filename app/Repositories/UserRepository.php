<?php


namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {}

    public function findById(int $id): ?User
    {
        return $this->model->with(['profile'])->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->with(['profile'])->where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['profile'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAll(): Collection
    {
        return $this->model->with(['profile'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function getUserStatistics(): array
    {
        $totalUsers = $this->model->count();
        $newUsersThisMonth = $this->model->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $verifiedUsers = $this->model->whereNotNull('email_verified_at')->count();
        $adminUsers = $this->model->where('role', UserRole::ADMIN)->count();
        
        return [
            'total_users' => $totalUsers,
            'new_users_this_month' => $newUsersThisMonth,
            'verified_users' => $verifiedUsers,
            'admin_users' => $adminUsers,
            'regular_users' => $totalUsers - $adminUsers,
        ];
    }

    public function getRecentUsers(int $limit = 10): Collection
    {
        return $this->model->with(['profile'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}


