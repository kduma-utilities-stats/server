<?php

namespace App\Http\Requests;

use App\Models\Counter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\PrefixedIds\Exceptions\NoPrefixedModelFound;

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
            'counter_id' => ['required', 'string', Rule::exists('counters', 'prefixed_id')], // TODO: Authorize
            'value' => ['required', 'numeric'],
            'notes' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @throws NoPrefixedModelFound
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        $validated['counter_id'] = Counter::findByPrefixedIdOrFail($validated['counter_id'])->id;

        return $validated;
    }


}
