<?php

namespace App\Http\Requests\GroupChart;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GroupChartUpdate extends FormRequest
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
            'group_chart_name' => 'required|string|unique:group_chart,group_chart_name,'.$id.',group_chart_id',
            'alias' => 'nullable|unique:group_chart,alias,'.$id.',group_chart_id',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespondWithError('validation group chart error ', $validator->errors(), 422)
        );

    }
}
