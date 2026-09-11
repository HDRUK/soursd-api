<?php

namespace App\Services\WebhookTranslators;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DebugLog;
use App\Models\Identity;
use App\Models\Registry;
use Illuminate\Http\Request;
use App\Http\Traits\HmacSigning;

class VeriffTranslator implements WebhookTranslationInterface
{
    use HmacSigning;

    public const ACTION_STARTED = 'started';
    public const ACTION_SUBMITTED = 'submitted';
    public const ACTION_DECISION = 'decision';

    public function validateSignature(Request $request): bool
    {
        $verdict = $this->verifySignature(
            $request->getContent(),
            config('speedi.system.idvt_supplier_secret_key'),
            $request->header('x-hmac-signature')
        );

        return $verdict;
    }

    public function translate(array $data): array
    {
        return $this->translateAction($data);
    }

    public function translateAction(array $data): array
    {
        // Forms a decision - weirdly lacks the action field, for some reason
        if (isset($data['verification'])) {
            return [
                'action' => self::ACTION_DECISION,
                'status' => $data['status'] ?? null,
                'verification' => $data['verification'] ?? null,
                'verified' => $data['verification']['status'] ?? null,
                'decision_time' => $data['verification']['decisionTime'] ?? null,
                'registry_id' => base64_decode($data['verification']['vendorData']),
            ];
        }

        switch (strtolower($data['action'])) {
            case self::ACTION_STARTED:
                return [
                    'action' => self::ACTION_STARTED,
                    'attempt_id' => $data['attemptId'] ?? null,
                    'registry_id' => base64_decode($data['vendorData']),
                ];
            case self::ACTION_SUBMITTED:
                // Ignore this, just means the user has submitted the job on Veriff side
                // but we don't need to do anything with it
                return [];
            default:
                // Ignore this.
                return [];
        }
    }


    public function saveContext(array $data): void
    {
        $registry = Registry::where('digi_ident', $data['registry_id'])->first();
        if (!$registry) {
            DebugLog::create([
                'class' => VeriffTranslator::class,
                'log' => 'Attempt to save veriff context with unknown registry_id - ' . json_encode($data) . ' likely the submitted event - can ignore',
            ]);

            return;
        }

        $identity = Identity::where('registry_id', $registry->id)->first();

        if (!$identity) {
            DebugLog::create([
                'class' => VeriffTranslator::class,
                'log' => 'Creating new identity for registry_id - ' . $registry->id,
            ]);

            $identity = Identity::create([
                'registry_id' => $registry->id,
            ]);
        }

        Log::debug('VeriffTranslator::saveContext - ' . json_encode($data));

        if (isset($data['action']) && $data['action'] === self::ACTION_STARTED) {

            DebugLog::create([
                'class' => VeriffTranslator::class,
                'log' => 'Attempt started for veriff for ' . $registry->id,
            ]);

            $identity->update([
                'idvt_started_at' => Carbon::now(),
                'idvt_attempt_id' => $data['attempt_id'] ?? null,
                'idvt_context' => json_encode($data),
            ]);
            return;
        }

        Log::debug('VeriffTranslator::saveContext - ' . json_encode($data));
        if (isset($data['action']) && $data['action'] === self::ACTION_DECISION) {

            DebugLog::create([
                'class' => VeriffTranslator::class,
                'log' => 'Attempt descion ' . json_encode($data) . ' ' . $registry->id,
            ]);

            $identity->update([
                'idvt_success' => $data['verification']['status'] === 'approved' ? 1 : 0,
                'idvt_result_text' => $data['verification']['status'],
                'idvt_context' => json_encode($data),
                'idvt_completed_at' => Carbon::now(),
                'idvt_identification_number' => $data['verification']['person']['idNumber'] ?? null,
                'idvt_document_type' => $data['verification']['document']['type'] ?? null,
                'idvt_document_number' => $data['verification']['document']['number'] ?? null,
                'idvt_document_country' => $data['verification']['document']['country'] ?? null,
                'idvt_document_valid_until' => $data['verification']['document']['validUntil'] ?? null,
                'idvt_document_first_name' => $data['verification']['person']['firstName'] ?? null,
                'idvt_document_last_name' => $data['verification']['person']['lastName'] ?? null,
                'idvt_attempt_id' => $data['verification']['attemptId'] ?? null,
                'idvt_context_id' => $data['verification']['id'] ?? null,
                'idvt_document_dob' => $data['verification']['person']['dateOfBirth'] ?? null,
            ]);
        }
    }
}
