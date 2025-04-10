<?php

namespace App\TriggerEmail;

use App\Jobs\SendEmailJob;
use App\Models\Custodian;
use App\Models\CustodianUser;
use App\Models\Organisation;
use App\Models\PendingInvite;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Hdruk\LaravelMjml\Models\EmailTemplate;

class TriggerEmail
{
    public function spawnEmail(array $input): void
    {
        $replacements = [];
        $newRecipients = [];
        $invitedBy = [];
        $template = null;

        $type = $input['type'];
        $unclaimedUserId = isset($input['unclaimed_user_id']) ? $input['unclaimed_user_id'] : null;
        $to = $input['to'];
        $by = isset($input['by']) ? $input['by'] : null;
        $for = isset($input['for']) ? $input['for'] : null;
        $identifier = $input['identifier'];
        switch (strtoupper($type)) {
            case 'AFFILIATION':
                if ($input['email'] === '') {
                    // Log and return
                    return;
                }

                $user = User::where('id', $to)->first();
                $template = EmailTemplate::where('identifier', $identifier)->first();

                $newRecipients = [
                    'id' => $user->id,
                    'email' => $input['pro_email'],
                ];

                $replacements = [
                    '[[users.first_name]]' => $user->first_name,
                    '[[env(SUPPORT_EMAIL)]]' => env('SUPPORT_EMAIL'),
                ];
                break;
            case 'USER_WITHOUT_ORGANISATION':
                $user = User::where('id', $to)->first();
                $template = EmailTemplate::where('identifier', $identifier)->first();

                $newRecipients = [
                    'id' => $user->id,
                    'email' => $user->email,
                ];

                $replacements = [
                    '[[users.first_name]]' => $user->first_name,
                    '[[users.last_name]]' => $user->last_name,
                    '[[users.created_at]]' => $user->created_at,
                ];

                PendingInvite::create([
                    'user_id' => $user->id,
                    'invite_sent_at' => Carbon::now(),
                    'status' => config('speedi.invite_status.PENDING'),
                ]);
                break;
            case 'USER':
                $user = User::where('id', $to)->first();
                $organisation = Organisation::where('id', $by)->first();
                $template = EmailTemplate::where('identifier', $identifier)->first();

                $newRecipients = [
                    'id' => $user->id,
                    'email' => $user->email,
                ];

                $replacements = [
                    '[[organisations.organisation_name]]' => $organisation->organisation_name,
                    '[[users.first_name]]' => $user->first_name,
                    '[[users.last_name]]' => $user->last_name,
                    '[[users.created_at]]' => $user->created_at,
                    '[[env(SUPPORT_EMAIL)]]' => env('SUPPORT_EMAIL'),
                ];

                PendingInvite::create([
                    'user_id' => $user->id,
                    'organisation_id' => $organisation->id,
                    'invite_sent_at' => Carbon::now(),
                    'status' => config('speedi.invite_status.PENDING'),
                ]);
                break;
            case 'USER_DELEGATE':
                $delegate = User::where('id', $to)->first();
                $user = User::where('id', $for)->first();
                $organisation = Organisation::where('id', $by)->first();
                $template = EmailTemplate::where('identifier', $identifier)->first();


                $newRecipients = [
                    'id' => $delegate->id,
                    'email' => $delegate->email,
                ];

                $invitedBy = [
                    'id' => $organisation->id,
                    'email' => $organisation->lead_applicant_email,
                ];

                $replacements = [
                    '[[organisation_name]]' => $organisation->organisation_name,
                    '[[delegate_first_name]]' => $delegate->first_name,
                    '[[delegate_last_name]]' => $delegate->last_name,
                    '[[user_first_name]]' => $user->first_name,
                    '[[user_last_name]]' => $user->last_name,
                    '[[user_created_at]]' => $user->created_at,
                    '[[env(AP_NAME)]]' => env('APP_NAME'),
                    '[[env(INVITE_TIME_HOURS)]]' => env('INVITE_TIME_HOURS'),
                    '[[env(SUPPORT_EMAIL)]]' => env('SUPPORT_EMAIL'),
                    '[[organisations.id]]' => $organisation->id,
                ];

                PendingInvite::create([
                    'user_id' => $delegate->id,
                    'organisation_id' => $organisation->id,
                    'invite_sent_at' => Carbon::now(),
                    'status' => config('speedi.invite_status.PENDING'),
                ]);
                break;
            case 'CUSTODIAN':
                $custodian = Custodian::where('id', $to)->first();
                $template = EmailTemplate::where('identifier', $identifier)->first();

                $newRecipients = [
                    'id' => $custodian->id,
                    'email' => $custodian->contact_email,
                ];

                $replacements = [
                    '[[custodian.name]]' => $custodian->name,
                    '[[env(SUPPORT_EMAIL)]]' => env('SUPPORT_EMAIL'),
                ];

                PendingInvite::create([
                    'user_id' => $unclaimedUserId,
                    'status' => config('speedi.invite_status.PENDING'),
                    'invite_sent_at' => Carbon::now()
                ]);

                break;
            case 'CUSTODIAN_USER':
                $custodianUser = CustodianUser::with('userPermissions.permission')->where('id', $to)->first();
                $custodian = Custodian::where('id', $custodianUser->custodian_id)->first();

                $role_description = '';

                if (count($custodianUser->userPermissions) > 0) {
                    $permission = Permission::where('id', $custodianUser->userPermissions[0]->permission_id)->first();

                    $role_description = "as an $permission->description";
                }

                $template = EmailTemplate::where('identifier', $identifier)->first();

                $newRecipients = [
                    'id' => $custodianUser->id,
                    'email' => $custodianUser->email,
                ];

                $replacements = [
                    '[[custodian_user.id]]' => $custodianUser->id,
                    '[[custodian_user.first_name]]' => $custodianUser->first_name,
                    '[[custodian_user.last_name]]' => $custodianUser->last_name,
                    '[[custodian.name]]' => $custodian->name,
                    '[[custodian.id]]' => $custodian->id,
                    '[[role.description]]' => $role_description,
                    '[[env(SUPPORT_EMAIL)]]' => env('SUPPORT_EMAIL'),
                ];

                PendingInvite::create([
                    'user_id' => $unclaimedUserId,
                    'status' => config('speedi.invite_status.PENDING'),
                    'invite_sent_at' => Carbon::now()
                ]);

                break;
            case 'ORGANISATION':
                $organisation = Organisation::where('id', $to)->first();
                $template = EmailTemplate::where('identifier', $identifier)->first();

                $newRecipients = [
                    'id' => $organisation->id,
                    'email' => $organisation->lead_applicant_email,
                ];

                $replacements = [
                    '[[organisation.organisation_name]]' => $organisation->organisation_name,
                    '[[env(SUPPORT_EMAIL)]]' => env('SUPPORT_EMAIL'),
                ];

                PendingInvite::create([
                    'user_id' => $unclaimedUserId,
                    'status' => config('speedi.invite_status.PENDING'),
                    'invite_sent_at' => Carbon::now()
                ]);

                break;
            default: // Unknown type.
                break;
        }

        SendEmailJob::dispatch($newRecipients, $template, $replacements);
    }
}
