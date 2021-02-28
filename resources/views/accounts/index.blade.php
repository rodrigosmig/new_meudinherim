@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/accounts/index.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datetime-moment.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datatable.js') }}"></script>
@endpush

@section('js')
    <script>
        var account_title = '{{ __('messages.ajax_title') }}';
        var account_text = '{{ __('messages.accounts.ajax_text') }}';
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
    <a href="{{ route('accounts.create') }}" class="float-sm-right btn btn-sm btn-success shadow-sm"><i class="fas fa-plus"></i> {{__("global.add")}}</a>
@endsection

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
<div class="card">
    <div class="card-body">        
        @if ($accounts->isNotEmpty())
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <th>{{ __('global.name') }}</th>
                        <th>{{ __('global.type') }}</th>
                        <th>{{ __('global.actions') }}</th>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            <tr>
                                <td>{{ $account->name }}</td>
                                <td>{{ toCategoryType($account->type) }}</td>
                                <td class="table-actions">
                                    <div class="row">
                                        <a class="btn btn-info btn-sm edit" href="{{ route('accounts.edit', $account->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm delete-account" data-account="{{ $account->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach 
                    </tbody>
                </table>
            </div>
        @else
            <h5 style="margin-top:20px">{{__("messages.accounts.not_found")}}</h5>
        @endif
    </div>
</div>
    
@stop
