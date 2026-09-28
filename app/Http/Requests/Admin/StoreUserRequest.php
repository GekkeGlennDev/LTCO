<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\DTOs\UserInfo;
use App\ValueObjects\Email;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users'),
            ],
            'is_admin' => ['sometimes', 'boolean'],
        ];
    }

    public function toUserDto(): UserInfo
    {
        return new UserInfo(
            name: $this->validated('name'),
            email: new Email($this->validated('email')),
            isAdmin: $this->boolean('is_admin'),
        );
    }
}
