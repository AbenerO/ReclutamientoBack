<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\EstadoVacante;

class UpdateEstadoVacanteApiRequest extends FormRequest
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
                Rule::unique('estados_vacante', 'nombre')->ignore($this->route('estadoVacante')?->id ?? null, 'id'),
            ],
        ];

    }
}

