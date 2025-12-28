<?php

namespace App\Http\Requests\Components;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ComponentsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comp_name' => 'required|string|unique:mp_components',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation component error ', $validator->errors(), 422)
        );

    }
}
