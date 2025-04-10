<?php

namespace App\Observers;

use TriggerEmail;
use App\Models\User;
use App\Models\Affiliation;
use App\Models\RegistryHasAffiliation;
use App\Traits\AffiliationCompletionManager;

class AffiliationObserver
{
    use AffiliationCompletionManager;

    public function created(Affiliation $affiliation): void
    {
        $this->handleChange($affiliation);
    }

    public function updated(Affiliation $affiliation): void
    {
        $this->handleChange($affiliation);
    }

    public function deleted(Affiliation $affiliation): void
    {
        $this->handleChange($affiliation);
    }

    protected function handleChange(Affiliation $affiliation): void
    {
        $registryIds = RegistryHasAffiliation::where('affiliation_id', $affiliation->id)
            ->distinct()
            ->select('registry_id')
            ->pluck('registry_id');

        foreach ($registryIds as $registryId) {
            $this->updateActionLog($registryId);
        }

        $this->emailDelegates($affiliation);

    }

    protected function emailDelegates(Affiliation $affiliation){
        $isComplete = $this->checkComplete($affiliation);

        $originalAttributes = $affiliation->getOriginal();
        $originalAffiliation = new Affiliation($originalAttributes);
        $wasIncomplete = !$this->checkComplete($originalAffiliation);

        if(!($isComplete && $wasIncomplete)){
            return;
        }

        $orgId = $affiliation->organisation_id;

        $delegateIds = User::where([
            'organisation_id' => $orgId,
            'is_delegate' => 1
        ])->select('id')->pluck('id');

        $firstRha = $affiliation->registryHasAffiliations()->first();
        $userId = optional($firstRha)?->registry?->user?->id;
        if(is_null($userId)){
            return;
        }
        foreach ($delegateIds as $delegateId) {
            $input = [
                'type' => 'USER_DELEGATE',
                'to' => $delegateId,
                'by' => $orgId,
                'for' => $userId,
                'identifier' => 'delegate_sponsor'
            ];
    
            TriggerEmail::spawnEmail($input);
        }
    }

    protected function checkComplete(Affiliation $affiliation){
            return !empty($affiliation->member_id) &&
                !empty($affiliation->relationship) &&
                !empty($affiliation->from);
    }

}
