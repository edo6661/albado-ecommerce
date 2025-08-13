<?php
namespace App\Services;
use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Contracts\Services\AddressServiceInterface;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
class AddressService implements AddressServiceInterface
{
    public function __construct(
        protected AddressRepositoryInterface $addressRepository
    ) {}
    public function getUserAddresses(int $userId): Collection
    {
        return $this->addressRepository->findByUserId($userId);
    }
    public function getAddressById(int $id): ?Address
    {
        return $this->addressRepository->findById($id);
    }
    public function createAddress(int $userId, array $data): Address
    {
        $data['user_id'] = $userId;
        $userAddresses = $this->getUserAddresses($userId);
        $setToDefault = $userAddresses->isEmpty() || ($data['is_default'] ?? false);
        $data['is_default'] = false;
        $address = $this->addressRepository->create($data);
        if ($setToDefault) {
            $this->addressRepository->setAsDefault($address);
        }
        return $address->fresh();
    }
    public function updateAddress(int $id, array $data): ?Address
    {
        $address = $this->getAddressById($id);
        if (!$address) {
            return null;
        }
        if (isset($data['is_default']) && $data['is_default'] === true) {
            $this->addressRepository->setAsDefault($address);
        }
        if (isset($data['is_default']) && $data['is_default'] === false) {
            $this->addressRepository->unsetAsDefault($address);
        }
        
        unset($data['is_default']);
        if (!empty($data)) {
            $this->addressRepository->update($address, $data);
        }
        return $address->fresh();
    }
    public function deleteAddress(int $id): bool
    {
        $address = $this->getAddressById($id);
        if (!$address) {
            return false;
        }
        return $this->addressRepository->delete($address);
    }
    public function setDefaultAddress(int $id): bool
    {
        $address = $this->getAddressById($id);
        if (!$address) {
            return false;
        }
        return $this->addressRepository->setAsDefault($address);
    }
}