@extends('layouts.app')

@section('button-header')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active">{{ __('global.edit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('categories.update', $category->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="card-body">
                @include('categories.partials.form')
            </div>        
            <div class="card-footer">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-dark">{{ __('global.cancel') }}</a>
                <button class="btn btn-primary" type="submit">{{ __('global.submit') }}</button>
            </div>
        </form>
    </div>    
@stop
