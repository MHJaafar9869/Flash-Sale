<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\Enums\OrderStatusEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentStatusRequest extends FormRequest
{
    protected string $allowed;

    protected function prepareForValidation()
    {
        $this->allowed = implode(',', array_map(fn ($case) => $case->value, OrderStatusEnum::cases()));
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => "required|in:{$this->allowed}",
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'The payment status is required.',
            'status.in' => "The status must be one of the following: {$this->allowed}.",
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'validation errors',
            'errors' => $validator->errors(),
        ], 422));
    }
}
