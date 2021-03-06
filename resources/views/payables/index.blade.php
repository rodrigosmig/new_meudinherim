@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/payables/index.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datetime-moment.js') }}"></script>
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
        var parcel_title = '{{ __('messages.parcel_title') }}';
        var parcel_text = '{{ __('messages.account_scheduling.delete_parcel') }}';
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

            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <th>{{ __('global.due_date') }}</th>
                        <th>{{ __('global.paid_date') }}</th>
                        <th>{{ __('global.description') }}</th>
                        <th>{{ __('global.category') }}</th>
                        <th>{{ __('global.value') }}</th>
                        <th>{{ __('global.monthly') }}</th>
                        <th>{{ __('global.paid') }}</th>
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
                                        @if (isset($payable->parcelable) && $payable->parcelable)
                                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Total: {{ toBrMoney($payable->parcelable->value) }}" 
                                                style="text-decoration: none; color: inherit">
                                                {{ $payable->description }}
                                            </a>
                                        @else
                                            {{ $payable->description }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{ $payable->category->name }}
                                </td>
                                <td>
                                    {{ toBrMoney($payable->value) }}
                                </td>
                                <td>
                                    @if ($payable->monthly)
                                        {{ __('global.yes') }}
                                    @else
                                        {{ __('global.no') }}
                                    @endif
                                    
                                </td>
                                <td>
                                    @if ($payable->isPaid())
                                        <i class="fas fa-check" style="color: green"></i>
                                    @else
                                        <i class="fas fa-times" style="color: red"></i>
                                    @endif
                                </td>
                                <td class="table-actions">
                                    <div class="row">
                                        @if (! $payable->isPaid())
                                            @if (! isset($payable->parcelable))
                                                <a class="btn btn-info btn-sm edit" href="{{ route('payables.edit', $payable->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                            @endif

                                            @if (! isset($payable->parcelable))
                                                <button class="btn btn-danger btn-sm delete" data-payable="{{ $payable->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                                <a class="btn btn-success btn-sm" href="{{ route('payables.show', [$payable->id]) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.pay') }}">
                                                    <i class="fas fa-money-bill-alt"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-danger btn-sm delete-parcels" data-payable="{{ $payable->parcelable_id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                                <a class="btn btn-success btn-sm" href="{{ route('payables.show', [$payable->id, "parcelable_id" => $payable->parcelable_id]) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.pay') }}">
                                                    <i class="fas fa-money-bill-alt"></i>
                                                </a>
                                            @endif
                                        @else
                                            <button class="btn btn-danger btn-sm cancel_payment" data-payable="{{ $payable->id }}" data-parcelable="{{ isset($payable->parcelable_id) ? $payable->parcelable_id : '' }}" data-toggle="tooltip"  data-placement="top" title="{{ __('global.cancel_payment') }}">
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
    </div>
@stop
