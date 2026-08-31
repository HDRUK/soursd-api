<?php

namespace App\Http\Requests\SsoTenants;

use App\Http\Requests\BaseFormRequest;

class RejectSsoTenant extends BaseFormRequest
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
            'reason' => ['sometimes', 'nullable', 'string', 'max:2000'],
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
