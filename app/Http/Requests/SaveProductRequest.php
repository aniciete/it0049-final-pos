<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to submit this form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Trim incoming text fields before validation runs.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name', '')),
            'price' => trim((string) $this->input('price', '')),
            'stock_quantity' => trim((string) $this->input('stock_quantity', '')),
            'remove_image' => $this->boolean('remove_image'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'price' => ['required', 'numeric', 'gt:0', 'max:999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get clear validation messages for the product form.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product Name is required.',
            'name.min' => 'Product Name must be at least 2 characters.',
            'name.max' => 'Product Name cannot exceed 100 characters.',
            'price.required' => 'Unit Price is required.',
            'price.numeric' => 'Unit Price must be a valid decimal amount.',
            'price.gt' => 'Unit Price must be greater than zero.',
            'stock_quantity.required' => 'Stock Quantity is required.',
            'stock_quantity.integer' => 'Stock Quantity must be a whole number.',
            'stock_quantity.min' => 'Stock Quantity cannot be negative.',
            'image.file' => 'Please select a valid product image file.',
            'image.image' => 'Product image must be a valid image file.',
            'image.mimes' => 'Product image must be a JPG, PNG, or WebP file.',
            'image.max' => 'Product image must not be larger than 2 MB.',
        ];
    }
}
