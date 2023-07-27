<?php

namespace App\Models;

// Connecting users to Chirps
// Added
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\ChirpCreated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chirp extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
    ];

    // Dispatching the event
    protected $dispatchesEvents = [
        'created' => ChirpCreated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
