@csrf
<div class="form-group row">
    <label for="receivable-date" class="col-sm-2 col-form-label">{{ __('global.date') }}</label>
    <div class="col-sm-10">
    <input type="text" id="receivable-date" class="form-control datepicker @error('date') is-invalid @enderror" name="date" value="{{ $account_scheduling->date ?? old('date') }}" required>
    </div>
</div>

<div class="form-group row">
    <label for="receivable-description" class="col-sm-2 col-form-label">{{ __('global.description') }}</label>
    <div class="col-sm-10">
    <input type="text" id="receivable-description" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $account_scheduling->description ?? old('description') }}" required>
    </div>
</div>

<div class="form-group row">
    <label for="$receivable-value" class="col-sm-2 col-form-label">{{ __('global.value') }}</label>
    <div class="col-sm-10">
        <input type="number" id="receivable-value" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ $account_scheduling->value ?? old('value') }}" min="0.01" step="any" required>
    </div>
</div>