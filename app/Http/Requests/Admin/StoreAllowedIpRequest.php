<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Rules\IpOrSubnet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAllowedIpRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'range' => [
                'required',
                'string',
                'max:43',
                new IpOrSubnet,
                Rule::unique('allowed_ips'),
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
