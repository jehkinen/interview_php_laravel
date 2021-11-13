<?php

namespace App\Http\Requests;

use App\Traits\DetectRequestType;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    use DetectRequestType;

    protected $dto;

    /**
     * @return mixed
     */
    public function getDto()
    {
        return $this->dto;
    }

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();
}
