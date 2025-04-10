<?php

namespace App\Traits;

use Exception;
use App\Models\State;
use App\Models\ModelState;

trait StateWorkflow
{
    protected array $transitions = [
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
        State::STATE_PROJECT_PENDING => [
            State::STATE_PROJECT_PENDING,
            State::STATE_PROJECT_APPROVED,
        ],
        State::STATE_PROJECT_APPROVED => [
            State::STATE_PROJECT_APPROVED,
            State::STATE_PROJECT_COMPLETED
        ],
        State::STATE_PROJECT_COMPLETED => [
            State::STATE_PROJECT_COMPLETED
        ],
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
        $currentState = $this->getState();
        return (isset($this->transitions[$currentState]) && in_array($newStateSlug, $this->transitions[$currentState]));
    }

    public function transitionTo(string $newStateSlug)
    {
        if (!$this->canTransitionTo($newStateSlug)) {
            throw new Exception('invalid state transition');
        }
        $this->setState($newStateSlug);
    }
}
