<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to submit this form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize optional customer selection so blank defaults cleanly to null (walk-in).
     */
    protected function prepareForValidation(): void
    {
        $customerId = trim((string) $this->input('customer_id', ''));

        $this->merge([
            'customer_id' => $customerId === '' ? null : $customerId,
        ]);
    }

    /**
     * Get the validation rules that apply to recording a sale.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'customer_id' => [
                'nullable',
                'integer',
                Rule::exists('customers', 'id'),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get clear validation messages for the sale register form.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product to sell.',
            'product_id.integer' => 'Selected product identifier is invalid.',
            'product_id.exists' => 'Selected product was not found or has been archived.',
            'customer_id.integer' => 'Selected customer identifier is invalid.',
            'customer_id.exists' => 'Selected customer account does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a positive whole number.',
            'quantity.min' => 'Quantity must be at least 1 unit.',
        ];
    }
}
