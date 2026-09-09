<?php

namespace App\Policies;

use App\Models\Affiliation;
use App\Models\User;

class AffiliationPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * View the affiliation list for a registry (custodians/organisations can view any
     * registry's affiliations, a plain user can only view their own).
     */
    public function viewByRegistry(User $user, int $registryId): bool
    {
        return $user->inGroup([
            User::GROUP_CUSTODIANS,
            User::GROUP_ORGANISATIONS,
        ]) || $user->registry_id === $registryId;
    }

    /**
     * Create an affiliation entry on behalf of a registry — only the registry owner
     * can self-declare their own affiliation history.
     */
    public function createForRegistry(User $user, int $registryId): bool
    {
        return $user->registry_id === $registryId;
    }

    /**
     * Update, resend verification for, or delete a specific affiliation — only the
     * registry that owns the affiliation may manage it.
     */
    public function manage(User $user, Affiliation $affiliation): bool
    {
        return $user->registry_id === $affiliation->registry_id;
    }

    /**
     * Approve or reject an affiliation — only the employing organisation may action it.
     */
    public function approve(User $user, Affiliation $affiliation): bool
    {
        return $user->user_group === User::GROUP_ORGANISATIONS
            && $user->organisation_id === $affiliation->organisation_id;
    }
}
