<?php

namespace App\Http\Requests;

use App\Rules\ValidRating;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return Auth::check() && Auth::user()->role === 'user';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating'  => ['required', new ValidRating],
            'comment' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'please add rating',
            'rating.min'      => 'rate should be > 1',
            'rating.max'      => 'rate should be < 5',
            'comment.max'     => 'comment max length is 500',
        ];
    }
}
