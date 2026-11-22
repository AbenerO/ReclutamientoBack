<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\TipoEntrevista;

class UpdateTipoEntrevistaApiRequest extends FormRequest
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
                Rule::unique('tipos_entrevista', 'nombre')->ignore($this->route('tipoEntrevista'), 'id'),
            ],
        ];

    }
}

