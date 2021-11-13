<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;

trait ValidationErrorTrait
{
    public function throwClientError($field, $messages)
    {
        $error = ValidationException::withMessages([
            $field => $messages,
        ]);
        throw $error;
    }
}
