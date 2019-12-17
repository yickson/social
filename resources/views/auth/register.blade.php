@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                @include('partials.validation-errors')
                <div class="card border-0 bg-light px-4 py-2">
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Username:</label>
                                <input class="form-control border-0" type="text" name="name" placeholder="Tu nombre de usuario..." value="{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input class="form-control border-0" type="text" name="first_name" placeholder="Tu nombre..."value="{{ old('first_name') }}">
                            </div>
                            <div class="form-group">
                                <label>Apellido:</label>
                                <input class="form-control border-0" type="text" name="last_name" placeholder="Tu apellido..." value="{{ old('last_name') }}">
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input class="form-control border-0" type="email" name="email" placeholder="Tu email..." value="{{ old('email') }}">
                            </div>
                            <div class="form-group">
                                <label>Contraseña:</label>
                                <input class="form-control border-0" type="password" name="password" placeholder="Tu contraseña...">
                            </div>
                            <div class="form-group">
                                <label>Repite la contraseña:</label>
                                <input class="form-control border-0" type="password" name="password_confirmation" placeholder="Repite tu contraseña...">
                            </div>
                            <button class="btn btn-primary btn-block" dusk="register-btn">Registro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
