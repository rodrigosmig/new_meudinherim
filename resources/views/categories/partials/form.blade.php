<div class="form-group row">
    <label for="category-type" class="col-sm-2 col-form-label">{{ __("global.type") }}</label>
    <div class="col-sm-10">
        <select class="form-control @error('type') is-invalid @enderror" name="type" required>
            <option value="">{{ __('messages.categories.select_type') }}</option>
            <option value="1" {{ isset($category) && $category->type === 1 ? 'selected' : '' }}>{{ __('global.incoming') }}</option>
            <option value="2" {{ isset($category) && $category->type === 2 ? 'selected' : '' }}>{{ __('global.outgoing') }}</option>
            
        </select>
    </div>
</div>
<div class="form-group row">
    <label for="category-name" class="col-sm-2 col-form-label">{{ __('global.name') }}</label>
    <div class="col-sm-10">
      <input type="text" id="category-name" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="{{ __('global.name') }}" value="{{ $category->name ?? old('name') }}" required>
    </div>
</div>
