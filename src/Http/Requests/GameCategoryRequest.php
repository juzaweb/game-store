<?php

namespace Juzaweb\Modules\GameStore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GameCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:250'],
        ];
    }
}
