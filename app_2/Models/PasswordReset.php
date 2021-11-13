<?php

namespace App\Models;

use App\Traits\UsedAtTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\PasswordReset.
 *
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereToken($value)
 * @mixin \Eloquent
 * @property string|null $used_at
 * @property \Illuminate\Support\Carbon|null $valid_to
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereValidTo($value)
 * @property int $id
 * @property int|null $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUserId($value)
 */
class PasswordReset extends Model
{
    use HasFactory;
    use UsedAtTrait;

    const UPDATED_AT = null;

    protected $fillable = [
        'valid_to',
        'user_id',
        'used_at',
        'token',
        'email',
    ];
    public $casts = [
        'valid_to' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
