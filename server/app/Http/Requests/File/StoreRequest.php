<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'name' => ['required', 'string', 'unique:files,name'],
            // 'uri' => ['required', 'string'],
            // 'size' => ['required', 'integer', 'min:0'],
            // 'file_type_id' => ['required', 'integer', 'exists:file_types,id'],
            'files' => ['required', 'array'],
        ];
    }
}
