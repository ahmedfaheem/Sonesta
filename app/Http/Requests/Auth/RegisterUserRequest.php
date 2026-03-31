<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class RegisterUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:255', 'unique:users,national_id'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'avatar' => [
                'nullable',
                File::image()->types(['jpg', 'jpeg', 'png', 'webp']),
            ],
            'country' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
        ];
    }
}
