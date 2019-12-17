@if($errors->any())
    <div class="alert alert-danger" dusk="validation-errors">
        @foreach($errors->all() as $error)
            {{ $error }} <br>
        @endforeach
    </div>
@endif