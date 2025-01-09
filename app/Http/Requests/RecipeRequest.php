<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
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
            'name' => ['required', 'max:2000'],
            'images.*' => ['nullable', 'image'],
            'deleted_images.*' => ['nullable', 'int'],
            'quantity' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'manufacturer_id' => ['nullable', 'numeric'],
            'allergens' => ['nullable', 'string'],
            'composition' => ['nullable', 'string'],
            'storing' => ['nullable', 'string'],
            'nutritional' => ['nullable', 'string'],
            'category_id' => ['required', 'numeric'],
            'published' => ['required', 'boolean']
        ];
    }
}
