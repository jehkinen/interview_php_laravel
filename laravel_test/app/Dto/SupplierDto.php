<?php


namespace App\Dto;


class SupplierDto
{
    private $name;
    private $info;
    private $rules;
    private $distinct;
    private $url;
    private $address;

    /**
     * SupplierDto constructor.
     * @param $name
     * @param $info
     * @param $rules
     * @param $distinct
     * @param $url
     * @param $address
     */
    public function __construct($name, $info, $rules, $distinct, $url, $address)
    {
        $this->name = $name;
        $this->info = $info;
        $this->rules = $rules;
        $this->distinct = $distinct;
        $this->url = $url;
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return mixed
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return mixed
     */
    public function getDistinct()
    {
        return $this->distinct;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }
}
