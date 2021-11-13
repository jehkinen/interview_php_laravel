<?php

namespace App\Services\Payments\Rbk;

class RecurrentCheckResult
{
    protected $result;

    protected $abortCharge = false;

    /**
     * @return bool
     */
    public function isChargeAborted()
    {
        return $this->abortCharge === true;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($array)
    {
        $this->result = collect($array);

        return $this;
    }

    public function abortCharge()
    {
        $this->abortCharge = true;

        return $this;
    }
}
