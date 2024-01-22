@extends('webapp::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('webapp.name') !!}</p>
@endsection
