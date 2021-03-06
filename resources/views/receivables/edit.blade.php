@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/plugins/bootstrap-switch.min.js') }}"></script>
@endpush

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.edit') }}</li>
    </ol>
@endsection

@section('js')
    <script>
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    </script>

    <script>
        var monthly = "{{ $receivable->monthly }}";

        if (monthly === '1') {
            $("#receivable-monthly").bootstrapSwitch('state', true)
        } else {
            $("#receivable-monthly").bootstrapSwitch('state', false)
        }

        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
    </script>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('receivables.update', $receivable->id) }}" method="post">
            @csrf
            @method('PUT')
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
