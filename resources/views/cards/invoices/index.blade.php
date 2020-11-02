@extends('layouts.app')

@section('js')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@stop

@push('js')
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datetime-moment.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datatable.js') }}"></script>
@endpush


@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">{{ $card->name }}</li>
        <li class="breadcrumb-item active">{{ __('global.invoices') }}</li>
    </ol>
@endsection

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">
        <div class="card-header d-flex p-0">
            <h3 class="card-title p-3">{{ __('global.limit') }}: <span style="color: blue">{{ toBrMoney($card->balance) }}</span></h3>
            <ul class="nav nav-pills ml-auto p-2">
                <li class="nav-item"><a class="nav-link active" href="#nav-opens" data-toggle="tab">{{__("global.opens")}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#nav-paids" data-toggle="tab">{{__("global.paids")}}</a></li>
                
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="nav-opens">
                    @if ($open_invoices->isNotEmpty())
                        <table class="table datatable">
                            <thead>
                                <th>{{ __('global.due_date') }}</th>
                                <th>{{ __('global.closing_date') }}</th>
                                @if (! isset($card))
                                    <th>{{ __('global.card') }}</th>
                                @endif
                                <th>{{ __('global.amount') }}</th>
                                <th>{{ __('global.actions') }}</th>
                            </thead>
                            <tbody>
                                @foreach ($open_invoices as $invoice)
                                    <tr>
                                        <td>{{ toBrDate($invoice->due_date) }}</td>
                                        <td>{{ toBrDate($invoice->closing_date) }}</td>
                                        @if (! isset($card))
                                            <td>{{ $invoice->card->name }}</td>
                                        @endif
                                        <td style="color: red">{{ toBrMoney($invoice->amount) }}</td>
                                        <td>
                                            <a class="btn btn-success btn-sm edit" href="{{ route('invoice_entries.index', [$invoice->id, $invoice->card->id]) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.view_entries') }}">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                            @if ($invoice->isClosed() && ! $invoice->payable)
                                                <a class="btn btn-info btn-sm" href="{{ route('cards.invoices.generate-payment', [$invoice->id, $invoice->card->id]) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.generate_payment') }}">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </a>
                                            @endif
                                            @if ($invoice->payable && ! $invoice->isPaid())
                                                <a class="btn btn-success btn-sm edit" href="{{ route('payables.show', $invoice->payable->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.pay') }}">
                                                    <i class="fas fa-money-bill-alt"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h5 style="margin-top:20px">{{__("messages.invoices.not_found")}}</h5>
                    @endif
                </div>
                <div class="tab-pane" id="nav-paids">
                    @if ($paid_invoices->isNotEmpty())
                        <table class="table datatable">
                            <thead>
                                <th>{{ __('global.due_date') }}</th>
                                <th>{{ __('global.closing_date') }}</th>
                                @if (! isset($card))
                                    <th>{{ __('global.card') }}</th>
                                @endif
                                <th>{{ __('global.amount') }}</th>
                                <th>{{ __('global.actions') }}</th>
                            </thead>
                            <tbody>
                                @foreach ($paid_invoices as $invoice)
                                    <tr>
                                        <td>{{ toBrDate($invoice->due_date) }}</td>
                                        <td>{{ toBrDate($invoice->closing_date) }}</td>
                                        @if (! isset($card))
                                            <td>{{ $invoice->card->name }}</td>
                                        @endif
                                        <td style="color: red">{{ toBrMoney($invoice->amount) }}</td>
                                        <td>
                                            <a class="btn btn-success btn-sm edit" href="{{ route('invoice_entries.index', [$invoice->id, $invoice->card->id]) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.view_entries') }}">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                            @if ($invoice->isClosed() && ! $invoice->payable)
                                                <a class="btn btn-info btn-sm" href="{{ route('cards.invoices.generate-payment', [$invoice->id, $invoice->card->id]) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.generate_payment') }}">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </a>
                                            @endif
                                            @if ($invoice->payable && ! $invoice->isPaid())
                                                <a class="btn btn-success btn-sm edit" href="{{ route('payables.show', $invoice->payable->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.pay') }}">
                                                    <i class="fas fa-money-bill-alt"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h5 style="margin-top:20px">{{__("messages.invoices.not_found")}}</h5>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
@stop
