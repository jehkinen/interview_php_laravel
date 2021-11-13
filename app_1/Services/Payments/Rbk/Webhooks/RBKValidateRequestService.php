<?php


namespace App\Services\Payments\Rbk\Webhooks;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class RBKValidateRequestService
{
    private $requestContent;
    private $requestContentAsArray;

    /**
     * @param $requestContent
     * @throws \ErrorException
     */
    public function validate($requestContent)
    {
        $this->requestContent = $requestContent;
        $this->verifySignature();

        Storage::disk('payments')->put('responses/' . uniqid('rbk_') . '.txt', $this->requestContent);
    }

    /**
     * Check if signature is valid.
     * @param $data
     * @param $decodedSignature
     * @param $publicKey
     * @throws \ErrorException
     * @return bool
     */
    public function verifySignature()
    {
        $httpContentSignature = $_SERVER['HTTP_CONTENT_SIGNATURE'] ?? null;

        if (! $httpContentSignature) {
            abort(500, 'HTTP_CONTENT_SIGNATURE is empty, could not process this request');
        }

        // Достаем сигнатуру из заголовка и декодируем
        $signatureFromHeader = $this->getSignatureFromHeader($httpContentSignature);

        // Декодируем данные
        $decodedSignature = $this->urlSafeBase64Decode($signatureFromHeader);

        $publicKey = Storage::disk('payments')->get('keys/webook.public.key');

        if (config('app.env') === 'testing') {
            return true;
        }

        if (empty($this->requestContent) || empty($decodedSignature) || empty($publicKey)) {
            return false;
        }

        $publicKeyId = openssl_get_publickey($publicKey);

        if (empty($publicKeyId)) {
            return false;
        }

        $verify = openssl_verify($this->requestContent, $decodedSignature, $publicKeyId, OPENSSL_ALGO_SHA256);

        $isVerified = ($verify == 1);

        if (! $isVerified) {
            throw new \ErrorException('web hook notification signature mismatch', 400);
        }

        return $isVerified;
    }

    /**
     * @param $contentSignature
     * @return string|string[]|null
     */
    private function getSignatureFromHeader($contentSignature)
    {
        $signature = preg_replace("/alg=(\S+);\sdigest=/", '', $contentSignature);

        if (empty($signature)) {
            abort(400, 'Signature is missing');
        }

        return $signature;
    }

    /**
     * @param $string
     * @return bool|string
     */
    private function urlSafeBase64Decode($string)
    {
        return base64_decode(strtr($string, '-_,', '+/='));
    }

    /**
     * @param $key
     * @return array|\ArrayAccess|mixed
     */
    public function getKeyFromRequest($key)
    {
        $requestContent = $this->requestContent;
        $requestContentAsArray = json_decode($requestContent, true);
        $this->requestContentAsArray = $requestContentAsArray;
        return Arr::get($requestContentAsArray, $key);
    }

    public function dataAsArray()
    {
        return $this->requestContentAsArray;
    }
}