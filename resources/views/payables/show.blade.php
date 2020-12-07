@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('payables.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.payment') }}</li>
    </ol>
@endsection

@section('js')
    <script>
        var payable_title = '{{ __('messages.ajax_title') }}';
        var payable_text = '{{ __('messages.account_scheduling.ajax_text') }}';
        var button_cancel = '{{ __('global.cancel') }}';
        var button_confirm = '{{ __('global.confirm') }}';
    </script>
    <script src="{{ asset('js/payables/show.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datepicker.js') }}"></script>
@stop

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">

        <div class="card-header">
            <h4>
                {{ __('global.payment') }}
                @if ($payable->isPaid())
                    - <span style="color: red">{{ __('global.paid') }}</span>
                @endif
                <span class="float-right">
                    @if ($payable->isPaid())
                        <button class="btn btn-danger btn-sm cancel_payment" data-payable="{{ $payable->id }}" data-toggle="tooltip" data-target="#payment" title="{{ __('global.cancel_payment') }}">
                            <i class="fas fa-ban"></i>
                        </button>
                    @else
                        <a class="btn btn-success btn-sm" href="{{ route('payables.edit', $payable->id) }}" title="{{ __('global.edit') }}"><i class="fas fa-edit"></i> {{ __('global.edit') }}</a>
                    @endif                    
                </span>
            </h4>
        </div>

        <form action="{{ route('payables.payment', $payable->id) }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label for="payable-accounts" class="col-sm-2 col-form-label">{{ __('global.account') }}</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="account_id" {{ $payable->isPaid() ? 'disabled' : 'required' }}>
                            @if ($payable->isPaid())
                                <option >{{ $payable->accountEntry->account->name }}</option>
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
                    <label for="payable-paid_date" class="col-sm-2 col-form-label">{{ __('global.paid_date') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="payable-paid_date" class="form-control datepicker" name="paid_date" value="{{ $payable->paid_date ? toBrDate($payable->paid_date) : '' }}" {{ $payable->isPaid() ? 'disabled' : 'required' }}>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="payable-category" class="col-sm-2 col-form-label">{{ __('global.category') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="payable-category" class="form-control" value="{{ $payable->category->name }}" disabled>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="payable-due_date" class="col-sm-2 col-form-label">{{ __('global.due_date') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="payable-due_date" class="form-control" name="due_date" value="{{ toBrDate($payable->due_date) }}" disabled>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="payable-description" class="col-sm-2 col-form-label">{{ __('global.description') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="payable-description" class="form-control" name="description" value="{{ $payable->description }}" disabled>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="payable-value" class="col-sm-2 col-form-label">{{ __('global.value') }}</label>
                    <div class="col-sm-10">
                        <input type="text" id="payable-value" class="form-control" value="{{ toBrMoney($payable->value) }}" disabled>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="payable-monthly" class="col-sm-2 col-form-label">{{ __('global.monthly') }}</label>
                    <div class="col-sm-10">
                        @if ($payable->monthly)
                            <i class="fas fa-check" style="color: green"></i>
                        @else
                            <i class="fas fa-times" style="color: red"></i>
                        @endif
                    </div>
                </div>

            </div>

            <div class="card-footer">
                <a href="{{ route('payables.index') }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                @if (! $payable->isPaid())
                    <button class="btn btn-primary" type="submit">{{ __('global.pay') }}</button>
                @endif
            </div>
            
        </form>
    </div>
@stop