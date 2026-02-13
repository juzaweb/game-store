<?php

namespace Juzaweb\Modules\GameStore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Juzaweb\Modules\GameStore\Enums\GameStatus;

class GameRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:250'],
            'content' => ['nullable', 'string', 'max:20000'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:game_categories,id'],
            'vendors' => ['nullable', 'array'],
            'platforms' => ['nullable', 'array'],
            'languages' => ['nullable', 'array'],
            'views' => ['nullable', 'integer'],
            'status' => ['required', 'string', Rule::enum(GameStatus::class)],
            'thumbnail' => ['nullable', 'string'],
            'download_links' => ['nullable', 'array'],
            'download_links.*.title' => ['nullable', 'string', 'max:250'],
            'download_links.*.url' => ['nullable', 'url', 'max:1000'],
            'download_links.*.size' => ['nullable', 'string', 'max:50'],
            'download_links.*.platform' => ['nullable', 'string', 'max:50'],
            'minimum_requirements' => ['nullable', 'string', 'max:10000'],
            'recommended_requirements' => ['nullable', 'string', 'max:10000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['nullable', 'boolean'],
        ];
    }
}
