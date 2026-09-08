<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Custodian;
use Illuminate\Http\Request;
use App\Models\DecisionModel;
use App\Http\Traits\Responses;
use App\Models\DecisionModelType;
use App\Traits\CommonFunctions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\CustodianModelConfig;
use App\Traits\Notifications\NotificationCustodianManager;
use App\Http\Requests\CustodianModelConfig\GetDecisionModelsRequest;
use App\Http\Requests\CustodianModelConfig\UpdateDecisionModelsRequest;
use App\Http\Requests\CustodianModelConfig\DeleteCustodianModelConfig;
use App\Http\Requests\CustodianModelConfig\CreateCustodianModelConfigRequest;
use App\Http\Requests\CustodianModelConfig\UpdateCustodianModelConfigRequest;
use App\Http\Requests\CustodianModelConfig\GetCustodianModelConfigByCustodian;

class CustodianModelConfigController extends Controller
{
    use CommonFunctions;
    use Responses;
    use NotificationCustodianManager;

    /**
     * @OA\Get(
     *      path="/api/v1/custodian_config/{id}",
     *      operationId="custodianModelConfigGetByCustodianID",
     *      x={"internal"="true"},
     *      summary="Return a list of Custodian config",
     *      description="Return a list of Custodian config",
     *      tags={"CustodianModelConfig"},
     *      summary="CustodianModelConfig@getByCustodianID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="CustodianModelConfig entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="CustodianModelConfig entry ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data",
     *                   ref="#/components/schemas/CustodianModelConfig"
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function getByCustodianID(GetCustodianModelConfigByCustodian $request, int $id): JsonResponse
    {
        if (! Gate::allows('viewByCustodian', [CustodianModelConfig::class, $id])) {
            return $this->ForbiddenResponse();
        }

        $conf = CustodianModelConfig::where('custodian_id', $id)->get();
        if (!$conf) {
            return $this->NotFoundResponse();
        }

        return $this->OKResponse($conf);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/custodian_config",
     *      operationId="custodianModelConfigStore",
     *      x={"internal"="true"},
     *      summary="Create a CustodianModelConfig entry",
     *      description="Create a CustodianModelConfig entry",
     *      tags={"CustodianModelConfig"},
     *      summary="CustodianModelConfig@store",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="CustodianModelConfig definition",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/CustodianModelConfig"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/CustodianModelConfig"
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function store(CreateCustodianModelConfigRequest $request): JsonResponse
    {
        try {
            $input = $request->only(app(CustodianModelConfig::class)->getFillable());

            if (! Gate::allows('create', [CustodianModelConfig::class, (int) $input['custodian_id']])) {
                return $this->ForbiddenResponse();
            }

            $conf = CustodianModelConfig::firstOrCreate([
                'decision_model_id' => $input['decision_model_id'],
                'custodian_id' => $input['custodian_id'],
            ], [
                'active' => $input['active'],
            ]);

            return $this->CreatedResponse($conf->id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // LS - Not sure if we'd use this, leaving in for now
    //
    // public function show(Request $request, int $id): JsonResponse
    // {
    //     //
    // }

    /**
     * @OA\Put(
     *      path="/api/v1/custodian_config/{id}",
     *      operationId="custodianModelConfigUpdate",
     *      x={"internal"="true"},
     *      summary="Update an CustodianModelConfig entry",
     *      description="Update an CustodianModelConfig entry",
     *      tags={"CustodianModelConfig"},
     *      summary="CustodianModelConfig@update",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="CustodianModelConfig entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="CustodianModelConfig entry ID",
     *         ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="CustodianModelConfig definition",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/CustodianModelConfig"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data",
     *                  ref="#/components/schemas/CustodianModelConfig"
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function update(UpdateCustodianModelConfigRequest $request, int $id): JsonResponse
    {
        try {
            $input = $request->only(app(CustodianModelConfig::class)->getFillable());
            $conf = CustodianModelConfig::findOrFail($id);

            if (! Gate::allows('update', $conf)) {
                return $this->ForbiddenResponse();
            }

            $conf->update($input);


            return $this->OKResponse($conf);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/custodian_config/{id}",
     *      operationId="custodianModelConfigDestroy",
     *      x={"internal"="true"},
     *      summary="Delete a CustodianModelConfig entry from the system by ID",
     *      description="Delete a CustodianModelConfig entry from the system",
     *      tags={"CustodianModelConfig"},
     *      summary="CustodianModelConfig@destroy",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="CustodianModelConfig entry ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="CustodianModelConfig entry ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *           ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="error")
     *          )
     *      )
     * )
     */
    public function destroy(DeleteCustodianModelConfig $request, int $id): JsonResponse
    {
        try {
            $conf = CustodianModelConfig::where('id', $id)->first();

            if (! Gate::allows('delete', $conf)) {
                return $this->ForbiddenResponse();
            }

            $conf->update([
                'active' => 0,
            ]);

            return $this->OKResponse(null);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    /**
     * @OA\Get(
     *      path="/api/v1/custodian_config/{custodianId}/decision_models",
     *      operationId="custodianModelConfigGetDecisionModels",
     *      x={"internal"="true"},
     *      summary="Get decision models for custodian config",
     *      description="Retrieve decision models associated with custodian config based on the specified decision_model_type",
     *      tags={"CustodianModelConfig"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="custodianId",
     *          in="path",
     *          required=true,
     *          description="ID of the custodian",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="decision_model_type",
     *          in="query",
     *          required=true,
     *          description="Type of decision model to retrieve",
     *          @OA\Schema(
     *              type="string",
     *              enum={"decision_model", "user_validation_rules", "org_validation_rules"}
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Decision Model A"),
     *                      @OA\Property(property="decision_model_type_id", type="integer", example=1),
     *                      @OA\Property(property="description", type="string", nullable=true, example="This is a decision model for process A"),
     *                      @OA\Property(property="created_at", type="string", format="date-time"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time"),
     *                      @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
     *                      @OA\Property(property="active", type="boolean", example=true)
     *                  )
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No decision models found")
     *          )
     *      )
     * )
     */
    public function getDecisionModels(GetDecisionModelsRequest $request, int $custodianId): JsonResponse
    {
        if (! Gate::allows('viewByCustodian', [CustodianModelConfig::class, $custodianId])) {
            return $this->ForbiddenResponse();
        }

        $decisionModelType = $request->input('decision_model_type');

        $decisionModelTypeId = DecisionModelType::where('name', $decisionModelType)->value('id');

        if (!$decisionModelTypeId) {
            return $this->NotFoundResponse();
        }

        $decisionModels = DecisionModel::with(['custodianModelConfig' => function ($query) use ($custodianId) {
            $query->where('custodian_id', $custodianId);
        }])
            ->whereHas('custodianModelConfig', function ($query) use ($custodianId) {
                $query->where('custodian_id', $custodianId);
            })
            ->where('decision_model_type_id', $decisionModelTypeId)
            ->get();

        if ($decisionModels->isEmpty()) {
            return $this->NotFoundResponse();
        }

        $decisionModels = $decisionModels->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'description' => $model->description,
                'active' => optional($model->custodianModelConfig)->active,
            ];
        });

