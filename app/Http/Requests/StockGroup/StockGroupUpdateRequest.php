<?php

namespace App\Http\Requests\StockGroup;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StockGroupUpdateRequest extends FormRequest
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
            'stock_group_name' => 'required|string|unique:stock_group,stock_group_name,'.$id.',stock_group_id',
            'alias' => 'nullable|unique:stock_group,alias,'.$id.',stock_group_id',
        ];

    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation stock group error ', $validator->errors(), 422)
        );

    }
}
