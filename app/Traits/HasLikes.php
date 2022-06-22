<?php

namespace App\Traits;

use App\Models\Like;

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
    }

    public function unlike()
    {
        //dentro de los likes de este estado, buscar el que tenga el id del usuario autenticado y lo va a quitar
        $this->likes()->where([
            'user_id' => auth()->id()
        ])->delete();
    }

    public function isLiked()
    {
        return $this->likes()->where('user_id', auth()->id())->exists(); //devuelve un boolean, obteniendo los likes del comentario y si existe el del usuario autenticado responde false|true
    }

    public function likesCount()
    {
        return $this->likes()->count();
    }
}
