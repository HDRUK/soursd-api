<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  @OA\Schema(
 *     schema="Identity",
 *     type="object",
 *     title="Identity",
 *     description="Model representing identity records",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="Unique identifier for the identity record"
 *     ),
 *     @OA\Property(
 *         property="registry_id",
 *         type="integer",
 *         example=42,
 *         description="ID of the registry associated with the identity record"
 *     ),
 *     @OA\Property(
 *         property="address_1",
 *         type="string",
 *         example="123 Example Street",
 *         description="First line of the address"
 *     ),
 *     @OA\Property(
 *         property="address_2",
 *         type="string",
 *         example="Suite 456",
 *         description="Second line of the address"
 *     ),
 *     @OA\Property(
 *         property="town",
 *         type="string",
 *         example="Example Town",
 *         description="Town of the address"
 *     ),
 *     @OA\Property(
 *         property="county",
 *         type="string",
 *         example="Example County",
 *         description="County of the address"
 *     ),
 *     @OA\Property(
 *         property="country",
 *         type="string",
 *         example="UK",
 *         description="Country of the address"
 *     ),
 *     @OA\Property(
 *         property="postcode",
 *         type="string",
 *         example="EX12 3AB",
 *         description="Postcode of the address"
 *     ),
 *     @OA\Property(
 *         property="dob",
 *         type="string",
 *         format="date",
 *         example="1990-01-01",
 *         description="Date of birth"
 *     ),
 *     @OA\Property(
 *         property="idvt_success",
 *         type="integer",
 *         example=1,
 *         description="Indicates whether IDVT was successful (1 for success, 0 for failure)"
 *     ),
 *     @OA\Property(
 *         property="idvt_identification_number",
 *         type="string",
 *         example="ID12345",
 *         description="Identification number from IDVT"
 *     ),
 *     @OA\Property(
 *         property="idvt_document_type",
 *         type="string",
 *         example="passport",
 *         description="Type of document used for IDVT"
 *     ),
 *     @OA\Property(
 *         property="idvt_document_number",
 *         type="string",
 *         example="123456789",
 *         description="Document number used for IDVT"
 *     ),
 *     @OA\Property(
 *         property="idvt_document_country",
 *         type="string",
 *         example="UK",
 *         description="Country of the document used for IDVT"
 *     ),
 *     @OA\Property(
 *         property="idvt_document_valid_until",
 *         type="string",
 *         format="date",
 *         example="2030-01-01",
 *         description="Validity date of the document used for IDVT"
 *     ),
 *     @OA\Property(
 *         property="idvt_document_first_name",
 *         type="string",
 *         example="Joe",
 *         description="First name on the document used for IDVT"
 *     ),
 *     @OA\Property(
 *         property="idvt_document_valid_last_name",
 *         type="string",
 *         example="Bloggs",
 *         description="Last name on the document used for IDVT"
 *     ),
 *     @OA\Property(
 *         property="idvt_attempt_id",
 *         type="string",
 *         example="ATT12345",
 *         description="ID of the IDVT attempt"
 *     ),
 *     @OA\Property(
 *         property="idvt_context_id",
 *         type="string",
 *         example="CTX12345",
 *         description="Context ID for IDVT"
 *     ),
 *     @OA\Property(
 *         property="idvt_document_dob",
 *         type="string",
 *         format="date",
 *         example="1990-01-01",
 *         description="Date of birth on the document used for IDVT"
 *     ),
 *     @OA\Property(
 *         property="idvt_context",
 *         type="string",
 *         example="Verification Context",
 *         description="Context of the IDVT process"
 *     ),
 *     @OA\Property(
 *         property="idvt_completed_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-25T12:00:00Z",
 *         description="Timestamp when IDVT was completed"
 *     ),
 *     @OA\Property(
 *         property="idvt_result_text",
 *         type="string",
 *         example="Verification successful",
 *         description="Result text of the IDVT process"
 *     ),
 *     @OA\Property(
 *         property="idvt_started_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-25T10:00:00Z",
 *         description="Timestamp when IDVT was started"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-25T12:00:00Z",
 *         description="Timestamp when the identity record was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-25T12:00:00Z",
 *         description="Timestamp when the identity record was last updated"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-25T12:00:00Z",
 *         description="Timestamp when the identity record was deleted"
 *     )
 * )
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $registry_id
 * @property string|null $address_1
 * @property string|null $address_2
 * @property string|null $town
 * @property string|null $county
 * @property string|null $country
 * @property string|null $postcode
 * @property string|null $dob
 * @property string|null $idvt_completed_at
 * @property string|null $idvt_result_text
 * @property string|null $idvt_context
 * @property int $idvt_success
 * @property string|null $idvt_identification_number
 * @property string|null $idvt_document_type
 * @property string|null $idvt_document_number
 * @property string|null $idvt_document_country
 * @property string|null $idvt_document_valid_until
 * @property string|null $idvt_document_first_name
 * @property string|null $idvt_document_last_name
 * @property string|null $idvt_attempt_id
 * @property string|null $idvt_context_id
 * @property string|null $idvt_document_dob
 * @property string|null $idvt_started_at
 * @method static \Database\Factories\IdentityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtDocumentCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtDocumentDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtDocumentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtDocumentValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtDocumentFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtDocumentLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtIdentificationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtResultText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereIdvtSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereRegistryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereTown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Identity withoutTrashed()
 * @mixin \Eloquent
 */
class Identity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'identities';

    public $timestamps = true;

    protected $fillable = [
        'registry_id',
        'address_1',
        'address_2',
        'town',
        'county',
        'country',
        'postcode',
        'dob',
        'idvt_success',
        'idvt_identification_number',
        'idvt_document_type',
        'idvt_document_number',
        'idvt_document_country',
        'idvt_document_valid_until',
        'idvt_document_first_name',
        'idvt_document_last_name',
        'idvt_attempt_id',
        'idvt_context_id',
        'idvt_document_dob',
        'idvt_context',
        'idvt_completed_at',
        'idvt_result_text',
        'idvt_started_at',
    ];

    // protected $hidden = [
    //     'selfie_path',
    //     'passport_path',
    //     'drivers_license_path',
    //     'address_1',
    //     'address_2',
    //     'town',
    //     'county',
    //     'country',
    //     'postcode',
    //     'dob',
    // ];
}
