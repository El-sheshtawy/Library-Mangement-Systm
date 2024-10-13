<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|exists:users,id',
            'notification_id' => 'sometimes|required|exists:notifications,id',
            'is_read' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
            'receiver_id' => 'nullable|exists:users,id',
        ];
    }
}
