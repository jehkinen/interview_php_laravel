<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\EventNote.
 *
 * @property int $id
 * @property int $event_id
 * @property string|null $public_note
 * @property string|null $private_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNote whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNote wherePrivateNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNote wherePublicNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventNote whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Database\Factories\EventNoteFactory factory(...$parameters)
 * @property-read \App\Models\Event $event
 */
class EventNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'private_note',
        'public_note',
    ];

    /**
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
