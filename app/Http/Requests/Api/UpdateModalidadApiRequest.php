<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Modalidad;

class UpdateModalidadApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                Rule::unique('modalidades', 'nombre')->ignore($this->route('modalidad')?->id ?? null, 'id'),
            ],
        ];

    }
}

