<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'text', 'category'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
