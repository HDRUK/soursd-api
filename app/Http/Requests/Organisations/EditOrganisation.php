<?php

namespace App\Http\Requests\Organisations;

use Illuminate\Foundation\Http\FormRequest;

class EditOrganisation extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'organisation_name' => 'sometimes|string|max:255',
            'address_1' => 'sometimes|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'town' => 'sometimes|string|max:100',
            'county' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
            'postcode' => 'sometimes|string|max:10',
            'lead_applicant_organisation_name' => 'sometimes|string|max:255',
            'lead_applicant_email' => 'sometimes|email|max:255',
            //'password' => 'sometimes|string|min:8',
            //'organisation_unique_id' => 'sometimes|string|max:255',
            'applicant_names' => 'sometimes|string|max:255',
            'funders_and_sponsors' => 'sometimes|string|max:255',
            'sub_license_arrangements' => 'nullable|string|max:255',
            'verified' => 'sometimes|boolean',
            'companies_house_no' => 'sometimes|string|max:8',
            'sector_id' => 'sometimes|integer',
            'dsptk_ods_code' => 'nullable|string|max:255',
            'dsptk_expiry_date' => 'sometimes|date_format:Y-m-d',
            'dsptk_expiry_evidence' => 'sometimes|integer',
            'iso_27001_certification_num' => 'nullable|string|max:255',
            'iso_expiry_date' => 'sometimes|date_format:Y-m-d',
            'iso_expiry_evidence' => 'sometimes|integer',
            'ce_certification_num' => 'nullable|string|max:255',
            'ce_expiry_date' => 'sometimes|date_format:Y-m-d',
            'ce_expiry_evidence' => 'sometimes|integer',
            'ce_plus_certification_num' => 'nullable|string|max:255',
            'ce_plus_expiry_date' => 'sometimes|date_format:Y-m-d',
            'ce_plus_expiry_evidence' => 'sometimes|integer',
            'ror_id' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'smb_status' => 'sometimes|boolean',
            'organisation_size' => 'sometimes|integer',
            'ods_id' => 'nullable|string',
            'dsptk_status' => 'nullable|string',
            'dsptk_date_last_published' => 'nullable|date_format:Y-m-d',
            'ico_registration_id' => 'nullable|string',
            'ico_date_registered' => 'nullable|date_format:Y-m-d',
            'ico_expiry_date' => 'nullable|date_format:Y-m-d',
            'ico_expiry_evidence' => 'sometimes|integer',

        ];
    }
}
