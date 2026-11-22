<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\EstadoPostulacion;

class UpdateEstadoPostulacionApiRequest extends FormRequest
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
                Rule::unique('estados_postulacion', 'nombre')->ignore($this->route('estadoPostulacion'), 'id'),
            ],
            'orden' => [
                'required',
                'integer',
            ],
        ];

    }
}

