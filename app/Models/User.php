<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['avatar']; // cuando se obtenga al usuario, tambien devolvera la propiedad avatar

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function link()
    {
        return route('users.show', $this);
    }

    public function avatar()
    {
        return '/img/avatar.jpg';
    }

    public function getAvatarAttribute() // metodo para devolver lo que esta en avatar
    {
        return $this->avatar();
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function friendshipRequestsReceived()
    {
        return $this->hasMany(Friendship::class, 'recipient_id');
    }

    public function friendshipRequestsSent()
    {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    public function sendFriendRequestTo($recipient)
    {
        /*return Friendship::query()->firstOrCreate([
            'sender_id' => $this->id,
            'recipient_id' => $recipient->id
        ]);*/
        return $this->friendshipRequestsSent()
            ->firstOrCreate(['recipient_id' => $recipient->id]);
    }

    public function acceptFriendRequestFrom($sender)
    {
        /*$friendship = Friendship::query()->where([
            'sender_id' => $sender->id,
            'recipient_id' => $this->id,
        ])->first();*/
        $friendship = $this->friendshipRequestsReceived()
            ->where(['sender_id' => $sender->id])
            ->first();

        $friendship->update(['status' => 'accepted']);

        return $friendship;
    }

    public function denyFriendRequestFrom($sender)
    {
        /*$friendship = Friendship::query()->where([
            'sender_id' => $sender->id,
            'recipient_id' => $this->id,
        ])->first();*/

        $friendship = $this->friendshipRequestsReceived()
            ->where(['sender_id' => $sender->id])
            ->first();

        $friendship->update(['status' => 'denied']);

        return $friendship;
    }

    public function friends()
    {
        $senderFriends = $this->belongsToMany(User::class, 'friendships', 'sender_id', 'recipient_id')
            ->wherePivot('status', 'accepted')
            ->get();

        $recipientFriends = $this->belongsToMany(User::class, 'friendships', 'recipient_id', 'sender_id')
            ->wherePivot('status', 'accepted')
            ->get();

        return $senderFriends->merge($recipientFriends);
    }
}
