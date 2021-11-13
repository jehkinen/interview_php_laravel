<?php

namespace App\Traits;

use Illuminate\Http\Request;

/**
 * Class DetectRequestType.
 * @mixin Request
 */
trait DetectRequestType
{
    /**
     * @return bool
     */
    public function isUpdateRequest()
    {
        return $this->isMethod('put') || $this->isMethod('patch');
    }

    /**
     * @return bool
     */
    public function isCreateRequest()
    {
        return $this->isMethod('post');
    }

    /**
     * @return bool
     */
    public function isDeleteRequest()
    {
        return $this->isMethod('delete');
    }
}
