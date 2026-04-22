<?php

namespace App\Http\Requests;

use App\Enums\VoteOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterVoteRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'member_id' => ['required', 'string', 'max:64'],
            'option' => ['required', 'string', Rule::enum(VoteOption::class)],
        ];
    }

    public function memberId(): string
    {
        return $this->string('member_id')->toString();
    }

    public function option(): VoteOption
    {
        return $this->enum('option', VoteOption::class);
    }
}
