<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true; // NOTE: This is true and does not a role check because the route already uses the CheckUserType middleware to validate the user type!
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:today',
            'days_to_deadline' => 'required|integer|min:3',
            'min_wage' => 'nullable|integer|min:0',
            'max_wage' => 'nullable|integer|min:0',
            'requirements' => 'nullable|string',
            'status' => 'required|in:Pending,Active,Expired,Closed',
            'city_id' => 'nullable|integer',
            'tags' => 'nullable|array', // Must be empty or array of tags!
            'tags.*' => 'exists:tag,id' // We have to ensure any tag in the tags arraw is actually a tag!
        ];
    }
}
