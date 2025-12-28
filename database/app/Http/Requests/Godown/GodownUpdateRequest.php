<?php

namespace App\Http\Requests\Godown;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GodownUpdateRequest extends FormRequest
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
            'godown_name' => 'required|string|unique:godowns,godown_name,'.$id.',godown_id',
            'alias' => 'nullable|unique:godowns,alias,'.$id.',godown_id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation godown error ', $validator->errors(), 422)
        );

    }
}
