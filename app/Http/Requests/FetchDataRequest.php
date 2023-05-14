<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class FetchDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'company_symbol' => ['required', 'string'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date', 'before_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date', 'before_or_equal:today'],
            'email' => ['required', 'email'],
        ];
    }
    public function messages()
    {
        return [
            'company_symbol.required' => 'Company symbol is required',
            'company_symbol.string' => 'Company symbol must be a string',
            'start_date.required' => 'Start date is required',
            'start_date.date' => 'Start date must be a date',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date',
            'start_date.before_or_equal' => 'Start date must be before or equal to today',
            'end_date.required' => 'End date is required',
            'end_date.date' => 'End date must be a date',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
            'end_date.before_or_equal' => 'End date must be before or equal to today',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
        ];
    }
    public function attributes()
    {
        return [
            'company_symbol' => 'Company Symbol',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'email' => 'Email',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $response = redirect()->back()
            ->withErrors($validator)
            ->withInput();
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

}
