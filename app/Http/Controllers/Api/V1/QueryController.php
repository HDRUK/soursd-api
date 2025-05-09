<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Affiliation;
use App\Models\Endorsement;
use App\Models\History;
use App\Models\Identity;
use App\Models\Infringement;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Training;
use App\Models\User;
use App\Models\RegistryHasTraining;
use App\Models\RegistryReadRequest;
use App\Models\Custodian;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/query",
     *      summary="Query the registry by Digital Identifier",
     *      description="Query the registry by Digital Identifier",
     *      tags={"Query"},
     *      summary="Query@query",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Query definition",
     *          @OA\JsonContent(
     *              @OA\Property(property="ident", type="string", example="$2y$12$V6SSFQLyQDQRZxvz.Tswa.HA.ixJIXofs7.omitted")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *                  @OA\Property(property="message", type="string", example="success"),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example="1"),
     *                          @OA\Property(property="created_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                          @OA\Property(property="updated_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                          @OA\Property(property="deleted_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                          @OA\Property(property="verified", type="boolean", example="true"),
     *                          @OA\Property(property="user", type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="user_id", type="integer", example="1"),
     *                                  @OA\Property(property="name", type="string", example="Some One"),
     *                                  @OA\Property(property="email", type="string", example="someone@somewhere.com"),
     *                                  @OA\Property(property="email_verified_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="registry_id", type="integer", example="1"),
     *                                  @OA\Property(property="created_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="updated_at", type="string", example="2024-03-12T13:11:55.000000Z")
     *                              )
     *                          ),
     *                          @OA\Property(property="identity", type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example="1"),
     *                                  @OA\Property(property="created_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="updated_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="deleted_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="registry_id", type="integer", example="1")
     *                              )
     *                          ),
     *                          @OA\Property(property="history", type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example="1"),
     *                                  @OA\Property(property="created_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="updated_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="project_id", type="integer", example="1"),
     *                                  @OA\Property(property="access_key_id", type="integer", example="876"),
     *                                  @OA\Property(property="custodian_identifier", type="string", example="ABC1234DEF-56789-0")
     *                              )
     *                          ),
     *                          @OA\Property(property="training", type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example="1"),
     *                                  @OA\Property(property="created_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="updated_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="registry_id", type="integer", example="1"),
     *                                  @OA\Property(property="provider", type="string", example="Training Provider Name"),
     *                                  @OA\Property(property="awarded_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="expires_at", type="string", example="2029-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="expires_in_years", type="integer", example="5"),
     *                                  @OA\Property(property="training_name", type="string", example="Training Course Name")
     *                              )
     *                          ),
     *                          @OA\Property(property="projects", type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example="1"),
     *                                  @OA\Property(property="created_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="updated_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="deleted_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="name", type="string", example="Project Name"),
     *                                  @OA\Property(property="public_benefit", type="string", example="Public Benefit statement"),
     *                                  @OA\Property(property="runs_to", type="string", example="2024-09-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="affiliate_id", type="integer", example="124")
     *                              )
     *                          ),
     *                          @OA\Property(property="organisations", type="array",
     *                              @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example="1"),
     *                                  @OA\Property(property="created_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="updated_at", type="string", example="2024-03-12T13:11:55.000000Z"),
     *                                  @OA\Property(property="name", type="string", example="Institute Name")
     *                              )
     *                          ),
     *                          @OA\Property(property="affiliations", type="array",
     *                              @OA\Items(ref="#/components/schemas/Affiliation")
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      )
     *  )
     */
    public function query(Request $request): JsonResponse
    {
        $input = $request->all();

        $custodianKey = $request->header('x-client-id', null);
        if (!$custodianKey) {
            return response()->json([
                'message' => 'you must provide your Custodian key',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $custodian = Custodian::where('client_id', $custodianKey)->first();
        if (! $custodian) {
            return response()->json([
                'message' => 'no known custodian matches the credentials provided',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $rrr = RegistryReadRequest::where('custodian_id', $custodian->id)
            ->where('registry_id', Registry::where('digi_ident', $input['ident'])->first()->id)
            ->where('status', RegistryReadRequest::READ_REQUEST_STATUS_APPROVED)
            ->first();
        if (!$rrr) {
            return response()->json([
                'message' => 'no user approved read request found',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // We could do the following with eloquent, but as it's quite a large hit,
        // it's far more performant to just pull the records manually and form
        // the resulting payload, to avoid Laravel bloat.
        $payload = [
            'user' => [
                'identity' => [],
            ],
            'registry' => [
                'training' => [],
                'history' => [],
            ],
        ];

        $registry = Registry::where('digi_ident', $input['ident'])->first();
        $payload['registry'] = $registry;

        $user = User::where('registry_id', $registry->id)->first();
        $payload['user'] = $user;

        $linkedTraining = RegistryHasTraining::where('registry_id')->select('training_id')->get()->toArray();
        $training = Training::whereIn('id', $linkedTraining);
        $payload['registry']['training'] = $training;

        $identity = Identity::where('registry_id', $registry->id)->first();
        $payload['user']['identity'] = $identity;

        $rhh = DB::table('registry_has_histories')->where('registry_id', '=', $registry->id)->get();
        foreach ($rhh as $item) {
            $history = History::where('id', $item->history_id)->first()->toArray();

            $affiliation = Affiliation::where('id', $history['affiliation_id'])->first();
            $history['affiliation'] = $affiliation;

            // LS 18/02/25 - Removed for now as in talks by IG team - and not MVP
            // $endorsement = Endorsement::where('id', $history['endorsement_id'])->first();
            // $history['endorsement'] = $endorsement;

            // $infringement = Infringement::where('id', $history['infringement_id'])->first();
            // $history['infringement'] = $infringement;

            $project = Project::where('id', $history['project_id'])->first();
            $history['project'] = $project;

            $payload['registry']['history'][] = $history;
        }

        if ($registry) {
            return response()->json([
                'message' => 'success',
                'data' => $payload,
            ], 200);
        }

        throw new NotFoundException();
    }
}
