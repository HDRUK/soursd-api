<?php

namespace App\Http\Requests\SsoTenants;

use App\Http\Requests\BaseFormRequest;

class StoreSsoTenant extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'metadata_url' => ['required_without:metadata_xml', 'nullable', 'url'],
            'metadata_xml' => ['required_without:metadata_url', 'nullable', 'string'],
            'domains' => ['required', 'array', 'min:1'],
            'domains.*' => [
                'required',
                'string',
                'distinct',
                'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/i',
                'unique:sso_tenant_domains,domain',
            ],
        ];
    }
}
