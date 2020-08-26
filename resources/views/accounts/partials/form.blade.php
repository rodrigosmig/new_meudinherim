<div class="form-group row">
    <label for="account-name" class="col-sm-2 col-form-label">{{ __('global.name') }}</label>
    <div class="col-sm-10">
      <input type="text" id="account-name" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="{{ __('global.name') }}" value="{{ $account->name ?? old('name') }}" required>
    </div>
</div>

<div class="form-group row">
    <label for="account-type" class="col-sm-2 col-form-label">{{ __("global.type") }}</label>
    <div class="col-sm-10">
        <select class="form-control @error('type') is-invalid @enderror" name="type" required>
            @foreach ($types as $key => $value)
                <option value="{{ $key }}" {{ isset($account) && $account->type === $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
            
        </select>
    </div>
</div>
