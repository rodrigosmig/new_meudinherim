@csrf
<div class="table-margin-bottom">    
    <input id="filter_from" class="form-control" type="text" name="filter_from" placeholder="{{ __('global.initial_date') }}">
    <input id="filter_to" class="form-control" type="text" name="filter_to" placeholder="{{ __('global.final_date') }}">

    <button type="submit" class="btn btn-primary waves-effect">
        {{ __('global.filter') }}
    </button>
</div>