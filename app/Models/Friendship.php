<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'status'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBetweenUsers($query, $sender, $recipient) // camel case query necesario para el scope
    {
        $query->where([
            ['sender_id', $sender->id],
            ['recipient_id', $recipient->id]
        ])->orWhere([
            ['sender_id', $recipient->id],
            ['recipient_id', $sender->id]
        ]);
    }
}
