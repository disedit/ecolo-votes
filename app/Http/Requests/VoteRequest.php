<?php

namespace App\Http\Requests;

use App\Rules\ValidVote;
use App\Rules\VoteIsOpen;
use App\Rules\CodeCorrect;
use Illuminate\Foundation\Http\FormRequest;

class VoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->code()->wasPickedUp(); 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vote_id' => [
                'required',
                new VoteIsOpen(),
            ],
            'option_ids' => [
                'required',
                new ValidVote()
            ],
            'code' => [
                new CodeCorrect()
            ]
        ];
    }
}
