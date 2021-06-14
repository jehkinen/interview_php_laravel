<?php

namespace App\Http\Requests;

use App\Dto\SupplierDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    /** @var SupplierDto */
    private $dto;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function validateResolved()
    {
       $this->dto = new SupplierDto(
            $this->input('name'),
            $this->input('info'),
            $this->input('rules'),
            $this->input('url'),
            $this->input('distinct'),
            $this->input('address'),
        );

    }

    /**
     * @return SupplierDto
     */
    public function getDto()
    {
        return $this->dto;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'info' => ['sometimes'],
            'url' => ['required'],
            'rules' => ['required'],
            'address' => ['required'],
            'distinct' => ['required'],
        ];
    }
}
