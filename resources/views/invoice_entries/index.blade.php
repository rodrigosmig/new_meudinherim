@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/invoice_entries/index.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datetime-moment.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datatable.js') }}"></script>
@endpush

@section('js')
    <script>
        var entry_title = '{{ __('messages.ajax_title') }}';
        var entry_text = '{{ __('messages.entries.ajax_text') }}';
        var parcel_title = '{{ __('messages.parcel_title') }}';
        var parcel_text = '{{ __('messages.entries.delete_parcel') }}';
        var button_cancel = '{{ __('global.cancel') }}';
        var button_confirm = '{{ __('global.confirm') }}';
    </script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
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
    {{-- Modal Anticipate --}}
    <div class="modal fade" id="modal-anticipate" tabindex="-1" role="dialog" aria-labelledby="anticipateLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('global.anticipate_parcels') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-anticipate" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div id="loading">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                <span class="sr-only"></span>
                                </div>
                            </div>
                        </div>
    
                        <h4 id="no-entries"></h4>
    
                        <div class="table-responsive">
                            <table id="table-parcels" class="table table-striped" style="display: none">
                                <caption>
                                    {{ __("global.amount") }}: <span id="table-caption"></span><br />
                                    {{ __("global.remaining") }}: <span id="table-remaining">
                                </caption>
    
                                <thead>
                                    <th>{{ __('global.parcel_number') }}</th>
                                    <th>{{ __('global.date') }}</th>
                                    <th>{{ __('global.description') }}</th>
                                    <th>{{ __('global.value') }}</th>
                                    <th>{{ __('global.anticipate') }}</th>
                                </thead>
    
                                <tbody id="table-body">
    
                                </tbody>
                                                        
                            </table>
                        </div>                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-anticipate">{{ __('global.submit') }}</button>
                    </div>                    
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>
                <i class="far fa-calendar-alt"></i> {{ toBrDate($invoice->due_date) }}
                @if ($invoice->amount < 0)
                    <span class="float-right" style="color: blue">{{ toBrMoney(($invoice->amount * -1)) }}</span>
                @else
                    <span class="float-right" style="color: red">{{ toBrMoney($invoice->amount) }}</span>
                @endif
            </h4>
        </div>
        <div class="card-body">        
            @if ($entries->isNotEmpty())
                <div class="table-responsive">
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

                                    @if (isset($entry->parcelable) && $entry->parcelable)
                                        <td>
                                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Total: {{ toBrMoney($entry->parcelable->value) }}" 
                                                style="text-decoration: none; color: inherit">
                                                {{ $entry->description }}
                                            </a>
                                        </td>
                                    @else
                                        <td>{{ $entry->description }}</td>
                                    @endif

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
                                                @if (!isset($entry->parcelable) && !$entry->anticipated)
                                                    <a class="btn btn-info btn-sm edit" href="{{ route('invoice_entries.edit', $entry->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                @endif

                                                @if (!isset($entry->parcelable) && !$entry->anticipated)
                                                    <button class="btn btn-danger btn-sm delete" data-entry="{{ $entry->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    @if($entry->parcelable && $entry->parcel_number === 1 && !$entry->invoice->isPaid() && !$entry->anticipated)
                                                        <button class="btn btn-danger btn-sm delete-parcels" data-entry="{{ $entry->parcelable_id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif

                                                    @if($entry->parcelable && $entry->parcel_number < $entry->parcel_total && !$entry->invoice->isPaid() && !$entry->anticipated)
                                                        <button class="btn btn-success btn-sm anticipate-parcels" 
                                                            data-placement="top" 
                                                            data-toggle="modal" data-target="#modal-anticipate"
                                                            title="{{ __('global.anticipate_parcels') }}"
                                                            data-entry_id="{{ $entry->parcelable_id }}"
                                                            data-card_id={{ $entry->invoice->card->id}}
                                                            data-parcel_number={{ $entry->parcel_number }}
                                                        >
                                                            <i class="fas fa-clock"></i>
                                                        </button>
                                                    @endif      
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>                
            @else
                <h5 style="margin-top:20px">{{__("messages.entries.not_found")}}</h5>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('cards.invoices.index', $invoice->card_id) }}" class="btn btn-outline-dark">{{ __('global.return') }}</a>
        </div>
    </div>    
@stop
