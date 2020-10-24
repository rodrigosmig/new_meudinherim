@csrf
<div class="table-margin-bottom">    
    <input id="filter_from" class="form-control" type="text" name="filter_from" placeholder="{{ __('global.initial_date') }}">
    <input id="filter_to" class="form-control" type="text" name="filter_to" placeholder="{{ __('global.final_date') }}">
    <select class="form-control" name="filter_status">
        <option value="all">{{ __('global.all') }}</option>
        <option value="open">{{ __('global.opens') }}</option>
        <option value="paid">{{ isset($payables) ? __('global.paids') : __('global.receiveds') }}</option>
    </select>

    <button type="submit" class="btn btn-primary waves-effect">
        {{ __('global.filter') }}
    </button>
</div>