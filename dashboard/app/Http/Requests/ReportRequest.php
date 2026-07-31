<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Activity::class);
    }

    public function rules(): array
    {
        return [
            'from' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'required', 'date', 'date_format:Y-m-d', 'after_or_equal:from'],
            'status' => ['nullable', 'in:pending,done'],
            'activity_id' => ['nullable', 'integer', 'exists:activities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.required' => 'A start date is required.',
            'to.required' => 'An end date is required.',
            'to.after_or_equal' => 'End date must be on or after the start date.',
        ];
    }
}
