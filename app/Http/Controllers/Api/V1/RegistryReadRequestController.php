<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Custodian;
use App\Models\Registry;
use App\Models\RegistryReadRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistryReadRequests\CreateRegistryReadRequest as CRRR;
use App\Http\Requests\RegistryReadRequests\EditRegistryReadRequest as ERRR;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\Responses;

class RegistryReadRequestController extends Controller
{
    use Responses;
    /**
     * @OA\Post(
     *      path="/api/v1/request_access",
     *      summary="Create a RegistryReadRequest entry",
     *      description="Create a RegistryReadRequest entry",
     *      tags={"Registry"},
     *      summary="RegistryReadRequest@request",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="RegistryReadRequest definition",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="custodian_identifier", type="string", example="AJKHDFEUHE329482kds"),
     *              @OA\Property(property="digital_identifier", type="string", example="HSJFY785615630X99123")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",*
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function request(CRRR $request): JsonResponse
    {
        $custodian = Custodian::where('client_id', $request->header('x-client-id'))->first();
        $registry = Registry::where('digi_ident', $request->only(['digital_identifier']))->first();

        if (!$registry) {
            $this->NotFoundResponse();
        }

        $rrr = RegistryReadRequest::updateOrCreate([
            'updated_at' => Carbon::now(),
            'custodian_id' => $custodian->id,
            'registry_id' => $registry->id,
            'status' => RegistryReadRequest::READ_REQUEST_STATUS_OPEN,
            'approved_at' => null,
        ]);

        if ($rrr) {
            return response()->json([
                'message' => 'success',
                'data' => $rrr->id,
            ], 201);
        }

        return response()->json([
            'message' => 'failed',
            'data' => null,
        ], 500);
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/request_access/{id}",
     *      summary="Edit a RegistryReadRequest entry",
     *      description="Edit a RegistryReadRequest entry",
     *      tags={"Registry"},
     *      summary="RegistryReadRequest@acceptOrReject",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="RegistryReadRequest definition",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="integer", example="123"),
     *              @OA\Property(property="user_id", type="integer", example="123")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",*
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="boolean", example="true")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function acceptOrReject(ERRR $request, int $id): JsonResponse
    {
        $input = $request->only([
            'id',
            'status',
        ]);

        $rrr = RegistryReadRequest::where('id', $id)->first();
        if (!$rrr) {
            return response()->json([
                'message' => 'not found',
                'data' => null,
            ], 404);
        }

        $user = User::where('registry_id', $rrr->registry_id)->first();
        if ($user->user_group !== User::GROUP_USERS) {
            return response()->json([
                'message' => 'you don\'t have access to this record',
                'data' => null,
            ], 403);
        }

        if ($user->registry_id !== $rrr->registry_id) {
            return response()->json([
                'message' => 'you don\'t have access to this record',
                'data' => null,
            ], 403);
        }

        if ($rrr->update([
            'status' => $input['status'],
            'approved_at' => ($input['status'] === RegistryReadRequest::READ_REQUEST_STATUS_APPROVED) ? Carbon::now() : null,
            'rejected_at' => ($input['status'] === RegistryReadRequest::READ_REQUEST_STATUS_REJECTED) ? Carbon::now() : null,
        ])) {
            return response()->json([
                'message' => 'success',
                'data' => true,
            ], 200);
        }

        return response()->json([
            'message' => 'error',
            'data' => null,
        ], 500);
    }
}
