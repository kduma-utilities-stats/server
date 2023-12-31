<?php

namespace App\Http\Requests;

use App\Models\Meter;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:50'],
        ];
    }
}
