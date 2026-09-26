<?php

namespace App\Http\Requests\Task;

use App\Enums\BoardMemberRole;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assigned_to' => ['nullable', 'integer',
                function (string $attribute, mixed $value, Closure $fail) {
                    $board = $this->route('board');

                    $isEligibleUser = $board->members()
                        ->where('user_id', $value)
                        ->whereNot('role', BoardMemberRole::VIEWER)
                        ->exists();

                    if (! $isEligibleUser) {
                        $fail('This user is not an eligible board member.');
                    }
                },
            ],
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer',
        ];
    }
}
