@extends('monet::layouts.auth')

@section('title')
    Email verification
@endsection

@section('content')
    <div>
        @livewire('monet::email-verification')
    </div>
@endsection
