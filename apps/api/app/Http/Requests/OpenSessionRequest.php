<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpenSessionRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string|int>>
     */
    public function rules(): array
    {
        return [
            'duration_seconds' => ['sometimes', 'integer', 'min:1', 'max:86400'],
        ];
    }

    public function durationSeconds(): ?int
    {
        return $this->has('duration_seconds')
            ? $this->integer('duration_seconds')
            : null;
    }
}
