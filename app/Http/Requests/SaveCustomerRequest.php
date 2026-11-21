<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to submit this form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Trim text inputs and normalize blank optional phone numbers to null.
     */
    protected function prepareForValidation(): void
    {
        $phone = trim((string) $this->input('phone', ''));

        $this->merge([
            'full_name' => trim((string) $this->input('full_name', '')),
            'email' => strtolower(trim((string) $this->input('email', ''))),
            'phone' => $phone === '' ? null : $phone,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Customer|null $customer */
        $customer = $this->route('customer');

        return [
            'full_name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:100',
                Rule::unique('customers', 'email')->ignore($customer?->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get clear validation messages displayed in the customer form.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Full name is required.',
            'full_name.min' => 'Full name must be at least 2 characters.',
            'full_name.max' => 'Full name must not exceed 100 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must not exceed 100 characters.',
            'email.unique' => 'That email address is already registered to another customer.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
        ];
    }
}
