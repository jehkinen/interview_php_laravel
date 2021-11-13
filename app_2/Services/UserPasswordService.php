<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use App\Traits\HasUserTrait;
use App\Models\PasswordReset;
use App\Traits\ValidationErrorTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Notifications\MailResetPasswordToken;

class UserPasswordService
{
    use HasUserTrait;
    use ValidationErrorTrait;

    const PASSWORD = 'hello@dolly';

    /**
     * Request password reset for user.
     * @param string $email
     * @return false|string
     */
    public function resetRequest(string $email)
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return false;
        }

        /** @var PasswordReset $passwordReset */
        $passwordReset = $user->passwordResets()->create([
            'email' => $user->email,
            'token' => Str::random(32),
            'valid_to' => now()->addDay(),
        ]);

        $user->notify(new MailResetPasswordToken($passwordReset->token));

        return true;
    }

    /**
     * @param string $currentPassword
     * @param string $newPassword
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Support\Collection
     */
    public function changePassword(string $currentPassword, string $newPassword)
    {
        if (! (Hash::check($currentPassword, $this->getUser()->password))) {
            $this->throwClientError(
                'current_password',
                'Your password does not matches with your account password. Please try again.'
            );
        }
        if ($currentPassword === $newPassword) {
            $this->throwClientError(
                'new_password',
                'New password must not be the same as an old password.'
            );
        }

        try {
            $this->getUser()->setNewPassword($newPassword);

            $this->getUser()->save();

            return collect([
                'sucess' => true,
                'message' => 'Your password has been successfully changed',
            ]);
        } catch (\Throwable $throwable) {
            $this->throwClientError('current_password', 'Something went wrong, please try again');
        }
    }

    /**
     * @param $token
     * @param $password
     * @throws \Illuminate\Validation\ValidationException
     * @return bool
     */
    public function resetPassword($token, $password): bool
    {
        $passwordReset = PasswordReset::query()
            ->whereNull('used_at')
            ->whereDate('valid_to', '>', now())
            ->where('token', $token)
            ->first();

        if (! $passwordReset) {
            $this->throwClientError('token', 'Password reset token expired or already used, please request another email');
        }

        $user = $passwordReset->user;
        $user->setNewPassword($password);
        $passwordReset->markAsUsed();

        return $user->save();
    }
}
