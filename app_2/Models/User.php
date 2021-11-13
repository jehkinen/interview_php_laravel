<?php

namespace App\Models;

use App\Traits\HasShortName;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\User.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property string|null $first_name
 * @property string|null $last_name
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @property-read \App\Models\LoginSecurity|null $loginSecurity
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PasswordReset[] $passwordResets
 * @property-read int|null $password_resets_count
 * @property-read mixed $two_factor_enabled
 * @property string|null $registration_source
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegistrationSource($value)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasShortName, HasRoles;

    protected $with = [
        'loginSecurity',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param $newPassword
     */
    public function setNewPassword($newPassword)
    {
        $this->password = Hash::make($newPassword);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @param $searchString
     */
    public static function ajaxMultiselectSearchQuery($searchString)
    {
        return self::query()
            ->selectRaw('users.*, concat(first_name, " " , last_name) as full_name')
            ->where('first_name', 'like', '%' . $searchString . '%')
            ->orWhere('last_name', 'like', '%' . $searchString . '%');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function loginSecurity(): HasOne
    {
        return $this->hasOne(LoginSecurity::class);
    }

    /**
     * @return HasMany
     */
    public function passwordResets() : HasMany
    {
        return $this->hasMany(PasswordReset::class, 'user_id');
    }

    public function getTwoFactorEnabledAttribute()
    {
        return $this->loginSecurity ? $this->loginSecurity->google2fa_enabled : false;
    }

    /**
     * @return HasOne
     */
    public function player()
    {
        return $this->hasOne(Player::class, 'user_id');
    }
}
