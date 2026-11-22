<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\FuenteReclutamiento;

class UpdateFuenteReclutamientoApiRequest extends FormRequest
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
                Rule::unique('fuentes_reclutamiento', 'nombre')->ignore($this->route('fuenteReclutamiento'), 'id'),
            ],
        ];

    }
}

