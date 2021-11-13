<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\LoginSecurity.
 *
 * @property int $id
 * @property int $user_id
 * @property int $google2fa_enabled
 * @property string|null $google2fa_secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity whereGoogle2faEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity whereGoogle2faSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|LoginSecurity whereGoogle2faEnabled($value)
 */
class LoginSecurity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    protected $casts = [
        'google2fa_enabled' => 'boolean',
    ];
    protected $attributes = [
        'google2fa_enabled' => false,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
