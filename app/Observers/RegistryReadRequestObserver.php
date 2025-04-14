<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Custodian;
use App\Models\RegistryReadRequest;
use App\Notifications\RegistryReadRequestNotification;
use Illuminate\Support\Facades\Notification;

class RegistryReadRequestObserver
{
    public const WEBHOOK_EVENT_TRIGGER_NAME = 'registry-read-request';

    public function created(RegistryReadRequest $readRequest)
    {
        // Send notification to the Registry owner
        $user = User::where('registry_id', $readRequest->registry_id)->first();
        $custodian = Custodian::where('id', $readRequest->custodian_id)->first();

        Notification::send($user, new RegistryReadRequestNotification($readRequest, $custodian->name));
    }
}
