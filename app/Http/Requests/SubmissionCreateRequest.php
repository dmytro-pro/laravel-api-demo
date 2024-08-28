<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionCreateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:250',
            'email' => 'required|string|min:1|max:250|email',
            'message' => 'required|string|min:1|max:1000',
        ];
    }
}
