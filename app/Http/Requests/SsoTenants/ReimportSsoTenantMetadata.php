<?php

namespace App\Http\Requests\SsoTenants;

use App\Http\Requests\BaseFormRequest;

class ReimportSsoTenantMetadata extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:sso_tenants,id'],
            'metadata_url' => ['required_without:metadata_xml', 'nullable', 'url'],
            'metadata_xml' => ['required_without:metadata_url', 'nullable', 'string'],
        ];
    }

    /**
     * Add Route parameters to the FormRequest.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge(['id' => $this->route('id')]);
    }
}
