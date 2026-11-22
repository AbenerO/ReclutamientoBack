<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PostulacionHistorial;



class CreatePostulacionHistorialApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

           return PostulacionHistorial::$rules;

    }
}

