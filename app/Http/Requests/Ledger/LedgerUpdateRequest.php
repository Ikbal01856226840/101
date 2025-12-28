<?php

namespace App\Http\Requests\Ledger;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LedgerUpdateRequest extends FormRequest
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
            'ledger_name' => 'required|string|unique:ledger_head,ledger_name,'.$id.',ledger_head_id',
            'alias' => 'nullable|unique:ledger_head,alias,'.$id.',ledger_head_id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation ledger error ', $validator->errors(), 422)
        );

    }
}
