<?php

namespace App\Http\Requests\BillOfMaterial;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BillOfMaterialUpdateRequest extends FormRequest
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
        $bom_id = $this->request->get('bom_id');

        return [
            'bom_name' => 'required|string|unique:bom,bom_name,'.$bom_id.',bom_id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation bill of material error', $validator->errors(), 422)
        );

    }
}
