<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreValueRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'counter_id' => ['required', 'int', Rule::exists('counters', 'id')], // TODO: Authorize
            'value' => ['required', 'numeric'],
            'notes' => ['nullable', 'string', 'max:50'],
        ];
    }
}
