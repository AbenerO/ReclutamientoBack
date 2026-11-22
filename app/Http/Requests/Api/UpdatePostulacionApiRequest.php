<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Postulacion;

class UpdatePostulacionApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'candidato_id' => [
                'required',
                'integer',
                Rule::unique('postulaciones', 'candidato_id')->ignore($this->route('postulacion')?->id ?? null, 'id'),
            ],
            'vacante_id' => [
                'required',
                'integer',
                Rule::unique('postulaciones', 'vacante_id')->ignore($this->route('postulacion')?->id ?? null, 'id'),
            ],
            'fecha_postulacion' => [
                'required',
                'date',
            ],
            'estado_postulacion_id' => [
                'required',
                'integer',
            ],
            'fuente_reclutamiento_id' => [
                'required',
                'integer',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
        ];

    }
}

