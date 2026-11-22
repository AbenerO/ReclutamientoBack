<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ResultadoEntrevista;



class CreateResultadoEntrevistaApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

           return ResultadoEntrevista::$rules;

    }
}

