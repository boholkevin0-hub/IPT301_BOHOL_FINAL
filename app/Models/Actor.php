<?php

namespace App\Models;

use Database\Factories\ActorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Actor extends Model
{
    /** @use HasFactory<ActorFactory> */
    use HasFactory;

    protected $fillable = ['movie_id', 'name', 'character_name', 'nationality', 'date_of_birth'];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date'];
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
