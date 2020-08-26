<div class="form-group row">
    <label for="invoice_entry-category" class="col-sm-2 col-form-label">{{ __('global.category') }}</label>
    <div class="col-sm-10">
        <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="invoice_entry-category_id" required>
            <option value="">{{ __('global.select_category') }}</option>
            <optgroup label={{ __('global.incomes') }}>
                @foreach ($form_all_categories['income'] as $key => $category)
                    <option value="{{ $key }}" {{ isset($entry) &&  $entry->category_id === $key ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </optgroup>
            <optgroup label={{ __('global.expenses') }}>
                @foreach ($form_all_categories['expense'] as $key => $category)
                    <option value="{{ $key }}" {{ isset($entry) &&  $entry->category_id === $key ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </optgroup>
            
        </select>
    </div>
</div>