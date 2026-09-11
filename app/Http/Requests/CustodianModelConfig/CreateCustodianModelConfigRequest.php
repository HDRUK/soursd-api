<?php

namespace App\Http\Requests\CustodianModelConfig;

use App\Http\Requests\BaseFormRequest;

class CreateCustodianModelConfigRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'decision_model_id' => [
                'integer',
                'required',
                'exists:decision_models,id',
            ],
            'active' => [
                'boolean',
                'required',
            ],
            'custodian_id' => [
                'integer',
                'required',
                'exists:custodians,id',
            ],
        ];
    }
}
