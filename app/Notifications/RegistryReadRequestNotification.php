<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RegistryReadRequestNotification extends Notification
{
    use Queueable;

    private $message;
    private $details;
    private $buttonUrls;

    public function __construct($readRequest, $custodianName)
    {
        $this->message = $custodianName . ' requested access to view your SOURSD data on ' . $readRequest->created_at->toFormattedDayDateString() . '.';
        $this->details = 'You can either approve or deny the request from ' . $custodianName . ' below. The Data Custodian will be notified of your decision.';
        $this->buttonUrls = [
            'Approve' => $readRequest->id,
            'Deny' => $readRequest->id,
        ];
    }

    public function via($notifiable)
    {
        return [
            'database',
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'details' => $this->details,
            'buttonUrls' => $this->buttonUrls,
            'time' => Carbon::now(),
        ];
    }
}
