<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\FuenteReclutamiento;



class CreateFuenteReclutamientoApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

           return FuenteReclutamiento::$rules;

    }
}

