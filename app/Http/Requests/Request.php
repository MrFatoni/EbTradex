<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

abstract class Request extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        if ($this->ajax()) {
            $fakeFields = config('fakefields.input_keys');
            $newErrors = [];
            foreach ($errors as $key => $value) {
                $key = isset($fakeFields[$key]) ? $fakeFields[$key] : $key;
                $newErrors[$key] = $value;
            }
            throw new HttpResponseException(response()->json(['errors' => $newErrors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }
        elseif ( $this->is('api/*') )
        {
            throw new HttpResponseException(response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }
        else {
            throw (new ValidationException($validator))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
        }
    }
}
