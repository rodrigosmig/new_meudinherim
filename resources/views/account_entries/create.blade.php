@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">{{ __('global.accounts') }}</li>
        <li class="breadcrumb-item">{{ __('global.entries') }}</li>
        <li class="breadcrumb-item active">{{ __('global.new') }}</li>
    </ol>
@endsection

@section('js')
    <script>
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    </script>
@endsection

@section('plugins.Datatables', true)

@section('content')
    <div class="card">
        <form action="{{ route('account_entries.store') }}" method="post">
            @csrf
            <div class="card-body">
                @include('account_entries.partials.form')
            </div>        
            <div class="card-footer">
                <a href="{{ route('account_entries.index') }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('global.submit') }}</button>
            </div>
        </form>
    </div>    
@stop
