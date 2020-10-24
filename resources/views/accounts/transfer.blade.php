@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.bank_transfer') }}</li>
    </ol>
@endsection

@section('js')
    <script src="{{ asset('js/plugins/init-datepicker.js') }}"></script>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('accounts.transfer_store') }}" method="POST">
            @csrf
            <div class="card-header">
                {{ __('global.bank_transfer') }}
            </div>
            <div class="card-body">
                <fieldset>
                    <legend style="color: red">{{ __('global.source_account') }}</legend>                    
                    <div class="form-group row">                        
                        <label class="col-sm-2 col-form-label">{{ __('global.select_account') }}</label>
                        <div class="col-sm-10">
                            <select class="form-control @error('type') is-invalid @enderror" name="source_account_id" required>
                                <option value="">{{ __('global.select_account') }}</option>
                                @foreach ($form_accounts as $key => $account)
                                    <option value="{{ $key }}">{{ $account }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{ __("global.select_category") }}</label>
                        <div class="col-sm-10">
                            <select class="form-control @error('type') is-invalid @enderror" name="source_category_id" required>
                                <option value="">{{ __('global.select_category') }}</option>
                                @foreach ($form_expense_categories as $key => $category)
                                    <option value="{{ $key }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <fieldset>
                    <legend style="color: blue">{{ __('global.destination_account') }}</legend>                    
                    <div class="form-group row">                        
                        <label class="col-sm-2 col-form-label">{{ __('global.select_account') }}</label>
                        <div class="col-sm-10">
                            <select class="form-control @error('type') is-invalid @enderror" name="destination_account_id" required>
                                <option value="">{{ __('global.select_account') }}</option>
                                @foreach ($form_accounts as $key => $account)
                                    <option value="{{ $key }}">{{ $account }}</option>
                                @endforeach
                                
                            </select>
                        </div>
                    </div>                    
                    <div class="form-group row">
                        <label for="account-type" class="col-sm-2 col-form-label">{{ __("global.select_category") }}</label>
                        <div class="col-sm-10">
                            <select class="form-control @error('type') is-invalid @enderror" name="destination_category_id" required>
                                <option value="">{{ __('global.select_category') }}</option>
                                @foreach ($form_income_categories as $key => $category)
                                    <option value="{{ $key }}">{{ $category }}</option>
                                @endforeach
                                
                            </select>
                        </div>
                    </div>                    
                </fieldset>
                <hr>
                <div class="form-group row">
                    <label for="transfer-description" class="col-sm-2 col-form-label">{{ __('global.description') }}</label>
                    <div class="col-sm-10">
                      <input type="text" id="transfer-description" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="{{ __('global.description') }}" value="{{ $account->description ?? old('name') }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="transfer-date" class="col-sm-2 col-form-label">{{ __('global.date') }}</label>
                    <div class="col-sm-10">
                      <input type="text" id="transfer-date" class="form-control datepicker @error('date') is-invalid @enderror" name="date" placeholder="{{ __('global.date') }}" value="{{ $transfer->date ?? old('name') }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="transfer-value" class="col-sm-2 col-form-label">{{ __('global.value') }}</label>
                    <div class="col-sm-10">
                      <input type="number" id="transfer-value" class="form-control @error('value') is-invalid @enderror" name="value" placeholder="{{ __('global.value') }}" value="{{ $account->value ?? old('name') }}" required>
                    </div>
                </div>
            </div>        
            <div class="card-footer">
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('global.submit') }}</button>
            </div>
        </form>
    </div>    
@stop
