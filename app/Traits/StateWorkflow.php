<?php

namespace App\Traits;

use Exception;
use App\Models\State;
use App\Models\ModelState;

/**
 * @property-read \App\Models\ModelState|null $modelState
 */
trait StateWorkflow
{
    protected static array $defaultTransitions = [
        State::STATE_REGISTERED => [
            State::STATE_PENDING,
            State::STATE_FORM_RECEIVED,
        ],
        State::STATE_PENDING => [
            State::STATE_FORM_RECEIVED,
            State::STATE_VALIDATION_IN_PROGRESS,
        ],
        State::STATE_FORM_RECEIVED => [
            State::STATE_VALIDATION_IN_PROGRESS,
            State::STATE_MORE_USER_INFO_REQ,
        ],
        State::STATE_VALIDATION_IN_PROGRESS => [
            State::STATE_VALIDATION_COMPLETE,
            State::STATE_MORE_USER_INFO_REQ,
            State::STATE_ESCALATE_VALIDATION,
            State::STATE_VALIDATED,
        ],
        State::STATE_VALIDATION_COMPLETE => [
            State::STATE_ESCALATE_VALIDATION,
            State::STATE_VALIDATED,
        ],
        State::STATE_MORE_USER_INFO_REQ => [
            State::STATE_ESCALATE_VALIDATION,
            State::STATE_VALIDATED,
        ],
        State::STATE_ESCALATE_VALIDATION => [
            State::STATE_VALIDATED,
        ],
        State::STATE_VALIDATED => [],

        State::STATE_AFFILIATION_INVITED => [
            State::STATE_AFFILIATION_PENDING
        ],
        State::STATE_AFFILIATION_PENDING => [
            State::STATE_AFFILIATION_APPROVED,
            State::STATE_AFFILIATION_REJECTED
        ],
        State::STATE_AFFILIATION_APPROVED => [
            State::STATE_AFFILIATION_REJECTED
        ],
        State::STATE_AFFILIATION_REJECTED => [
            State::STATE_AFFILIATION_APPROVED
        ]
    ];

    public function modelState()
    {
        return $this->morphOne(ModelState::class, 'stateable');
    }

    public function setState(string $stateSlug)
    {
        $state = State::where('slug', $stateSlug)->firstOrFail();

        if ($this->modelState) {
            $this->modelState->update(['state_id' => $state->id]);
        } else {
            $this->modelState()->create(['state_id' => $state->id]);
        }

        // Reload the relationship. To note, a call to ->refresh() would do the
        // same thing, but refresh queries for the entire model and relationships,
        // whereas this has a far smaller footprint for our needs
        $this->load('modelState');
    }

    public function getState(): ?string
    {
        return $this->modelState ? $this->modelState->state->slug : null;
    }

    public function isInState(string $stateSlug): bool
    {
        return $this->modelState && $this->modelState->state->slug === $stateSlug;
    }

    public function canTransitionTo(string $newStateSlug): bool
    {
        if (!config('workflow.transitions.enforced')) {
            return true;
        }
        $currentState = $this->getState();
        $transitions = static::getTransitions();
        return (isset($transitions[$currentState]) && in_array($newStateSlug, $transitions[$currentState]));
    }

    public function transitionTo(string $newStateSlug)
    {
        if (!$this->canTransitionTo($newStateSlug)) {
            throw new Exception('invalid state transition');
        }
        $this->setState($newStateSlug);
        $this->save(); // make sure triggers observers
    }

    public static function getTransitions(): array
    {
        return static::$defaultTransitions;
    }

    public static function getAllStates(): array
    {
        $transitions = static::getTransitions();
        $keys = array_keys($transitions);
        $values = array_merge(...array_values($transitions));
        $allStates = array_unique(array_merge($keys, $values));
        return $allStates;
    }
}
