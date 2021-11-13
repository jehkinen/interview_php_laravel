<?php

namespace App\Services;

use BaconQrCode\Writer;
use App\Traits\HasUserTrait;
use App\Models\LoginSecurity;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\App;
use App\Traits\ValidationErrorTrait;
use Illuminate\Support\Facades\Hash;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;

class LoginSecurityService
{
    const DEV_SECRET = 111222;

    use ValidationErrorTrait;
    use HasUserTrait;

    /** @var Google2FA */
    private $googleTwoFaService;

    private $serviceResult;

    public function __construct()
    {
        $this->googleTwoFaService = new Google2FA();
        $this->serviceResult = collect([
            'success' => true,
        ]);
    }

    /**
     * Generate qr code and secret for Google Auth.
     * @return array
     */
    public function generateQRCode()
    {
        if (! $this->getUser()->loginSecurity || ! $this->getUser()->loginSecurity->google2fa_secret) {
            $this->generate2faSecret();
            $this->getUser()->refresh();
        }

        $g2faUrl = $this->googleTwoFaService->getQRCodeUrl(
            config('app.name'),
            $this->getUser()->email,
            $this->getUser()->loginSecurity->google2fa_secret
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            )
        );

        return [
            'image' =>  'data:image/png;base64,' . base64_encode($writer->writeString($g2faUrl)),
            'otp_url' => $g2faUrl,
            'secret' => $this->getUser()->loginSecurity->google2fa_secret,
        ];
    }

    public function generate2faSecret()
    {
        /** @var LoginSecurity $loginSecurity */
        $loginSecurity = $this->getUser()->loginSecurity()->firstOrCreate();
        $loginSecurity->google2fa_secret = $this->googleTwoFaService->generateSecretKey();
        $loginSecurity->save();
    }

    /**
     * @param $secret
     * @throws SecretKeyTooShortException
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @return bool
     */
    public function validateCode($secret)
    {
        $verifySecretDev = (! App::environment('production') && (int) trim($secret) === self::DEV_SECRET);
        $verifySecret = $this->googleTwoFaService->verifyKey($this->getUser()->loginSecurity->google2fa_secret, $secret) || $verifySecretDev;

        return $verifySecret || $verifySecretDev;
    }

    /**
     * @param $secret
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @return false|\Illuminate\Support\Collection
     */
    public function enable2fa($secret)
    {
        $loginSecurity = $this->getUser()->loginSecurity()->firstOrCreate();

        $invalidCodeError = 'Invalid verification Code, Please try again.';

        try {
            $verifySecret = $this->validateCode($secret);
        } catch (SecretKeyTooShortException $exception) {
            $this->throwClientError('secret', $invalidCodeError);

            return false;
        }

        if ($verifySecret) {
            $loginSecurity->google2fa_enabled = true;
            $loginSecurity->save();

            $this->serviceResult->put('success', 'true');
            $this->serviceResult->put('message', '2FA is enabled successfully.');

            return $this->serviceResult;
        }
        $this->throwClientError('secret', $invalidCodeError);

        return false;
    }

    /**
     * @param $currentPassword
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Support\Collection
     */
    public function disable2fa($currentPassword)
    {
        /** @var LoginSecurity $loginSecurity */
        $loginSecurity = $this->getUser()->loginSecurity()->firstOrCreate();

        if (! $loginSecurity->google2fa_enabled) {
            $this->throwClientError('2fa', 'Google 2fa currently not enabled');
        }

        if (! (Hash::check($currentPassword, $this->getUser()->password))) {
            $this->throwClientError(
                'current_password',
                'Your password does not matches with your account password. Please try again.'
            );
        }

        $loginSecurity->google2fa_enabled = false;
        $loginSecurity->save();

        $this->serviceResult->put('success', true);
        $this->serviceResult->put('message', '2FA has been successfully disabled');

        return $this->serviceResult;
    }
}
