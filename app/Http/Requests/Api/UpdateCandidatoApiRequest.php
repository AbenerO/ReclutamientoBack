<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Candidato;

class UpdateCandidatoApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'primer_nombre' => [
                'required',
                'string',
            ],
            'segundo_nombre' => [
                'nullable',
                'string',
            ],
            'tercer_nombre' => [
                'nullable',
                'string',
            ],
            'primer_apellido' => [
                'required',
                'string',
            ],
            'segundo_apellido' => [
                'nullable',
                'string',
            ],
            'apellido_casada' => [
                'nullable',
                'string',
            ],
            'email' => [
                'required',
                'string',
                Rule::unique('candidatos', 'email')->ignore($this->route('candidato'), 'id'),
            ],
            'telefono' => [
                'required',
                'string',
            ],
            'fecha_nacimiento' => [
                'required',
                'date',
            ],
            'expectativa_salarial' => [
                'required',
                'numeric',
            ],
        ];

    }
}

