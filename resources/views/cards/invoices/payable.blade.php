@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('payables.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.new') }}</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('payables.store') }}" method="POST">
            <div class="card-body">
                @csrf
                <div class="form-group row">
                    <label for="$account_entry-category" class="col-sm-2 col-form-label">{{ __('global.category') }}</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="category_id" required>
                            <option value="">{{ __('global.select_category') }}</option>
                            @foreach ($form_expense_categories as $key => $category)
                                <option value="{{ $key }}" {{ isset($payable) && $payable->category_id === $key ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="payable-due_date" class="col-sm-2 col-form-label">{{ __('global.due_date') }}</label>
                    <div class="col-sm-10">
                    <input type="text" id="payable-due_date" class="form-control @error('due_date') is-invalid @enderror" name="due_date" value="{{ $due_date ?? old('due_date') }}" readonly>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="payable-description" class="col-sm-2 col-form-label">{{ __('global.description') }}</label>
                    <div class="col-sm-10">
                    <input type="text" id="payable-description" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $description ?? old('description') }}" readonly>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="payable-value" class="col-sm-2 col-form-label">{{ __('global.value') }}</label>
                    <div class="col-sm-10">
                        <input type="number" id="payable-value" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ $value ?? old('value') }}" min="0.01" step="any" readonly>
                    </div>
                </div>
                <input type="hidden" name="invoice_id" value="{{ $invoice_id }}">

            </div>

            <div class="card-footer">
                <a href="{{ route('payables.index') }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('global.submit') }}</button>
            </div>
            
        </form>
    </div>
@stop
