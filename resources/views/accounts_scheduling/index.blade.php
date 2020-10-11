@extends('layouts.app')

@section('button-header')
    <a href="{{ route('cards.create') }}" class="float-sm-right btn btn-sm btn-success shadow-sm"><i class="fas fa-plus"></i> {{__("global.add")}}</a>
@endsection

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills ml-auto p-2">
                <li class="nav-item"><a id="incoming-tab" class="nav-link active" href="#payable" data-toggle="tab">{{__("global.payable")}}</a></li>
                <li class="nav-item"><a id="outgoing-tab" class="nav-link" href="#receivable" data-toggle="tab">{{__("global.receivable")}}</a></li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="payable" role="tabpanel" aria-labelledby="incoming-tab">
                    1
                </div>
                <div class="tab-pane fade" id="receivable" role="tabpanel" aria-labelledby="outgoing-tab">
                    2
                </div>
            </div>
        </div>
    </div>
@stop
