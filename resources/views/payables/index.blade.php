@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/payables/index.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datatable.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datepicker_range.js') }}"></script>
@endpush

@section('button-header')
    <a href="{{ route('payables.create') }}" class="float-sm-right btn btn-sm btn-success shadow-sm"><i class="fas fa-plus"></i> {{__("global.add")}}</a>
@endsection

@section('js')
    <script>
        var payable_title = '{{ __('messages.ajax_title') }}';
        var payable_text = '{{ __('messages.account_scheduling.ajax_text') }}';
        var button_cancel = '{{ __('global.cancel') }}';
        var button_confirm = '{{ __('global.confirm') }}';
    </script>
@stop

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">
        <div class="card-body">
            <label>{{ __('global.filter_by_range') }}:</label>
            <div class="form-inline">
                <form action="{{ route('payables.filter') }}" method="POST">
                    @include('includes.form_filter')
                </form>
            </div>

            <table class="table datatable">
                <thead>
                    <th>{{ __('global.due_date') }}</th>
                    <th>{{ __('global.paid_date') }}</th>
                    <th>{{ __('global.description') }}</th>
                    <th>{{ __('global.category') }}</th>
                    <th>{{ __('global.value') }}</th>
                    <th>{{ __('global.status') }}</th>
                    <th>{{ __('global.actions') }}</th>
                </thead>
                <tbody>
                    @foreach ($payables as $payable)
                        <tr>
                            <td>
                                {{ toBrDate($payable->due_date) }}
                            </td>
                            <td>
                                @if ($payable->isPaid())
                                    {{ toBrDate($payable->paid_date) }}
                                @endif
                            </td>
                            <td>
                                @if ($payable->invoice)
                                    <a href="{{ route('invoice_entries.index', [$payable->invoice->card->id, $payable->invoice->id]) }}" title="{{ __('global.show_invoice') }}">
                                        {{ $payable->description }}
                                    </a>
                                @else
                                    {{ $payable->description }}
                                @endif
                            </td>
                            <td>
                                {{ $payable->category->name }}
                            </td>
                            <td>
                                {{ toBrMoney($payable->value) }}
                            </td>
                            <td>
                                @if ($payable->isPaid())
                                    <span style="color: green">{{ __('global.paid') }}</span>
                                @else
                                    <span style="color: red">{{ __('global.open') }}</span>
                                @endif
                            </td>
                            <td class="table-actions">
                                <div class="row">
                                    @if (! $payable->isPaid())
                                        <a class="btn btn-info btn-sm edit" href="{{ route('payables.edit', $payable->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm delete" data-payable="{{ $payable->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <a class="btn btn-success btn-sm edit" href="{{ route('payables.show', $payable->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.pay') }}">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-danger btn-sm cancel_payment" data-payable="{{ $payable->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.cancel_payment') }}">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
