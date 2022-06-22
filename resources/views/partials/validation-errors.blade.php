@if($errors->any())
    <div class="alert alert-danger" dusk="errors">
        @foreach($errors->all() as $error)
            {{ $error }} <br>
        @endforeach
    </div>
@endif
