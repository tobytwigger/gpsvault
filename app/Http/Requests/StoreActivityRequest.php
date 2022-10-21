<?php

namespace App\Http\Requests;

use App\Services\Filepond\FilePondFile;
use App\Services\Filepond\FilepondRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'file' => ['required', app(FilepondRule::class)],
            'name' => 'sometimes|nullable|max:255',
        ];
    }
}
