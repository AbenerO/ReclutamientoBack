<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ResultadoEntrevista;

class UpdateResultadoEntrevistaApiRequest extends FormRequest
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
                Rule::unique('resultados_entrevista', 'nombre')->ignore($this->route('resultadoEntrevista'), 'id'),
            ],
        ];

    }
}

