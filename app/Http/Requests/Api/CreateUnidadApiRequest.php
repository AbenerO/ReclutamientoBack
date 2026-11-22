<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Unidad;



class CreateUnidadApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = Unidad::$rules;

        $rules['nombre'] = [
            'required',
            'string',
            'max:100',
            Rule::unique('unidades', 'nombre'),
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'nombre.unique' => 'Ya existe una unidad con ese nombre.',
        ];
    }
}
