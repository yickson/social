@extends('layouts.app')

@section('content')
    <div class="container">
        <status-list-item
            :status="{{ json_encode($status) }}"
        ></status-list-item>
    </div>
@endsection
