<?php

namespace App\Traits;

use App\Events\ModelLiked;
use App\Events\ModelUnliked;
use App\Models\Like;
use Illuminate\Support\Str;

trait HasLikes
{
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        $this->likes()->firstOrCreate([
            'user_id' => auth()->id()
        ]);

        ModelLiked::dispatch($this, auth()->user()); // this para que envie el modelo, y el usuario autenticado que envió el like
    }

    public function unlike()
    {
        //dentro de los likes de este estado, buscar el que tenga el id del usuario autenticado y lo va a quitar
        $this->likes()->where([
            'user_id' => auth()->id()
        ])->delete();

        ModelUnliked::dispatch($this);
    }

    public function isLiked()
    {
        return $this->likes()->where('user_id', auth()->id())->exists(); //devuelve un boolean, obteniendo los likes del comentario y si existe el del usuario autenticado responde false|true
    }

    public function likesCount()
    {
        return $this->likes()->count();
    }

    public function eventChannelName()
    {
        return strtolower(Str::plural(class_basename($this))) . "." . $this->getKey() . ".likes";
    }

    abstract public function path(); //cualquier clase que utilice el trait HasLikes debe implementar el metodo path()
}
