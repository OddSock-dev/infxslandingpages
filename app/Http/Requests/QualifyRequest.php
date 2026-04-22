<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PageType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QualifyRequest extends FormRequest
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
            'source_page_key' => [
                'required',
                'string',
                Rule::exists('pages', 'page_key')
                    ->where('page_type', PageType::Landing->value)
                    ->where('is_active', true),
            ],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.step_key' => ['required', 'string', 'max:100'],
            'answers.*.field_key' => ['required', 'string', 'max:100'],
            'answers.*.value' => ['required', 'string', 'max:1000'],
            'consent' => ['required', 'accepted'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'referrer' => ['nullable', 'url', 'max:500'],
        ];
    }
}
