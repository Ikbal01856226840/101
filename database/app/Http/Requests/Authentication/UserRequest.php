<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
            'log_in_id' => 'required|string|max:255|unique:admin_user_info',
            'password' => 'required|string|min:8',
        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            RespondWithError('validation registration error ', $validator->errors(), 422)
        );

    }
}
