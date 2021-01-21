@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('receivables.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.receivement') }}</li>
    </ol>
@endsection

@section('js')
    <script>
        var receivable_title = '{{ __('messages.ajax_title') }}';
        var receivable_text = '{{ __('messages.account_scheduling.ajax_text') }}';
        var button_cancel = '{{ __('global.cancel') }}';
        var button_confirm = '{{ __('global.confirm') }}';
    </script>
    <script src="{{ asset('js/receivables/show.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datepicker.js') }}"></script>
@stop

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">

        <div class="card-header">
            <h4>
                {{ __('global.receivement') }}
                @if ($receivable->isPaid())
                    - <span style="color: green">{{ __('global.received') }}</span>
                @endif
                <span class="float-right">
                    @if ($receivable->isPaid())
                        <button class="btn btn-danger btn-sm cancel_receivement" data-receivable="{{ $receivable->id }}" data-toggle="tooltip" title="{{ __('global.cancel_receivement') }}">
                            <i class="fas fa-ban"></i>
                        </button>
                    @else
                        <a class="btn btn-success btn-sm" href="{{ route('receivables.edit', $receivable->id) }}" title="{{ __('global.edit') }}"><i class="fas fa-edit"></i> {{ __('global.edit') }}</a>
                    @endif                    
                </span>
            </h4>
        </div>

        <form action="{{ route('receivables.receivement', $receivable->id) }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label for="receivable-accounts" class="col-sm-2 col-form-label">{{ __('global.account') }}</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="account_id" {{ $receivable->isPaid() ? 'disabled' : 'required' }}>
                            @if ($receivable->isPaid())
                                <option >{{ $receivable->accountEntry->account->name }}</option>
                            @else
                                <option value="">{{ __('global.select_accounts') }}</option>
                                @foreach ($form_accounts as $key => $account)
                                    <option value="{{ $key }}">{{ $account }}</option>
                                @endforeach
                            @endif                            
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="receivable-paid_date" class="col-sm-2 col-form-label">{{ __('global.paid_date') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="receivable-paid_date" class="form-control datepicker" name="paid_date" value="{{ $receivable->paid_date ? toBrDate($receivable->paid_date) : '' }}" {{ $receivable->isPaid() ? 'disabled' : 'required' }}>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="receivable-category" class="col-sm-2 col-form-label">{{ __('global.category') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="receivable-category" class="form-control" value="{{ $receivable->category->name }}" disabled>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="receivable-due_date" class="col-sm-2 col-form-label">{{ __('global.due_date') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="receivable-due_date" class="form-control" name="due_date" value="{{ toBrDate($receivable->due_date) }}" disabled>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="receivable-description" class="col-sm-2 col-form-label">{{ __('global.description') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="receivable-description" class="form-control" name="description" value="{{ $receivable->description }}" disabled>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="receivable-value" class="col-sm-2 col-form-label">{{ __('global.value') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="receivable-value" class="form-control" value="{{ toBrMoney($receivable->value) }}" disabled>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="payable-monthly" class="col-sm-2 col-form-label">{{ __('global.monthly') }}</label>
                    <div class="col-sm-10">
                        @if ($receivable->monthly)
                            {{ __('global.yes') }}
                        @else
                            {{ __('global.no') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('receivables.index') }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                @if (! $receivable->isPaid())
                    <button class="btn btn-primary" type="submit">{{ __('global.receive') }}</button>
                @endif
            </div>            
        </form>
    </div>
@stop