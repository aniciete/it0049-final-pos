<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to submit this form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize incoming staff fields and default blank roles before validation runs.
     */
    protected function prepareForValidation(): void
    {
        $role = trim((string) $this->input('role', ''));
        $email = strtolower(trim((string) $this->input('email', '')));

        $this->merge([
            'username' => trim((string) $this->input('username', '')),
            'full_name' => trim((string) $this->input('full_name', '')),
            'email' => $email === '' ? null : $email,
            'role' => $role === '' ? 'Staff Member' : $role,
            'remove_avatar' => $this->boolean('remove_avatar'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');
        $isEdit = $user !== null;

        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($user?->id),
            ],
            'full_name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [
                $isEdit ? 'nullable' : 'required',
                'string',
                'min:8',
                'max:72',
                'confirmed',
            ],
            'role' => ['nullable', 'string', 'max:50', Rule::in(User::ROLES)],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get clear validation messages displayed in the staff user form.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.max' => 'Username must not exceed 50 characters.',
            'username.regex' => 'Username may only contain letters, numbers, periods, hyphens, and underscores.',
            'username.unique' => 'That username is already assigned to another staff account.',
            'full_name.required' => 'Full name is required.',
            'full_name.min' => 'Full name must be at least 2 characters.',
            'full_name.max' => 'Full name must not exceed 100 characters.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must not exceed 150 characters.',
            'email.unique' => 'That email address is already linked to another staff account.',
            'password.required' => 'Password is required for new staff accounts.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password cannot exceed 72 characters.',
            'password.confirmed' => 'Password confirmation must match the password.',
            'role.in' => 'Please choose one of the supported system roles.',
            'avatar.file' => 'Please select a valid avatar image file.',
            'avatar.image' => 'Avatar must be a valid image file.',
            'avatar.mimes' => 'Avatar must be a JPG, PNG, or WebP file.',
            'avatar.max' => 'Avatar image must not be larger than 2 MB.',
        ];
    }
}
