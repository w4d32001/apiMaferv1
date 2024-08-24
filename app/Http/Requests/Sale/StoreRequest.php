<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'total_sale' => [
                'required',
                'numeric',
                'min:0'
            ],
            'total_quantity' => [
                'required',
                'integer',
                'min:1'
            ],
            'customer_id' => [
                'required',
                'exists:customers,id'
            ],
            'inventory_id' => [
                'required',
                'exists:inventories,id'
            ],
            'payment_method_id' => [
                'required',
                'exists:payment_methods,id'
            ]
        ];
    }
}
