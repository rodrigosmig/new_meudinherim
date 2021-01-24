@csrf
<div class="table-margin-bottom">    
    <input id="filter_from" class="form-control" type="text" name="filter_from" placeholder="{{ __('global.initial_date') }}" value="{{ $filter['from'] ?? '' }}">
    <input id="filter_to" class="form-control" type="text" name="filter_to" placeholder="{{ __('global.final_date') }}" value="{{ $filter['to'] ?? '' }}">
    <select class="form-control" name="filter_status">
        <option value="all">{{ __('global.all') }}</option>
        <option value="open" {{ isset($filter['status']) && $filter['status'] == 'open' ? 'selected' : '' }}>{{ __('global.opens') }}</option>
        <option value="paid" {{ isset($filter['status']) && $filter['status'] == 'paid' ? 'selected' : '' }}>{{ isset($payables) ? __('global.paids') : __('global.receiveds') }}</option>
    </select>

    <button type="submit" class="btn btn-primary waves-effect">
        {{ __('global.filter') }}
    </button>
</div>