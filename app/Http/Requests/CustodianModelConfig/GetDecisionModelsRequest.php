<?php

namespace App\Http\Requests\CustodianModelConfig;

use App\Http\Requests\BaseFormRequest;
use App\Models\DecisionModelType;
use Illuminate\Validation\Rule;

class GetDecisionModelsRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'custodianId' => [
                'required',
                'integer',
                'exists:custodians,id',
            ],
            'decision_model_type' => [
                'required',
                'string',
                Rule::in(DecisionModelType::ENTITY_TYPES),
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
        $this->merge(['custodianId' => $this->route('custodianId')]);
    }
}
