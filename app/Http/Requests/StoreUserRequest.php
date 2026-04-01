<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'national_id' => ['nullable', 'string', 'max:255', 'unique:users,national_id'],
            'phone' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female'],
            'is_approved' => ['sometimes', 'boolean'],
            'avatar' => [
                'nullable',
                File::image()
                    ->types(['jpg', 'jpeg', 'png', 'webp']),
            ],
        ];
    }
}
