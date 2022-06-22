<?php

namespace App\Models;

use App\Traits\HasLikes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, HasLikes;

    protected $fillable = [
        'user_id',
        'status_id',
        'body'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
