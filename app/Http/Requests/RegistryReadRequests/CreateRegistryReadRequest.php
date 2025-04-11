<?php

namespace App\Http\Requests\RegistryReadRequests;

use App\Http\Requests\BaseFormRequest;

class CreateRegistryReadRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'digital_identifier' => [
                'string',
                'required'
            ],
        ];
    }
}
