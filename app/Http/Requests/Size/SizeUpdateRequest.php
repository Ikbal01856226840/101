<?php

namespace App\Http\Requests\Size;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SizeUpdateRequest extends FormRequest
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
        $id = $this->request->get('id');

        return [
            'symbol' => 'required|string|unique:unitof_size,symbol,'.$id.',unit_of_size_id',
        ];

    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation size error ', $validator->errors(), 422)
        );

    }
}
