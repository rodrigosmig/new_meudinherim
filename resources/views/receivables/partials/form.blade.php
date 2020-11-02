@csrf
<div class="form-group row">
    <label for="$account_entry-category" class="col-sm-2 col-form-label">{{ __('global.category') }}</label>
    <div class="col-sm-10">
        <select class="form-control" name="category_id" required>
            <option value="">{{ __('global.select_category') }}</option>
            @foreach ($form_income_categories as $key => $category)
                <option value="{{ $key }}" {{ isset($receivable) && $receivable->category_id === $key ? 'selected' : '' }}>{{ $category }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group row">
    <label for="receivable-due_date" class="col-sm-2 col-form-label">{{ __('global.due_date') }}</label>
    <div class="col-sm-10">
    <input type="text" id="receivable-due_date" class="form-control datepicker @error('due_date') is-invalid @enderror" name="due_date" value="{{ $receivable->due_date ?? old('due_date') }}" required>
    </div>
</div>

<div class="form-group row">
    <label for="receivable-description" class="col-sm-2 col-form-label">{{ __('global.description') }}</label>
    <div class="col-sm-10">
    <input type="text" id="receivable-description" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $receivable->description ?? old('description') }}" required>
    </div>
</div>

<div class="form-group row">
    <label for="receivable-value" class="col-sm-2 col-form-label">{{ __('global.value') }}</label>
    <div class="col-sm-10">
        <input type="number" id="receivable-value" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ $receivable->value ?? old('value') }}" min="0.01" step="any" required>
    </div>
</div>

<div class="form-group row">
    <label for="receivable-monthly" class="col-sm-2 col-form-label">{{ __('global.monthly') }}</label>
    <div class="col-sm-10">
        <input id="receivable-monthly" type="checkbox" name="monthly" data-bootstrap-switch data-off-color="danger" data-on-color="success">
    </div>
</div>