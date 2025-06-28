<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
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
            'package_name' => 'required|string|max:50|unique:packages,package_name,' . $this->package->id,
            'short_description' => 'required',
            'price' => 'required|numeric',
            'max_product' => 'required|integer',
            'status' => 'required|in:Active,Inactive',
            'services' => 'required|array|min:1',
        ];
    }
}
