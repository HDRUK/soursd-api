<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalRequest;
use App\Models\ProjectUserCustodianApproval;
use Illuminate\Http\Request;
use App\Http\Traits\Responses;
use App\Models\ProjectHasCustodian;
use App\Models\ProjectHasUser;
use App\Models\Custodian;
use App\Models\Registry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProjectUserCustodianApprovalController extends Controller
{
    use Responses;

    public function show(Request $request, int $custodianId, int $projectId, int $registryId)
    {
        try {
            $custodian = Custodian::findOrFail($custodianId);
            if (!Gate::allows('view', $custodian)) {
                return $this->ForbiddenResponse();
            }

            $registry = $this->resolveAndAuthorize($custodianId, $projectId, $registryId);
            if ($registry instanceof JsonResponse) {
                return $registry;
            }

            $puhca = ProjectUserCustodianApproval::where([
                'project_id' => $projectId,
                'user_id' => $registry->user->id,
                'custodian_id' => $custodianId
            ])->latest('created_at')->first();

            return $this->OKResponse($puhca);
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    public function store(ApprovalRequest $request, int $custodianId, int $projectId, int $registryId)
    {
        try {
            $custodian = Custodian::findOrFail($custodianId);
            if (!Gate::allows('update', $custodian)) {
                return $this->ForbiddenResponse();
            }

            $registry = $this->resolveAndAuthorize($custodianId, $projectId, $registryId);
            if ($registry instanceof JsonResponse) {
                return $registry;
            }

            $approval = ProjectUserCustodianApproval::create([
                'project_id' => $projectId,
                'user_id' => $registry->user->id,
                'custodian_id' => $custodianId,
                'approved' => $validated['approved'],
                'comment' => $validated['comment'],
            ]);

            return $this->CreatedResponse($approval);
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    private function resolveAndAuthorize(int $custodianId, int $projectId, int $registryId): Registry|JsonResponse
    {
        $phc = ProjectHasCustodian::where([
            'project_id' => $projectId,
            'custodian_id' => $custodianId,
        ])->exists();

        if (!$phc) {
            return $this->ForbiddenResponse();
        }

        $registry = Registry::find($registryId);
        if (!$registry || !$registry->user) {
            return $this->NotFoundResponse();
        }

        $phu = ProjectHasUser::where([
            'project_id' => $projectId,
            'user_digital_ident' => $registry->digi_ident,
        ])->exists();

        if (!$phu) {
            return $this->ForbiddenResponse();
        }

        return $registry;
    }
}
