<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSliderRequest extends FormRequest
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
            //'domain_id' => 'required|integer|exists:domains,id',
            'title' => 'required|string|unique:sliders,title,' . $this->slider->id,
            'sub_title' => 'required|string|unique:sliders,sub_title,' . $this->slider->id,
            'image' => 'nullable',
            'status' => 'required|in:Active,Inactive',
        ]; 
    }
}
