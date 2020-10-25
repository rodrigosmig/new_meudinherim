@extends('adminlte::page')

@section('title', $title)

@section('content_top_nav_right')
    @include('includes.add_menu')
    @include('includes.balance_menu')
@stop

@push('css')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endpush

@push('js')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
@endpush

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $title }}</h1>
            </div>
            <div class="col-sm-6">
                @yield('button-header')
            </div>
        </div>
    </div>

    @include('sweetalert::alert')
@stop

@section('footer')
    
@stop

