<?php

namespace App\Http\Requests\SsoTenants;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateSsoTenant extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tenantId = $this->route('id');

        return [
            'id' => ['required', 'integer', 'exists:sso_tenants,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'domains' => ['sometimes', 'required', 'array', 'min:1'],
            'domains.*' => [
                'required',
                'string',
                'distinct',
                'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/i',
                Rule::unique('sso_tenant_domains', 'domain')
                    ->where(fn ($query) => $query->where('sso_tenant_id', '!=', $tenantId)),
            ],
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
