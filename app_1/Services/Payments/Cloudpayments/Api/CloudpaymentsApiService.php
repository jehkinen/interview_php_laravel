<?php


namespace App\Services\Payments\Cloudpayments\Api;


use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class CloudpaymentsApiService
{
    private $privateKey;
    private $publicKey;
    private $config;

    /** @var Client client */
    private $client;

    public function __construct()
    {
        $this->publicKey = config('cloudpayments.public_key');
        $this->privateKey = config('cloudpayments.private_key');

        $this->client = new Client([
            'base_uri' => 'https://api.cloudpayments.ru',
        ]);

        $this->config = [
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'auth' => [
                $this->publicKey,
                $this->privateKey
            ]
        ];
    }


    /**
     * @param $subscriptionId
     * @return mixed
     * @throws \ErrorException
     */
    public function changeSubscription(UpdateSubscriptionDto $dto)
    {
        $result = $this->sendRequest(CloudpaymentsEndpoints::SUBSCRIPTION_UPDATE, [
            'Id' => $dto->getId(),
            'Description' => $dto->getDescription(),
            'Amount' => $dto->getAmount(),
            'StartDate' => $dto->getStartDate(),
            'Interval' => $dto->getInterval(),
            'Period' => $dto->getPeriod(),
            'CultureName' => $dto->getCultureName()
        ]);

        return Arr::get($result, 'Model');
    }

    /**
     * @param $subscriptionId
     * @return array|\ArrayAccess|mixed
     * @throws \ErrorException
     */
    public function cancelSubscription($subscriptionId)
    {
        $result = $this->sendRequest(CloudpaymentsEndpoints::SUBSCRIPTION_CANCEL, [
            'Id' => $subscriptionId
        ]);

        return Arr::get($result, 'Success');
    }



    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws \ErrorException
     */
    private function sendRequest(
        $url,
        $params = [],
        $method = CloudpaymentsEndpoints::METHOD_POST
    ) {
        $options = array_merge($this->config, [
            'body' => json_encode($params),
        ]);

        switch ($method) {
            case CloudpaymentsEndpoints::METHOD_POST:
                $response = $this->client->post($url, $options);
                break;
            case CloudpaymentsEndpoints::METHOD_GET:
                $response = $this->client->get($url, $options);
                break;
            default:
                throw new \ErrorException('Unsupported method while calling cp api service');
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $subscriptionId
     * @return mixed
     * @throws \ErrorException
     */
    public function fetchSubscription($subscriptionId)
    {
        $result = $this->sendRequest(CloudpaymentsEndpoints::SUBSCRIPTION_GET, [
            'Id' => $subscriptionId
        ]);

        return Arr::get($result, 'Model');
    }

}