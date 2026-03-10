<?php

namespace App\Http\Requests\Ticket;

use App\Enums\TicketPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
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
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')],
            'category_id' => ['nullable', 'integer', Rule::exists('ticket_categories', 'id')],
            'priority' => ['required', 'string', Rule::enum(TicketPriority::class)],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'A ticket subject is required.',
            'subject.max' => 'The subject must not exceed 255 characters.',
            'description.required' => 'A ticket description is required.',
            'priority.required' => 'Please select a priority level.',
            'priority.in' => 'The selected priority is invalid.',
            'attachments.*.max' => 'Each attachment must not exceed 10MB.',
            'attachments.*.mimes' => 'Attachments must be a file of type: jpg, jpeg, png, gif, pdf, doc, docx, txt, zip.',
        ];
    }
}
