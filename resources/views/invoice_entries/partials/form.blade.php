@csrf
@if (! isset($entry))
    <div class="form-group row">
        <label for="invoice_entry-credit-card" class="col-sm-2 col-form-label">{{ __('global.credit-card') }}</label>
        <div class="col-sm-10">
            <select class="form-control @error('card_id') is-invalid @enderror" name="card_id" id="invoice_entry-card_id" required>
                <option value="">{{ __('global.select_card') }}</option>
                @foreach ($form_cards as $key => $card)
                    <option value="{{ $key }}">{{ $card }}</option>
                @endforeach
            </select>        
        </div>
    </div>
@endif

@if (! isset($entry))
    <div class="form-group row">
        <label for="invoice_entry-date" class="col-sm-2 col-form-label">{{ __('global.date') }}</label>
        <div class="col-sm-10">
        <input type="text" id="invoice_entry-date" class="form-control datepicker @error('date') is-invalid @enderror" name="date" value="{{ $entry->date ?? old('date') }}" required>
        </div>
    </div>
@endif

<div class="form-group row">
    <label for="invoice_entry-description" class="col-sm-2 col-form-label">{{ __('global.description') }}</label>
    <div class="col-sm-10">
    <input type="text" id="invoice_entry-description" class="form-control @error('description') is-invalid @enderror" name="description" value="{{ $entry->description ?? old('description') }}" required>
    </div>
</div>

@include('includes.categories_form_add')

<div class="form-group row">
    <label for="invoice_entry-value" class="col-sm-2 col-form-label">{{ __('global.value') }}</label>
    <div class="col-sm-10">
        <input type="number" id="invoice_entry-value" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ $entry->value ?? old('value') }}" min="0.01" step="any" required>
    </div>
</div>

@if (! isset($entry))
    <div class="form-group row">
        <label for="invoice_entry-installment" class="col-sm-2 col-form-label">{{ __('global.installment') }}</label>
        <div class="col-sm-10">
            <input id="invoice_entry-installment" type="checkbox" name="installment" data-bootstrap-switch data-off-color="danger" data-on-color="success">
        </div>
    </div>
@endif

<div id="installments" style="display: none">
</div>
