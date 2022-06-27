<div class="card border-0 bg-light shadow-sm">
    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="card-img-top">
    <div class="card-body">
        @if(auth()->id() === $user->id)
            <h5 class="card-title"><a class="text-decoration-none" href="{{ route('users.show', $user) }}">{{ $user->name }}</a><small class="text-secondary">Eres tú</small></h5>
        @else
            <h5 class="card-title"><a class="text-decoration-none" href="{{ route('users.show', $user) }}">{{ $user->name }}</h5></a>
            <div class="d-grid gap-2">
                <friendship-btn
                    dusk="request-friendship"
                    class="btn btn-primary"
                    :recipient="{{ $user }}"
                ></friendship-btn>
            </div>
        @endif
    </div>
</div>
