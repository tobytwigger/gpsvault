<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\MessageBag;

class SyncRequest extends FormRequest
{
    /**
     * @var MessageBag[]
     */
    private array $additionalErrors = [];

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
            'tasks' => 'required|array|min:1|max:20',
            'tasks.*.id' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!app()->has('tasks.' . $value)) {
                    $fail(sprintf('The task %s is not a valid task.', $value));
                }
                if (app()->make('tasks.' . $value)->disabled($this->user())) {
                    $fail(sprintf('The task %s is disabled and so cannot be used.', $value));
                }
            }],
            'tasks.*.config' => 'sometimes|array'
        ];
    }

    public function withValidator(Validator $validator)
    {
        foreach (app()->tagged('tasks') as $task) {
            foreach ($task->validationRules() as $key => $rules) {
                $validator->sometimes(
                    'tasks.*.config',
                    [function ($attribute, $value, $fail) use ($key, $rules, $validator) {
                        $data = array_key_exists($key, $value) ? [$key => $value[$key]] : [];
                        $v = \Illuminate\Support\Facades\Validator::make($data, [$key => $rules]);
                        if ($v->fails()) {
                            foreach ($v->errors()->toArray() as $errorKey => $errorMessages) {
                                foreach ($errorMessages as $errorMessage) {
                                    $validator->errors()->add($attribute . '.' . $errorKey, $errorMessage);
                                }
                            }
                            $fail('The task configuration is invalid');
                        }
                    }],
                    fn ($input, $item) => $item->id === $task::id()
                );
            }
        }
    }
}
