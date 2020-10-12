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
    <label for="payable-date" class="col-sm-2 col-form-label">{{ __('global.date') }}</label>
    <div class="col-sm-10">
    <input type="text" id="payable-date" class="form-control datepicker @error('date') is-invalid @enderror" name="date" value="{{ $payable->date ?? old('date') }}" required>
    </div>
</div>

<div class="form-group row">
    <label for="payable-description" class="col-sm-2 col-form-label">{{ __('global.description') }}</label>
    <div class="col-sm-10">
    <input type="text" id="payable-description" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $payable->description ?? old('description') }}" required>
    </div>
</div>

<div class="form-group row">
    <label for="payable-value" class="col-sm-2 col-form-label">{{ __('global.value') }}</label>
    <div class="col-sm-10">
        <input type="number" id="payable-value" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ $payable->value ?? old('value') }}" min="0.01" step="any" required>
    </div>
</div>