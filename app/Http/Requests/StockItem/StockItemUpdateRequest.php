<?php

namespace App\Http\Requests\StockItem;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StockItemUpdateRequest extends FormRequest
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
            'product_name' => 'required|string|unique:stock_item,product_name,'.$id.',stock_item_id',
            'alias' => 'nullable|unique:stock_item,alias,'.$id.',stock_item_id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation stock Item error ', $validator->errors(), 422)
        );

    }
}
