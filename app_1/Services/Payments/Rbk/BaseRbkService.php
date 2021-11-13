<?php

namespace App\Services\Payments\Rbk;

use App\Constants\Roles;
use App\UserConfirmation;
use App\Constants\UserConfirmationTypes;
use Illuminate\Support\Facades\Log;

abstract class BaseRbkService
{
    private $apiKey;
    protected $shopId;
    protected $orderId;
    protected $baseApiUrl;
    protected $curlStatus;

    public function getHttpStatus()
    {
        return $this->curlStatus;
    }

    /**
     * @param $baseApiUrl
     */
    protected function setBaseApiUrl($baseApiUrl)
    {
        $this->baseApiUrl = $baseApiUrl;
    }

    /**
     * RbkMoneyService constructor.
     * @param $apiKey
     * @param $shopId
     * @throws \ErrorException
     */
    public function __construct()
    {
        $apiKey = config('services.rbk_money.api_key');
        $shopId = config('services.rbk_money.shop_id');

        if (! $apiKey || ! $shopId) {
            abort(503, 'You must setup api key and shop id');
        }

        $this->apiKey = $apiKey;
        $this->shopId = $shopId;
    }

    protected function replaceUrlParams($url, $params)
    {
        foreach ($params as $paramName => $paramValue) {
            $url = str_replace('{' . $paramName . '}', $paramValue, $url);
        }

        return $url;
    }

    /**
     * @param $url
     * @param $apiKey
     * @param array $requestedData
     * @param string $method
     * @param mixed $invoiceAccessToken
     * @throws \ErrorException
     * @return \Illuminate\Support\Collection
     */
    protected function makeRequestByAccessToken($url, $invoiceAccessToken, $requestedData = [], $method = 'POST')
    {
        return $this->makeRequest($url, $requestedData, $method, $invoiceAccessToken);
    }

    /**
     * @param $url
     * @param $requestData
     * @param mixed $method
     * @param null|mixed $apiKey
     * @throws \ErrorException
     * @return \Illuminate\Support\Collection
     */
    protected function makeRequest($url, $requestData = [], $method = 'POST', $apiKey = null)
    {
        $fullUrl = $this->baseApiUrl . '/' . $url;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => $this->prepareHeaders($apiKey),
        ]);
        $response = curl_exec($curl);
        $this->curlStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        Log::info('make request with headers', [
            'headers' => $this->prepareHeaders($apiKey),
            'url' =>  $this->baseApiUrl . '/' . $url,
        ]);

        curl_close($curl);

        if ($curlError) {
            throw new \ErrorException('Could not start and process payment: ' . $curlError);
        }

        return json_decode($response, true);
    }

    /**
     * @param null|mixed $authKey
     * @return array
     */
    protected function prepareHeaders($authKey = null)
    {
        $headers = [];
        $headers[] = 'X-Request-ID: ' . uniqid();
        if ($authKey) {
            $headers[] = 'Authorization: Bearer ' . $authKey;
        } else {
            $headers[] = 'Authorization: Bearer ' . $this->apiKey;
        }
        $headers[] = 'Content-type: application/json; charset=utf-8';
        $headers[] = 'Accept: application/json';

        return $headers;
    }

    /**
     *
     * @todo remove this
     * @param $email
     * @return \Illuminate\Support\Collection
     */
    public function createUserConfirmations($email)
    {
        $emailConfirmation = UserConfirmation::createConfirmation([
            'email' => $email,
            'role' => Roles::STUDENT,
            'type' => UserConfirmationTypes::EMAIL,
        ]);

        $webConfirmation = UserConfirmation::createConfirmation([
            'email' => $email,
            'role' => Roles::STUDENT,
            'type' => UserConfirmationTypes::WEB,
        ]);

        return collect([
            'email_confirmation' => $emailConfirmation,
            'web_confirmation' => $webConfirmation,
        ]);
    }
}
