<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'product_name' => 'required|string|max:50|unique:products,product_name,' . $this->product->id,
            'product_price' => 'required|numeric',
            'unit_id' => 'required|integer|exists:units,id', 
            'stock_qty' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'description' => 'required',
            'status' => 'required|in:Active,Inactive',
        ];
    }
}
