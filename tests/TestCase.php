<?php

namespace Tests;

use Keycloak;
use Tests\Traits\RefreshDatabaseLite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabaseLite;

    protected function setUp(): void
    {
        parent::setUp();
        $this->liteSetUp();
        $this->disableMiddleware();
        $this->disableObservers();

        Keycloak::shouldReceive('checkUserExists')
            ->andReturn(true);

        Keycloak::shouldReceive('determineUserGroup')
            ->andReturn('USERS');
    }

    protected function disableMiddleware(): void
    {
        $this->withoutMiddleware();
    }

    protected function enableMiddleware(): void
    {
        $this->withMiddleware();
    }

    protected function disableObservers()
    {
        Model::unsetEventDispatcher();
    }

    protected function enableObservers()
    {
        Model::setEventDispatcher(app('events'));
    }

}
