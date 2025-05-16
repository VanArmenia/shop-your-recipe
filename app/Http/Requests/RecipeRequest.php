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
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'int'],
            'prep_time' => ['required', 'string'],
            'images.*' => ['nullable', 'image'],
            'deleted_images.*' => ['nullable', 'int'],

            // Accept ingredients
            'ingredients' => 'array',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.measurement' => 'nullable|string|max:255',
        ];
    }
}
