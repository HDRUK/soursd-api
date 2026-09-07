<?php

namespace App\Policies;

use App\Models\CustodianUser;
use App\Models\CustodianUserHasPermission;
use App\Models\User;

class CustodianUserPolicy
{
    public function create(User $user): bool
    {
        return $this->isCustodianAdmin($user) || $this->isCustodianApprover($user);
    }

    public function update(User $user, CustodianUser $custodianUser): bool
    {
        if (! $this->sameCustodian($user, $custodianUser)) {
            return false;
        }

        if ($this->isCustodianAdmin($user)) {
            return true;
        }

        // A CUSTODIAN_APPROVER may manage other custodian users, but not a CUSTODIAN_ADMIN.
        return $this->isCustodianApprover($user) && ! $this->custodianUserHasPermission($custodianUser->id, 'CUSTODIAN_ADMIN');
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
        return $user->custodian_user_id
            && $this->custodianUserHasPermission($user->custodian_user_id, 'CUSTODIAN_ADMIN');
    }

    private function isCustodianApprover(User $user): bool
    {
        return $user->custodian_user_id
            && $this->custodianUserHasPermission($user->custodian_user_id, 'CUSTODIAN_APPROVER');
    }

    private function custodianUserHasPermission(int $custodianUserId, string $permissionName): bool
    {
        return CustodianUserHasPermission::where('custodian_user_id', $custodianUserId)
            ->whereHas('permission', fn ($query) => $query->where('name', $permissionName))
            ->exists();
    }

    private function sameCustodian(User $user, CustodianUser $custodianUser): bool
    {
        return optional($user->custodian_user)->custodian_id === $custodianUser->custodian_id;
    }
}
