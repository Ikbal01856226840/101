<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SupplierUpdateRequest extends FormRequest
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
        $supplier_id = $this->request->get('supplier_id');

        return [
            'supplier_name' => 'required|string|unique:supplier,supplier_name,'.$supplier_id.',id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation supplier error ', $validator->errors(), 422)
        );

    }
}
