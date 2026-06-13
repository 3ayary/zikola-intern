<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => $this->method('post') ? 'required|string|min:2' : 'sometimes|string|min:2',
            'price' => $this->method('post') ? 'required|numeric' : 'sometimes|numeric',
            'price_after_discount' => 'nullable|numeric',
            'description' => 'nullable|string',
            'sku' => $this->method('post') ? 'required|string' : 'sometimes|string',
            'stock' => 'required|integer|min:1',
            'category_id' => $this->method('post') ? 'nullable|integer|exists:categories,id' : 'sometimes|integer|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
}
