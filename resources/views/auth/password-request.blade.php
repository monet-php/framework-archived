@extends('monet::layouts.auth')

@section('title')
    Forgot password
@endsection

@section('content')
    <div>
        @livewire('monet::password-request')
    </div>
@endsection
