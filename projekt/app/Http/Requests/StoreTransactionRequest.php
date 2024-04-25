<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'category_id' => 'required|numeric|exists:categories,id',
            'created_at' => 'required|date',
            'comment' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Der Name ist ein Pflichtfeld.',
            'type.required' => 'Der Typ ist ein Pflichtfeld.',
            'type.in' => 'Der Typ muss "income" oder "expense" sein.',
            'amount.required' => 'Der Betrag ist ein Pflichtfeld.',
            'amount.numeric' => 'Der Betrag muss numerisch sein.',
            'category_id.required' => 'Die Kategorie-ID ist ein Pflichtfeld.',
            'category_id.exists' => 'Die angegebene Kategorie existiert nicht.',
            'created_at.required' => 'Das Erstellungsdatum ist ein Pflichtfeld.',
            'created_at.date_format' => 'Das Erstellungsdatum muss das Format JJJJ-MM-TT haben.',
            'comment.string' => 'Der Kommentar muss eine Zeichenfolge sein.',
        ];
    }
}
