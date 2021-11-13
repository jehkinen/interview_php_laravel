<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\EventNoteHasPlayerAccess.
 *
 * @property int $id
 * @property int $event_id
 * @property int $player_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventNoteHasPlayerAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNoteHasPlayerAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNoteHasPlayerAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNoteHasPlayerAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNoteHasPlayerAccess whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNoteHasPlayerAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNoteHasPlayerAccess wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNoteHasPlayerAccess whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Event $event
 * @property-read \App\Models\Player $player
 */
class EventNoteHasPlayerAccess extends Model
{
    use HasFactory;

    protected $with = [
        'player',
    ];
    protected $fillable = [
        'event_id',
        'player_id',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
