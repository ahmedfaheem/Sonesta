<?php

namespace App\Http\Requests;

use App\Models\Floor;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $floor = $this->route('floor');

        return $floor instanceof Floor && ($this->user()?->can('update', $floor) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'manager_id' => [
                Rule::requiredIf(fn () => $this->user()?->hasRole('admin')),
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value === null) {
                        return;
                    }

                    $manager = User::query()->find($value);

                    if (! $manager?->hasRole('manager')) {
                        $fail('The selected manager is invalid.');
                    }
                },
            ],
        ];
    }
}
