@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datetime-moment.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datatable.js') }}"></script>
@endpush

@section('js')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@stop

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">{{ __('global.invoices') }}</li>
    </ol>
@endsection

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills ml-auto p-2">
                <li class="nav-item"><a class="nav-link active" href="#nav-opens" data-toggle="tab">{{__("global.opens")}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#nav-paids" data-toggle="tab">{{__("global.paids")}}</a></li>
                
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="nav-opens">
                    @if ($open_invoices->isNotEmpty())
                        @include('cards.partials.form_invoices')
                    @else
                        <h5 style="margin-top:20px">{{__("messages.invoices.not_found")}}</h5>
                    @endif
                </div>
                <div class="tab-pane" id="nav-paids">
                    @if ($paid_invoices->isNotEmpty())
                        @include('cards.partials.form_invoices')
                    @else
                        <h5 style="margin-top:20px">{{__("messages.invoices.not_found")}}</h5>
                    @endif
                </div>
                
            </div>
        </div>
      </div>
@stop
