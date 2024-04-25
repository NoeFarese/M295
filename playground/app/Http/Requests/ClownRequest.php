<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClownRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email',
            'rating' => 'required|integer|min:1|max:5',
            'status' => 'required|string|in:active,inactive,passive,unknown',
            'description' => 'string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Der Name ist ein Pflichtfeld.',
            'name.string' => 'Der Name muss ein String sein.',
            'email.required' => 'Die Email ist ein Pflichtfeld.',
            'email.email' => 'Die Email muss gültig sein.',
        ];
    }

}
