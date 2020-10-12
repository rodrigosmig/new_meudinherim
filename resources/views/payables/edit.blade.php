@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.edit') }}</li>
    </ol>
@endsection

@section('js')
    <script>
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    </script>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('payables.update', $payable->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="card-body">

                @include('payables.partials.form')
                
            </div>        
            <div class="card-footer">
                <a href="{{ route('payables.index') }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('global.submit') }}</button>
            </div>
        </form>
    </div>    
@stop
