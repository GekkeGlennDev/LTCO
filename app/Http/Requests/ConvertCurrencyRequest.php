<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\CurrencyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConvertCurrencyRequest extends FormRequest
{
    /**
     * Redirect to a clean home page, redirecting back would loop on the invalid query string.
     */
    protected $redirectRoute = 'home';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $currencyService = app(CurrencyService::class);

        return [
            'currency' => ['sometimes', 'string', Rule::in($currencyService->all())],
            'amount' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
