<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Identity;
use App\Traits\CommonFunctions;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdentityController extends Controller
{
    use CommonFunctions;

    /**
     * @OA\Get(
     *      path="/api/v1/identities",
     *      summary="Return a list of Identity entries",
     *      description="Return a list of Identity entries",
     *      tags={"Identity"},
     *      summary="Identity@index",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *                  @OA\Property(property="created_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="updated_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="registry_id", type="integer", example="1")
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $identities = Identity::paginate((int)$this->getSystemConfig('PER_PAGE'));

        return response()->json([
            'message' => 'success',
            'data' => $identities,
        ], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/identities/{id}",
     *      summary="Return an Identity entry by ID",
     *      description="Return an Identity entry by ID",
     *      tags={"Identity"},
     *      summary="Identity@show",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identity ID",
     *         required=true,
     *         example="1",
     *
     *         @OA\Schema(
     *            type="integer",
     *            description="Identity ID",
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *                  @OA\Property(property="created_at", type="string", example="2024-02-04 12:00:00"),
     *                  @OA\Property(property="updated_at", type="string", example="2024-02-04 12:01:00"),
     *                  @OA\Property(property="registry_id", type="integer", example="1")
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found"),
     *          )
     *      )
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $identity = Identity::findOrFail($id);
        if ($identity) {
            return response()->json([
                'message' => 'succcess',
                'data' => $identity,
            ], 200);
        }

        throw new NotFoundException();
    }

    /**
     * @OA\Post(
     *      path="/api/v1/identities",
     *      summary="Create an Identity entry",
     *      description="Create a Identity entry",
     *      tags={"Identity"},
     *      summary="Identity@store",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Identity definition",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="registry_id", type="integer", example="1"),
     *              @OA\Property(property="selfie_path", type="string", example="storage/path/to/selfie.jpeg"),
     *              @OA\Property(property="passport_path", type="string", example="storage/path/to/passport.jpeg"),
     *              @OA\Property(property="drivers_license_path", type="string", example="storage/path/to/drivers_license.jpeg"),
     *              @OA\Property(property="address_1", type="string", example="123 Road"),
     *              @OA\Property(property="address_2", type="string", example="Other part of Address"),
     *              @OA\Property(property="town", type="string", example="Town"),
     *              @OA\Property(property="county", type="string", example="County"),
     *              @OA\Property(property="country", type="string", example="Country"),
     *              @OA\Property(property="postcode", type="string", example="AB12 3CD"),
     *              @OA\Property(property="dob", type="string", example="1977-07-25")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
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
    public function store(Request $request): JsonResponse
    {
        try {
            $input = $request->only(app(Identity::class)->getFillable());
            $identity = Identity::create($input);
            if (!$identity) {
                return response()->json([
                    'message' => 'error',
                ], 400);
            }

            return response()->json([
                'message' => 'success',
                'data' => $identity->id,
            ], 201);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/identities/{id}",
     *      summary="Update an Identity entry",
     *      description="Update a Identity entry",
     *      tags={"Identity"},
     *      summary="Identity@update",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Identity definition",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="registry_id", type="integer", example="1"),
     *              @OA\Property(property="selfie_path", type="string", example="storage/path/to/selfie.jpeg"),
     *              @OA\Property(property="passport_path", type="string", example="storage/path/to/passport.jpeg"),
     *              @OA\Property(property="drivers_license_path", type="string", example="storage/path/to/drivers_license.jpeg"),
     *              @OA\Property(property="address_1", type="string", example="123 Road"),
     *              @OA\Property(property="address_2", type="string", example="Other part of Address"),
     *              @OA\Property(property="town", type="string", example="Town"),
     *              @OA\Property(property="county", type="string", example="County"),
     *              @OA\Property(property="country", type="string", example="Country"),
     *              @OA\Property(property="postcode", type="string", example="AB12 3CD"),
     *              @OA\Property(property="dob", type="string", example="1977-07-25")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found")
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="123"),
     *                  @OA\Property(property="registry_id", type="integer", example="1")
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
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $input = $request->only(app(Identity::class)->getFillable());

            $identity = Identity::findOrFail($id);
            $identity->update($input);

            return response()->json([
                'message' => 'success',
                'data' => Identity::where('id', $id)->first(),
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/identities/{id}",
     *      summary="Delete an Identity entry from the system by ID",
     *      description="Delete an Identity entry from the system",
     *      tags={"Identity"},
     *      summary="Identity@destroy",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identity entry ID",
     *         required=true,
     *         example="1",
     *
     *         @OA\Schema(
     *            type="integer",
     *            description="Identity entry ID",
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Not found response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="not found")
     *           ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="success")
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
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            Identity::where('id', $id)->delete();

            return response()->json([
                'message' => 'success',
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
