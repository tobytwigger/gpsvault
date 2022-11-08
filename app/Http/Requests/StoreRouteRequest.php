<?php

namespace App\Http\Requests;

use App\Services\Filepond\FilePondFile;
use App\Services\Filepond\FilepondRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRouteRequest extends FormRequest
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
            'file' => ['sometimes', 'nullable', app(FilepondRule::class)],
            'name' => 'required|nullable|max:255',
            'description' => 'sometimes|nullable|max:65535',
        ];
    }
}
