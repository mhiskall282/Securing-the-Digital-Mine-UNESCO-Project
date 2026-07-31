<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateStatus', $this->route('activity'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,done'],
            'remark' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'A status (pending or done) is required.',
            'status.in' => 'Status must be either pending or done.',
            'date.required' => 'The activity date is required.',
        ];
    }
}
