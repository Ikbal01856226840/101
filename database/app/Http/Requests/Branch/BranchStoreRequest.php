<?php

namespace App\Http\Requests\Branch;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BranchStoreRequest extends FormRequest
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
            'branch_name' => 'required|string|unique:unit_branch_setup',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation branch error ', $validator->errors(), 422)
        );

    }
}
