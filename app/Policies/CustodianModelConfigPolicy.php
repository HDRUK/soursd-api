<?php

namespace App\Policies;

use App\Models\CustodianModelConfig;
use App\Models\CustodianUserHasPermission;
use App\Models\User;

class CustodianModelConfigPolicy
{
    public function before(User $user): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewByCustodian(User $user, int $custodianId): bool
    {
        return $this->belongsToCustodian($user, $custodianId)
            && ($this->isCustodianAdmin($user) || $this->isCustodianApprover($user));
    }

    public function view(User $user, CustodianModelConfig $config): bool
    {
        return $this->viewByCustodian($user, $config->custodian_id);
    }

    public function create(User $user, int $custodianId): bool
    {
        return $this->belongsToCustodian($user, $custodianId) && $this->isCustodianAdmin($user);
    }

    public function update(User $user, CustodianModelConfig $config): bool
    {
        return $this->belongsToCustodian($user, $config->custodian_id) && $this->isCustodianAdmin($user);
    }

    public function delete(User $user, CustodianModelConfig $config): bool
    {
        return $this->update($user, $config);
    }

    public function updateByCustodian(User $user, int $custodianId): bool
    {
        return $this->belongsToCustodian($user, $custodianId) && $this->isCustodianAdmin($user);
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

    private function belongsToCustodian(User $user, int $custodianId): bool
    {
        return optional($user->custodian_user)->custodian_id === $custodianId;
    }
}
