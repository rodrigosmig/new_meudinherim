@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">{{ $entry->invoice->card->name }}</li>
        <li class="breadcrumb-item">{{ __('global.invoices') }}</li>
        <li class="breadcrumb-item">{{ __('global.entry') }}</li>
        <li class="breadcrumb-item active">{{ __('global.edit') }}</li>
    </ol>
@endsection

@push('js')
    <script src="{{ asset('js/plugins/bootstrap-switch.min.js') }}"></script>
@endpush

@section('js')
    <script>
        var installments_number = '{{ __('global.installments_number') }}';
        var installment_value = '{{ __('global.installment_value') }}';
        var number_installments = '{{ __('global.number_installments') }}';

        var monthly = "{{ $entry->monthly }}";

        if (monthly === '1') {
            $("#invoice_entry-monthly").bootstrapSwitch('state', true)
        } else {
            $("#invoice_entry-monthly").bootstrapSwitch('state', false)
        }

        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    </script>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('invoice_entries.update', $entry->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="card-body">
                @include('invoice_entries.partials.form')
            </div>        
            <div class="card-footer">
                <a href="{{ route('invoice_entries.index', [$entry->invoice->card->id, $entry->invoice->id]) }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('global.submit') }}</button>
            </div>
        </form>
    </div>    
@stop
