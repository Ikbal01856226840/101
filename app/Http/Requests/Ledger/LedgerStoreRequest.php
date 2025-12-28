<?php

namespace App\Http\Requests\Ledger;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LedgerStoreRequest extends FormRequest
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
            'ledger_name' => 'required|string|unique:ledger_head',
            'alias' => 'nullable|unique:ledger_head',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation ledger error ', $validator->errors(), 422)
        );

    }
}
