<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use TriggerEmail;
use App\Models\File;
use App\Models\User;
use App\Models\State;
use App\Models\Registry;
use App\Models\Affiliation;
use App\Jobs\ScanFileUpload;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Http\Traits\Responses;
use App\Models\RegistryHasFile;
use App\Traits\CommonFunctions;
use Illuminate\Http\JsonResponse;
use App\Models\OrganisationHasFile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\CustodianHasProjectOrganisation;
use App\Http\Requests\FileUploads\GetFileUpload;
use App\Notifications\Organisations\UploadSroDoc;
use App\Http\Requests\FileUploads\GetDownloadFileUpload;
use Illuminate\Support\Facades\Auth;

class FileUploadController extends Controller
{
    use CommonFunctions;
    use Responses;
    /**
     * @OA\Get(
     *      path="/api/v1/files/{id}",
     *      operationId="filesShow",
     *      x={"internal"="true"},
     *      summary="Gets an uploaded file",
     *      description="Gets an uploaded file",
     *      tags={"Files"},
     *      summary="Files@show",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="File ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="File ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="users.csv"),
     *                  @OA\Property(property="path", type="string", example="1739297394_users.csv"),
     *                  @OA\Property(property="type", type="string", example="RESEARCHERS_LIST"),
     *                  @OA\Property(property="status", type="string", example="failed")
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
     *          description="Not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="file upload failed")
     *          ),
     *      ),
     * )
     */
    public function show(GetFileUpload $request, int $id): JsonResponse
    {
        $file = File::findOrFail($id);

        if ($file) {
            return response()->json([
                'message' => 'success',
                'data' => $file,
            ], 200);
        }

        return response()->json([
            'message' => 'not found',
            'data' => null,
        ], 404);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/files/{id}/download",
     *      operationId="filesDownload",
     *      x={"internal"="true"},
     *      summary="Download an uploaded file",
     *      description="Downloads the specified file",
     *      tags={"Files"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="File ID",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *            type="integer",
     *            description="File ID",
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="File downloaded successfully",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/octet-stream",
     *                  @OA\Schema(
     *                      type="string",
     *                      format="binary"
     *                  )
     *              )
     *          }
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
     *          description="File not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="File not found"),
     *          ),
     *      ),
     * )
     */
    public function download(GetDownloadFileUpload $request, int $id)
    {
        try {
            $file = File::find($id);
            if (!$file) {
                return $this->NotFoundResponse();
            }

            if ($file->status !== FILE::FILE_STATUS_PROCESSED) {
                return $this->NotFoundResponse();
            }

            $filePath = $file->path;
            $fileSystem = config('speedi.system.scanning_filesystem_disk');

            $scannedFileSystem = $fileSystem . '_scanned';

            if (!Storage::disk($scannedFileSystem)->exists($filePath)) {
                return $this->NotFoundResponse();
            }

            $headers = [
               'Access-Control-Expose-Headers' => 'Content-Disposition'
            ];
            return Storage::disk($scannedFileSystem)->download($filePath, $file->name, $headers);


        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());

        }
    }


    /**
     * @OA\Post(
     *      path="/api/v1/files",
     *      operationId="filesStore",
     *      x={"internal"="true"},
     *      summary="Upload a file to the registry",
     *      description="Uploads a file to the registry",
     *      tags={"Files"},
     *      summary="Files@store",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="File definition",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="registry_id", type="integer", example="1"),
     *                  @OA\Property(property="file", type="string", format="binary"),
     *                  @OA\Property(property="file_type", type="string", example="CV"),
     *                  @OA\Property(property="entity_type", type="string", example="researcher"),
     *              )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="file upload failed")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="success"),
     *              @OA\Property(property="data", type="integer", example="1"),
     *          ),
     *      )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $input = $request->all();

            $maxFilesize = $this->getSystemConfig('MAX_FILESIZE');
            $supportedTypes = $this->getSystemConfig('SUPPORTED_FILETYPES');

            $request->validate([
                'file' => [
                    'required',
                    'file',
                    'mimes:'.$supportedTypes,
                    'max:'.(int) $maxFilesize * 1000,
                ],
            ]);

            $file = $request->file('file');
            $fileSystem = config('speedi.system.scanning_filesystem_disk');
            $storedFilename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs(
                '',
                $storedFilename,
                $fileSystem . '_unscanned'
            );

            if (!$path) {
                return response()->json([
                    'message' => 'failed',
                    'data' => 'file upload failed',
                ], 400);
            }

            $fileIn = File::create([
                'name' => $file->getClientOriginalName(),
                'type' => $input['file_type'],
                'path' => $path,
                'status' => File::FILE_STATUS_PENDING,
            ]);

            if (strtoupper($input['entity_type'] ?? '') === 'RESEARCHER' && isset($input['registry_id']) && $input['registry_id'] != null) {
                $registryId = intval($input['registry_id']);
                $user = User::where('registry_id', $registryId)->first();

                if (!$user) {
                    throw new Exception('User not found for the given registry ID');
                }

                $registry = Registry::find($user->registry_id);

                if (!$registry) {
                    throw new Exception('Registry not found for the user');
                }

                RegistryHasFile::create([
                    'registry_id' => $registry->id,
                    'file_id' => $fileIn->id,
                ]);
            } elseif (isset($input['organisation_id']) && $input['organisation_id'] != null) {

                $organisationId = intval($input['organisation_id']);
                $organisation = Organisation::find($organisationId);

                if (!$organisation) {
                    throw new Exception('Organisation not found');
                }

                OrganisationHasFile::create([
                    'organisation_id' => $organisation->id,
                    'file_id' => $fileIn->id,
                ]);

                if (strtolower($input['file_type']) === File::FILE_TYPE_DECLARATION_SRO && !$organisation->unclaimed) {
                    $inProgressState = State::STATE_AFFILIATION_ACCOUNT_IN_PROGRESS;
                    $inReview = State::STATE_AFFILIATION_REVIEW;
                    $orgInReview = State::STATE_ORG_IN_REVIEW;

                    CustodianHasProjectOrganisation::whereRelation(
                        'projectOrganisation',
                        'organisation_id',
                        $organisationId
                    )->each(
                        fn ($approval) =>
                        $approval->setState($orgInReview)
                    );

                    Affiliation::with(['registry.user'])
                     ->where('organisation_id', $organisation->id)
                     ->whereHas(
                         'modelState.state',
                         fn ($q) =>
                         $q->where('slug', $inProgressState)
                     )
                     ->whereHas(
                         'registry.user',
                         fn ($q) =>
                         $q->where('unclaimed', false)
                     )->each(fn ($affiliation) => $affiliation->setState($inReview));

                    $userAdmins = User::where('user_group', User::GROUP_ADMINS)->select(['id'])->get();
                    foreach ($userAdmins as $userAdmin) {

                        TriggerEmail::spawnEmail([
                            'type' => 'ORGANISATION_NEEDS_CONFIRMATION',
                            'to' => $organisation->id,
                            'by' => $userAdmin->id,
                            'identifier' => 'organisation_confirmation_needed'
                        ]);

                    }

                    $this->sendNotificationOnUploadSroDoc($organisation->id, $fileIn->id);

                    activity('upload')
                        ->causedBy(Auth::user())
                        ->performedOn($organisation)
                        ->withProperties([
                            'name' => $file->getClientOriginalName(),
                            'type' => $input['file_type'],
                            'path' => $path,
                        ])
                        ->event('upload_queued')
                        ->log('Upload SRO Declaration Form');

                }
            } else {
                throw new Exception('Invalid or missing registry ID or organisation ID');
            }

            ScanFileUpload::dispatch((int) $fileIn->id, $fileSystem);

            return response()->json([
                'message' => 'success',
                'data' => File::where('id', $fileIn->id)->first(),
            ], 200);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function changeAffiliationState($organisationId)
    {
        $affiliations = Affiliation::where("organisation_id", $organisationId)->get();

        foreach ($affiliations as $affiliation) {
            if ($affiliation->getState() === State::STATE_AFFILIATION_ACCOUNT_IN_PROGRESS) {
                $affiliation->setState(State::STATE_AFFILIATION_PENDING);
            }
        }
    }

    public function sendNotificationOnUploadSroDoc($organisationId, $fileId)
    {
        $file = File::where('id', $fileId)->first();
        if ($file->type !== File::FILE_TYPE_DECLARATION_SRO) {
            return;
        }

        $sroUsers = User::where([
            'is_sro' => 1,
            'organisation_id' => $organisationId,
        ])->get();

        foreach ($sroUsers as $sroUser) {
            Notification::send($sroUser, new UploadSroDoc($sroUser, $file));
        }
    }
}
