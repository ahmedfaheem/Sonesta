<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateUserRequest extends FormRequest
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
        $user = $this->routeUser();

        return [
            'name' => ['sometimes', 'required', 'string', 'min:3'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'password' => ['nullable', 'string', 'min:6'],
            'national_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'national_id')->ignore($user),
            ],
            'is_approved' => ['sometimes', 'boolean'],
            'avatar' => [
                'nullable',
                File::image()
                    ->types(['jpg', 'jpeg']),
            ],
        ];
    }

    protected function routeUser(): ?User
    {
        foreach (['user', 'manager', 'receptionist', 'client'] as $parameter) {
            $value = $this->route($parameter);

            if ($value instanceof User) {
                return $value;
            }
        }

        return null;
    }
}
