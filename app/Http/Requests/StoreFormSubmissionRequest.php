<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFormSubmissionRequest extends FormRequest
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
            'form_type' => ['required', 'string', 'max:50'],
            'form_name' => ['nullable', 'string', 'max:150'],
            'name' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'course' => ['nullable', 'string', 'max:150'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'course_name' => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'message' => ['nullable', 'string', 'max:5000'],
            'page_url' => ['nullable', 'string', 'max:1000'],
            'page_title' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:100'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_medium' => ['nullable', 'string', 'max:100'],
            'utm_campaign' => ['nullable', 'string', 'max:100'],
            'utm_term' => ['nullable', 'string', 'max:150'],
            'utm_content' => ['nullable', 'string', 'max:150'],
            'additional_data' => ['nullable', 'array'],
            // Anti-spam honeypot fields
            'website_url_hp' => ['nullable', 'string'],
            'b_fax_hp' => ['nullable', 'string'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('form_type');

            // For lead, demo, enquiry, callback, contact forms, at least phone or email is required
            if (in_array($type, ['contact', 'book_demo', 'hero_demo', 'course_enquiry', 'callback', 'brochure', 'lead'])) {
                if (empty($this->input('phone')) && empty($this->input('email')) && empty($this->input('whatsapp_number'))) {
                    $validator->errors()->add('phone', 'Please provide a valid phone number or email address so we can contact you.');
                }
            }
        });
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.email' => 'Please enter a valid email address.',
            'phone.max' => 'Phone number cannot exceed 50 characters.',
            'course_id.exists' => 'The selected course does not exist.',
        ];
    }
}
