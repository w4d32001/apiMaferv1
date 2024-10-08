<?php

namespace App\Http\Requests\Customer;

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
            'name' => ['required', 'string', 'max:255'], 
            'surname' => ['required', 'string', 'max:255'],
            'dni' => ['required', 'string', 'min:8', 'max:12', 'unique:customers,dni'], 
            'ruc' => ['nullable', 'string', 'size:11'],
            'image' => ['nullable'],
            'reason' => ['nullable', 'string', 'max:255'], 
            'password'=> ['nullable'],
            'address' => ['nullable', 'string', 'max:255'], 
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'], 
            'phone' => ['required', 'string'],
        ];
    }
}
