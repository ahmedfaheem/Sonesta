<?php

namespace App\Http\Requests;

use App\Models\Floor;
use App\Models\Room;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Room::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'regex:/^\d{4,}$/', 'max:255', 'unique:rooms,number'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'floor_id' => [
                'required',
                'integer',
                Rule::exists('floors', 'id'),
                function (string $attribute, mixed $value, Closure $fail): void {
                    $floor = Floor::query()->find($value);
                    $user = $this->user();

                    if (! $floor || ! $user) {
                        $fail('The selected floor is invalid.');

                        return;
                    }

                    if ($user->hasRole('manager') && $floor->manager_id !== $user->id) {
                        $fail('You can only assign rooms to your own floors.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'number.regex' => 'The room number must be at least 4 digits.',
        ];
    }
}
