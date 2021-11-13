<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\EventHasCharacter.
 *
 * @property int $id
 * @property int $event_id
 * @property int $character_id
 * @property int $player_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Character $character
 * @property-read \App\Models\Event $event
 * @property-read \App\Models\Event $player
 * @method static \Database\Factories\EventHasCharacterFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasCharacter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasCharacter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasCharacter query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasCharacter whereCharacterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasCharacter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasCharacter whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasCharacter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasCharacter wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasCharacter whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EventHasCharacter extends Model
{
    use HasFactory;

    public $fillable = [
        'event_id',
        'character_id',
        'player_id',
    ];

    /**
     * @return BelongsTo
     */
    public function event()  : BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * @return BelongsTo
     */
    public function character() : BelongsTo
    {
        return $this->belongsTo(Character::class, 'character_id');
    }

    /**
     * @return BelongsTo
     */
    public function player() : BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}
