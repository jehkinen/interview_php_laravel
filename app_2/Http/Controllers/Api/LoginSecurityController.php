<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\LoginSecurityService;
use App\Http\Requests\EnableTwoFaRequest;
use App\Http\Requests\DisableTwoFaRequest;

class LoginSecurityController extends Controller
{
    /** @var LoginSecurityService */
    private $loginSecurityService;

    public function __construct(LoginSecurityService $loginSecurityService)
    {
        $this->loginSecurityService = $loginSecurityService;
        if (auth()->check()) {
            /** @var User $user */
            $user = auth()->user();
            $this->loginSecurityService->setUser($user);
        }
    }

    /**
     * Generate QR code for google auth.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateQrCode()
    {
        $qrCodeUrl = $this->loginSecurityService->generateQRCode();

        return response()->json($qrCodeUrl);
    }

    /**
     * Enable 2fa for current user.
     * @bodyParam string required google 2fa secret
     *
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function enable2fa(EnableTwoFaRequest $request)
    {
        $result = $this->loginSecurityService->enable2fa($request->input('secret'));

        return response()->json($result->all());
    }

    /**
     * Disable 2fa for current user.
     *
     * @bodyParam string required current_password current user password
     *
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function disable2fa(DisableTwoFaRequest $request)
    {
        $result = $this->loginSecurityService->disable2fa($request->input('current_password'));

        return response()->json($result->all());
    }
}
