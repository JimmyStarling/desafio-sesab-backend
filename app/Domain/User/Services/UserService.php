<?php
namespace App\Domain\User\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class UserService
{
    /**
     * Check user profile and assign default profile if not authenticated.
     *
     * @param bool $is_authenticated
     * @param array $data
     * @return array
     */
    public function checkProfile(bool $is_authenticated, ?int $requestedProfileId): int
    {
        $profileId = 3; // Default profile ID

        if ($is_authenticated && Gate::allows('create-user-with-profile', $requestedProfileId)) {
            $profileId = $requestedProfileId;
        }

        return $profileId;
    }

    /**
     * Sync addresses for a user.
     *
     * @param User $user
     * @param array $addresses
     * @return User
     */
    public function syncAddresses(User $user, array $addresses): void
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