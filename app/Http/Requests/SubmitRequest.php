<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PageType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'journey_token' => ['nullable', 'string', 'size:26'],
            'product_key' => [
                'required',
                'string',
                Rule::exists('pages', 'page_key')
                    ->where('page_type', PageType::Product->value)
                    ->where('is_active', true),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
