<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Models\Address;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = User::with(['profile', 'address']);

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['cpf'])) {
            $query->where('cpf', $filters['cpf']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                $filters['start_date'],
                $filters['end_date']
            ]);
        }

        return $query->paginate(10);
    }

    public function create(array $data, array $addressIds = []): User
    {
        $user = User::create($data);

        if (!empty($addressIds)) {
            $user->address()->sync($addressIds);
        }

        return $user->load('profile', 'address');
    }

    public function update(User $user, array $data, array $address= []): User
    {
        /**$addressIds**/
        $user->update($data);

        $this->syncAddresses($user, $address);
        //if (!empty($addressIds)) {
        //    $user->address()->sync($addressIds);
        //}

        return $user->load('profile', 'address');
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    private function syncAddresses(User $user, array $addresses)
    {
        if (!empty($addresses)) {
            $addressIds = [];

            foreach ($addresses as $addressData) {
                $address = Address::firstOrCreate([
                    'street' => $addressData['street'],
                    'city'   => $addressData['city'],
                    'state'  => $addressData['state'],
                    'zip'    => $addressData['zip'],
                ]);

                $addressIds[] = $address->id;
            }

            $user->address()->sync($addressIds);
        }
    }
}
