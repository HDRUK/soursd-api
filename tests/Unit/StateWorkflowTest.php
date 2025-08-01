<?php

namespace Tests\Unit;

use App\Models\Affiliation;
use App\Models\User;
use App\Models\State;
use App\Models\Project;
use App\Models\Organisation;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class StateWorkflowTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Config::set('workflow.transitions.enforced', true);
    }

    public function test_the_application_can_track_user_state(): void
    {
        $user = User::where('user_group', 'USERS')->first();
        $user->setState(State::STATE_PENDING);

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_PENDING)->first()->id,
            'stateable_id' => $user->id,
        ]);

        $this->assertTrue($user->getState() === State::STATE_PENDING);
        $this->assertTrue($user->canTransitionTo(State::STATE_VALIDATED) === false);
    }

    public function test_the_application_can_move_users_through_all_logical_states(): void
    {
        $user = User::where('user_group', 'USERS')->first();
        $user->setState(State::STATE_PENDING); // Original State

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_PENDING)->first()->id,
            'stateable_id' => $user->id,
        ]);

        $this->assertTrue($user->getState() === State::STATE_PENDING);
        $this->assertTrue($user->canTransitionTo(State::STATE_FORM_RECEIVED) === true);

        $user->transitionTo(State::STATE_FORM_RECEIVED);

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_FORM_RECEIVED)->first()->id,
            'stateable_id' => $user->id,
        ]);

        $this->assertTrue($user->getState() === State::STATE_FORM_RECEIVED);

        $user->transitionTo(State::STATE_VALIDATION_IN_PROGRESS);

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_VALIDATION_IN_PROGRESS)->first()->id,
            'stateable_id' => $user->id,
        ]);

        $this->assertTrue($user->getState() === State::STATE_VALIDATION_IN_PROGRESS);

        $user->transitionTo(State::STATE_MORE_USER_INFO_REQ);

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_MORE_USER_INFO_REQ)->first()->id,
            'stateable_id' => $user->id,
        ]);

        $this->assertTrue($user->getState() === State::STATE_MORE_USER_INFO_REQ);

        $user->transitionTo(State::STATE_ESCALATE_VALIDATION);

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_ESCALATE_VALIDATION)->first()->id,
            'stateable_id' => $user->id,
        ]);

        $this->assertTrue($user->getState() === State::STATE_ESCALATE_VALIDATION);

        $user->transitionTo(State::STATE_VALIDATED);

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_VALIDATED)->first()->id,
            'stateable_id' => $user->id,
        ]);

        $this->assertTrue($user->getState() === State::STATE_VALIDATED);
    }

    public function test_the_application_can_track_project_state(): void
    {
        $proj = Project::where('id', 1)->first();
        $proj->setState(State::STATE_PENDING);

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_PENDING)->first()->id,
            'stateable_id' => $proj->id,
        ]);

        $this->assertTrue($proj->getState() === State::STATE_PENDING);
        $this->assertTrue($proj->canTransitionTo(State::STATE_VALIDATED) === false);
    }

    public function test_the_application_can_track_organisation_state(): void
    {
        $org = Organisation::where('id', 1)->first();
        $org->setState(State::STATE_PENDING);

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_PENDING)->first()->id,
            'stateable_id' => $org->id,
        ]);


        $this->assertTrue($org->getState() === State::STATE_PENDING);
        $this->assertTrue($org->canTransitionTo(State::STATE_VALIDATED) === false);
    }

    public function test_the_application_can_track_affiliation_state(): void
    {
        $affiliation = Affiliation::where('id', 1)->first();

        $this->assertTrue($affiliation->getState() === State::STATE_AFFILIATION_PENDING);
        $this->assertTrue($affiliation->canTransitionTo(State::STATE_AFFILIATION_APPROVED) === true);
        $this->assertTrue($affiliation->canTransitionTo(State::STATE_AFFILIATION_REJECTED) === true);

        $affiliation->transitionTo(State::STATE_AFFILIATION_APPROVED);

        $this->assertDatabaseHas('model_states', [
            'state_id' => State::where('slug', State::STATE_AFFILIATION_APPROVED)->first()->id,
            'stateable_id' => $affiliation->id,
        ]);

        $this->assertTrue($affiliation->canTransitionTo(State::STATE_AFFILIATION_APPROVED) === false);
        $this->assertTrue($affiliation->canTransitionTo(State::STATE_AFFILIATION_PENDING) === false);
        $this->assertTrue($affiliation->canTransitionTo(State::STATE_AFFILIATION_REJECTED) === true);
    }
}
