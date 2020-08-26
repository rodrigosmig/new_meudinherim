<div class="form-group row">
    <label for="card-name" class="col-sm-2 col-form-label">{{ __('global.name') }}</label>
    <div class="col-sm-10">
      <input type="text" id="card-name" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="{{ __('global.name') }}" value="{{ $card->name ?? old('name') }}" required>
    </div>
</div>

<div class="form-group row">
    <label for="card-credit_limit" class="col-sm-2 col-form-label">{{ __('global.credit_limit') }}</label>
    <div class="col-sm-10">
      <input type="number" id="card-credit_limit" class="form-control @error('credit_limit') is-invalid @enderror" name="credit_limit" placeholder="0.00" value="{{ $card->credit_limit ?? old('credit_limit') }}" required>
    </div>
</div>

<div class="form-group row">
  <label for="card-closing_day" class="col-sm-2 col-form-label">{{ __('global.closing_day') }}</label>
  <div class="col-sm-10">
    <input type="text" id="card-closing_day" class="form-control @error('closing_day') is-invalid @enderror" name="closing_day" placeholder="1-31" value="{{ $card->closing_day ?? old('closing_day') }}" required>
  </div>
</div>

<div class="form-group row">
    <label for="card-pay_day" class="col-sm-2 col-form-label">{{ __('global.pay_day') }}</label>
    <div class="col-sm-10">
      <input type="text" id="card-pay_day" class="form-control @error('pay_day') is-invalid @enderror" name="pay_day" placeholder="1-31" value="{{ $card->pay_day ?? old('pay_day') }}" required>
    </div>
</div>

