<?php


namespace App\Services\Payments\Cloudpayments\Api;


/**
 * Class UpdateSubscriptionDto
 * @package App\Services\Payments\Cloudpayments\Api
 *
 * More info about subscriptions
 * https://developers.cloudpayments.ru/#izmenenie-podpiski-na-rekurrentnye-platezhi
 *
 */
class UpdateSubscriptionDto
{
    private $Id;
    private $Description;
    private $Amount;
    private $StartDate;
    private $Interval;
    private $Period;

    /**
     * UpdateSubscriptionDto constructor.
     * @param $Id
     * @param $Description
     * @param $Amount
     * @param $StartDate
     * @param $Interval
     * @param $Period
     */
    public function __construct($Id, $Description, $Amount, $StartDate, $Interval, $Period)
    {
        $this->Id = $Id;
        $this->Description = $Description;
        $this->Amount = $Amount;
        $this->StartDate = $StartDate;
        $this->Interval = $Interval;
        $this->Period = $Period;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->Description;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->Amount;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->StartDate;
    }

    /**
     * @return mixed
     */
    public function getInterval()
    {
        return $this->Interval;
    }

    /**
     * @return mixed
     */
    public function getPeriod()
    {
        return $this->Period;
    }


    /**
     * If it required more lang this method could be extended
     * @return string
     */
    public function getCultureName()
    {
        return 'ru-Ru';
    }

}