<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use App\Services\Payments\Cloudpayments\CloudpaymentService;
use Illuminate\Http\Request;


/**
 * @group Payments / Cloudpayments
 */
class CloudpaymentsController extends Controller
{
    protected $cloudpaymentService;

    public function __construct(CloudpaymentService $cloudpaymentService)
    {
        $this->cloudpaymentService = $cloudpaymentService;
    }

    /**
     * @param Request $request
     * @param CloudpaymentService $cloudpaymentService
     * @throws \ErrorException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function recurrent(Request $request)
    {
        $this->cloudpaymentService->setCfIpAddress($request->header('cf-connecting-ip'));
        $this->cloudpaymentService->recurrent($request->all());

        return response()->json([
            'code' => 0
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refund(Request $request)
    {
        $this->cloudpaymentService->setCfIpAddress($request->header('cf-connecting-ip'));
        $this->cloudpaymentService->refund($request->all());

        return response()->json([
            'code' => 0
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function pay(Request $request)
    {
        $this->cloudpaymentService->setCfIpAddress($request->header('cf-connecting-ip'));
        $this->cloudpaymentService->pay($request->all());

        return response()->json([
            'code' => 0
        ]);
    }


    /**
     * See more info here https://developers.cloudpayments.ru/#check
     *
     * @param Request $request
     * @param CloudpaymentService $checkService
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        try {
            $this->cloudpaymentService->setCfIpAddress($request->header('cf-connecting-ip'));
            $this->cloudpaymentService->check($request->all());

            return response()->json([
                'code' => 0
            ]);

        } catch (\Throwable $throwable) {
            return response()->json([
                'code' => 13
            ]);
        }
    }

}