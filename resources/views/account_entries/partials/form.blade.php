@csrf
@if (! isset($entry))
    <div class="form-group row">
        <label for="invoice_entry-account" class="col-sm-2 col-form-label">{{ __('global.account') }}</label>
        <div class="col-sm-10">
            <select class="form-control @error('account_id') is-invalid @enderror" name="account_id" id="invoice_entry-account_id" required>
                <option value="">{{ __('global.select_account') }}</option>
                @foreach ($form_accounts as $key => $account)
                    <option value="{{ $key }}">{{ $account }}</option>
                @endforeach
            </select>        
        </div>
    </div>
@endif

<div class="form-group row">
    <label for="invoice_entry-date" class="col-sm-2 col-form-label">{{ __('global.date') }}</label>
    <div class="col-sm-10">
    <input type="text" id="invoice_entry-date" class="form-control datepicker @error('date') is-invalid @enderror" name="date" value="{{ $entry->date ?? old('date') }}" required>
    </div>
</div>

<div class="form-group row">
    <label for="invoice_entry-description" class="col-sm-2 col-form-label">{{ __('global.description') }}</label>
    <div class="col-sm-10">
    <input type="text" id="invoice_entry-description" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $entry->description ?? old('description') }}" required>
    </div>
</div>

@include('includes.categories_form_add')

<div class="form-group row">
    <label for="$account_entry-value" class="col-sm-2 col-form-label">{{ __('global.value') }}</label>
    <div class="col-sm-10">
        <input type="number" id="invoice_entry-value" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ $entry->value ?? old('value') }}" min="0.01" step="any" required>
    </div>
</div>

