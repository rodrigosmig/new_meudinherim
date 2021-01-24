@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/plugins/init-datepicker_range.js') }}"></script>
    <script src="{{ asset('js/account_entries/index.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datetime-moment.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datatable.js') }}"></script>
@endpush

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">{{ __('global.accounts') }}</li>
        <li class="breadcrumb-item">{{ __('global.extract') }}</li>
        <li class="breadcrumb-item active">{{ $account->name }}</li>
    </ol>
@endsection

@section('js')
    <script>
        var entry_title = '{{ __('messages.ajax_title') }}';
        var entry_text = '{{ __('messages.entries.ajax_text') }}';
        var button_cancel = '{{ __('global.cancel') }}';
        var button_confirm = '{{ __('global.confirm') }}';
    </script>
@stop

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="float-left">{{ $account->name }}</h4>
            <h4>
                <span class="float-right">{{ __('global.balance') }}: {{ toBrMoney($account->balance) }}</span>
            </h4>
        </div>
        <div class="card-body">
            <label>{{ __('global.filter_by_range') }}:</label>
            <div class="form-inline">
                <form action="{{ route('accounts.entries', $account->id) }}" method="POST">
                    @csrf
                    <div class="table-margin-bottom">    
                        <input id="filter_from" class="form-control" type="text" name="filter_from" placeholder="{{ __('global.initial_date') }}" value="{{ isset($filter['from']) ? $filter['from'] : '' }}">
                        <input id="filter_to" class="form-control" type="text" name="filter_to" placeholder="{{ __('global.final_date') }}" value="{{ isset($filter['to']) ? $filter['to'] : '' }}">
                        <button type="submit" class="btn btn-primary waves-effect">
                            {{ __('global.filter') }}
                        </button>
                    </div>
                </form>
            </div>

            @if ($entries->isNotEmpty())
                <table class="table datatable table-margin-top">
                    <thead>
                        <th>{{ __('global.date') }}</th>
                        <th>{{ __('global.description') }}</th>
                        <th>{{ __('global.category') }}</th>
                        <th>{{ __('global.value') }}</th>
                        <th>{{ __('global.actions') }}</th>
                    </thead>
                    <tbody>
                        @foreach ($entries as $entry)
                            <tr>
                                <td>{{ toBrDate($entry->date) }}</td>
                                <td>{{ $entry->description }}</td>
                                <td>{{ $entry->category->name }}</td>
                                @if ($entry->isExpenseCategory())
                                    <td style="color: red">{{ toBrMoney($entry->value) }}</td>
                                @else
                                    <td style="color: blue">{{ toBrMoney($entry->value) }}</td>
                                @endif
                                
                                <td class="table-actions">
                                    <div class="row">
                                        @if ($entry->isPayable())
                                            <a href="{{ route('payables.show', $entry->accountScheduling->id) }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('global.show_payment') }}">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        @elseif($entry->isReceivable())
                                            <a href="{{ route('receivables.show', $entry->accountScheduling->id) }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('global.show_receivement') }}">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        @else
                                            <a class="btn btn-info btn-sm edit" href="{{ route('account_entries.edit', $entry->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm delete" data-entry="{{ $entry->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif


                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h5 style="margin-top:20px">{{__("messages.entries.not_found")}}</h5>
            @endif
        </div>
    </div>
@stop