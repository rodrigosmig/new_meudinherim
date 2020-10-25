@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/invoice_entries/index.js') }}"></script>
@endpush

@section('js')
    <script>
        var entry_title = '{{ __('messages.ajax_title') }}';
        var entry_text = '{{ __('messages.entries.ajax_text') }}';
        var button_cancel = '{{ __('global.cancel') }}';
        var button_confirm = '{{ __('global.confirm') }}';
    </script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()

            $('.datatable').DataTable({
                "language": {
                    "url": "{{ asset('js/plugins/datatable-portuguese.json') }}"
                }
            });
        })
    </script>
@stop

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">{{ $invoice->card->name }}</li>
        <li class="breadcrumb-item"><a href="{{ route('cards.invoices.index', $invoice->card_id) }}">{{ __('global.invoices') }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.entries') }}</li>
    </ol>
@endsection

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>
                <i class="far fa-calendar-alt"></i> {{ toBrDate($invoice->due_date) }}
                <span class="float-right" style="color: red">{{ toBrMoney($invoice->amount) }}</span>
            </h4>
        </div>
        <div class="card-body">        
            @if ($entries->isNotEmpty())
                <table class="table datatable">
                    <thead>
                        <th>{{ __('global.date') }}</th>
                        <th>{{ __('global.category') }}</th>
                        <th>{{ __('global.description') }}</th>
                        <th>{{ __('global.value') }}</th>
                        @if (! $invoice->isPaid())
                            <th>{{ __('global.actions') }}</th>
                        @endif
                    </thead>
                    <tbody>
                        @foreach ($entries as $entry)
                            <tr>
                                <td>{{ toBrDate($entry->date) }}</td>
                                <td>{{ $entry->category->name }}</td>
                                <td>{{ $entry->description }}</td>
                                @if ($entry->isExpenseCategory())
                                    <td style="color: red">
                                        {{ toBrMoney($entry->value) }}
                                    </td>
                                @else
                                    <td style="color: blue">
                                        {{ toBrMoney($entry->value) }}
                                    </td>
                                @endif
                                @if (! $invoice->isPaid())
                                    <td class="table-actions">
                                        <div class="row">
                                            <a class="btn btn-info btn-sm edit" href="{{ route('invoice_entries.edit', $entry->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm delete" data-entry="{{ $entry->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h5 style="margin-top:20px">{{__("messages.entries.not_found")}}</h5>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('cards.invoices.index', $invoice->card_id) }}" class="btn btn-outline-dark">{{ __('global.return') }}</a>
        </div>
    </div>    
@stop