        return $this->OKResponse($decisionModels);
    }
    /**
     * @OA\Put(
     *      path="/api/v1/custodian_config/{custodianId}/decision_models",
     *      operationId="custodianModelConfigUpdateDecisionModels",
     *      x={"internal"="true"},
     *      summary="Update a custodian's decision models",
     *      description="Update the active status of specified custodian model configs for a given custodian",
     *      tags={"CustodianModelConfig"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="custodianId",
     *          in="path",
     *          required=true,
     *          description="ID of the custodian",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="configs",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="decision_model_id", type="integer", example=1),
     *                      @OA\Property(property="active", type="boolean", example=true)
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Custodian model configs updated successfully"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="decision_model_id", type="integer", example=1),
     *                      @OA\Property(property="active", type="boolean", example=true)
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid argument(s)",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid argument(s)"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Custodian or one or more decision models not found")
     *          )
     *      )
     * )
     */
    public function updateDecisionModels(UpdateDecisionModelsRequest $request, int $id): JsonResponse
    {
        try {
            if (! Gate::allows('updateByCustodian', [CustodianModelConfig::class, $id])) {
                return $this->ForbiddenResponse();
            }

            $loggedInUserId = $request->user()?->id;
            $request->validate([
                'configs' => 'required|array',
                'configs.*.decision_model_id' => 'required|integer|exists:decision_models,id',
                'configs.*.active' => 'required|boolean',
            ]);

            $configs = $request->input('configs');
            $updatedConfigs = [];
            $hasChanges = false;

            foreach ($configs as $config) {
                $custodianModelConfig = CustodianModelConfig::where('custodian_id', $id)
                    ->where('decision_model_id', $config['decision_model_id'])
                    ->first();

                if ($custodianModelConfig) {
                    if ((bool)$custodianModelConfig->active !== (bool)$config['active']) {
                        $hasChanges = true;
                    }
                    $custodianModelConfig->active = $config['active'];
                    $custodianModelConfig->save();

                    $updatedConfigs[] = [
                        'decision_model_id' => $custodianModelConfig->decision_model_id,
                        'active' => $custodianModelConfig->active,
                    ];
                }
            }

            if ($hasChanges) {
                $this->notifyOnConfirationWasUpdated($loggedInUserId);
            }

            if (count($updatedConfigs) !== count($configs)) {
                return $this->NotFoundResponse();
            }
            return $this->OKResponse($updatedConfigs);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
