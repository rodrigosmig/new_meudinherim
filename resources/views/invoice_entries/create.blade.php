@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">{{ __('global.credit-card') }}</li>
        <li class="breadcrumb-item active">{{ __('global.add_entry') }}</li>
    </ol>
@endsection

@push('js')
    <script src="{{ asset('js/plugins/bootstrap-switch.min.js') }}"></script>
    <script src="{{ asset('js/invoice_entries/create.js') }}"></script>
@endpush

@section('js')
    <script>
        var installments_number = '{{ __('global.installments_number') }}';
        var installment_value = '{{ __('global.installment_value') }}';
        var number_installments = '{{ __('global.number_installments') }}';

        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
        
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    </script>
@endsection

@section('content')
    <div id="app" class="card">
        <form action="{{ route('invoice_entries.store') }}" method="post">
            @csrf
            <div class="card-body">
                @include('invoice_entries.partials.form')
            </div>        
            <div class="card-footer">
                <button class="btn btn-primary" type="submit">{{ __('global.submit') }}</button>
            </div>
        </form>
    </div>    
@stop
