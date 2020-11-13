@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('payables.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.new') }}</li>
    </ol>
@endsection

@push('js')
    <script src="{{ asset('js/plugins/bootstrap-switch.min.js') }}"></script>
    <script src="{{ asset('js/payables/create.js') }}"></script>
@endpush

@section('js')
    <script src="{{ asset('js/plugins/init-datepicker.js') }}"></script>

    <script>
        var installments_number = '{{ __('global.installments_number') }}';
        var installment_value = '{{ __('global.installment_value') }}';
        var number_installments = '{{ __('global.number_installments') }}';
        
        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
    </script>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('payables.store') }}" method="POST">
            <div class="card-body">

                @include('payables.partials.form')

            </div>

            <div class="card-footer">
                <a href="{{ route('payables.index') }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('global.submit') }}</button>
            </div>
            
        </form>
    </div>
@stop
