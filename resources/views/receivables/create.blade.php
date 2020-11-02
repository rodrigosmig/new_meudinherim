@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/plugins/bootstrap-switch.min.js') }}"></script>
@endpush

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('receivables.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.new') }}</li>
    </ol>
@endsection

@section('js')
    <script src="{{ asset('js/plugins/init-datepicker.js') }}"></script>

    <script>
        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
    </script>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('receivables.store') }}" method="POST">
            <div class="card-body">

                @include('receivables.partials.form')

            </div>

            <div class="card-footer">
                <a href="{{ route('receivables.index') }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('global.submit') }}</button>
            </div>
            
        </form>
    </div>
@stop
