<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Postulacion;



class CreatePostulacionApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = Postulacion::$rules;

        $rules['candidato_id'] = [
            'required',
            'integer',
            Rule::unique('postulaciones', 'candidato_id')->where('vacante_id', $this->vacante_id),
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'candidato_id.unique' => 'El candidato ya está postulado a esta vacante.',
        ];
    }
}

