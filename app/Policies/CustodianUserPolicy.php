<?php

namespace App\Policies;

use App\Models\CustodianUser;
use App\Models\CustodianUserHasPermission;
use App\Models\User;

class CustodianUserPolicy
{
    public function create(User $user): bool
    {
        return $this->isCustodianAdmin($user);
    }

    public function update(User $user, CustodianUser $custodianUser): bool
    {
        return $this->isCustodianAdmin($user) && $this->sameCustodian($user, $custodianUser);
    }

    public function delete(User $user, CustodianUser $custodianUser): bool
    {
        return $this->update($user, $custodianUser);
    }

    public function invite(User $user, CustodianUser $custodianUser): bool
    {
        return $this->update($user, $custodianUser);
    }

    private function isCustodianAdmin(User $user): bool
    {
        if (! $user->custodian_user_id) {
            return false;
        }

        return CustodianUserHasPermission::where('custodian_user_id', $user->custodian_user_id)
            ->whereHas('permission', fn ($query) => $query->where('name', 'CUSTODIAN_ADMIN'))
            ->exists();
    }

    private function sameCustodian(User $user, CustodianUser $custodianUser): bool
    {
        return optional($user->custodian_user)->custodian_id === $custodianUser->custodian_id;
    }
}
