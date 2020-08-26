@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/cards/index.js') }}"></script>
@endpush

@section('js')
    <script>
        var card_title = '{{ __('messages.ajax_title') }}';
        var card_text = '{{ __('messages.cards.ajax_text') }}';
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
    <a href="{{ route('cards.create') }}" class="float-sm-right btn btn-sm btn-success shadow-sm"><i class="fas fa-plus"></i> {{__("global.add")}}</a>
@endsection

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
<div class="card">
    <div class="card-body">        
        @if ($cards->isNotEmpty())
            <table class="table datatable">
                <thead>
                    <th>{{ __('global.name') }}</th>
                    <th>{{ __('global.credit_limit') }}</th>
                    <th>{{ __('global.available_limit') }}</th>
                    <th>{{ __('global.closing_day') }}</th>
                    <th>{{ __('global.pay_day') }}</th>
                    <th>{{ __('global.actions') }}</th>
                </thead>
                <tbody>
                    @foreach ($cards as $card)
                        <tr>
                            <td>{{ $card->name }}</td>
                            <td>{{ toBrMoney($card->credit_limit) }}</td>
                            <td>{{ toBrMoney($card->balance) }}</td>
                            <td>{{ $card->closing_day }}</td>
                            <td>{{ $card->pay_day }}</td>
                            <td class="table-actions">
                                <div class="row">
                                    <a class="btn btn-info btn-sm edit" href="{{ route('cards.edit', $card->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete" data-card="{{ $card->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <a class="btn btn-success btn-sm invoices" href="{{ route('cards.invoices.index', $card->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.invoices') }}">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach 
                </tbody>
            </table>
        @else
            <h5 style="margin-top:20px">{{__("messages.cards.not_found")}}</h5>
        @endif
    </div>
</div>
    
@stop
