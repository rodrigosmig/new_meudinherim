@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/plugins/init-datepicker_range.js') }}"></script>
    <script src="{{ asset('js/reports/total_by_category.js') }}"></script>
@endpush

@section('content')
    {{-- Modal --}}
    <div class="modal fade" id="modal-entries" tabindex="-1" role="dialog" aria-labelledby="entriesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('global.entries') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loading">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                              <span class="sr-only"></span>
                            </div>
                        </div>
                    </div>

                    <h4 id="no-entries"></h4>

                    <table id="table-entries" class="table table-responsive table-striped" style="display: none">
                        <caption>{{ __("global.category") }}: <span id="table-caption"></span></caption>
                        <thead>
                            <th>{{ __('global.date') }}</th>
                            <th>{{ __('global.description') }}</th>
                            <th>{{ __('global.value') }}</th>
                            <th>{{ __('global.source') }}</th>
                        </thead>

                        <tbody id="table-body">

                        </tbody>
                                                
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">   
        <div class="card-header">
            <h3 class="float-left">{{ __('global.total_by_category') }}</h3>
            <ul class="nav nav-pills ml-auto float-right">
                <li class="nav-item"><a id="incoming-tab" class="nav-link active" href="#incoming" data-toggle="tab">{{__("global.incoming")}}</a></li>
                <li class="nav-item"><a id="outgoing-tab" class="nav-link" href="#outgoing" data-toggle="tab">{{__("global.outgoing")}}</a></li>
                <li class="nav-item"><a id="outgoing-tab" class="nav-link" href="#credit_card" data-toggle="tab">{{__("global.credit-card")}}</a></li>
            </ul>
        </div>

        <div class="card-body">
            <label>{{ __('global.filter_by_range') }}:</label>
            <div class="form-inline">
                <form action="{{ route('reports.total_by_category.filter') }}" method="POST">
                    @csrf
                    <div class="table-margin-bottom">    
                        <input id="filter_from" class="form-control" type="text" name="filter_from" value="{{ isset($from) ? $from : '' }}" placeholder="{{ __('global.initial_date') }}">
                        <input id="filter_to" class="form-control" type="text" name="filter_to" value="{{ isset($to) ? $to : '' }}" placeholder="{{ __('global.final_date') }}">

                        <button type="submit" class="btn btn-primary waves-effect">
                            {{ __('global.filter') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="incoming" role="tabpanel" aria-labelledby="incoming-tab">
                    @isset($incomes)
                        @if (! empty($incomes))
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <th width="33%">{{ __('global.category') }}</th>
                                        <th width="33%">{{ __('global.quantity') }}</th>
                                        <th>Total</th>
                                    </thead>
    
                                    <tbody>                                
                                        @foreach ($incomes as $category)
                                            <tr>
                                                <td>{{ $category['category'] }}</td>
                                                <td>
                                                    <a class="badge-pill badge-success show-entries" data-from="{{ $from }}" data-to="{{ $to }}" data-category="{{ $category['id'] }}" data-type="account" href="javascript:void(0)" data-toggle="modal" data-target="#modal-entries">
                                                        {{ $category['quantity'] }}
                                                    </a>
                                                </td>
                                                <td>{{ toBrMoney($category['total'] )}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <h4>{{ __('messages.entries.not_found') }}</h4>
                        @endif
                        
                    @endisset                
                </div>
        
                <div class="tab-pane fade show" id="outgoing" role="tabpanel" aria-labelledby="outgoing-tab">
                    @isset($expenses)
                        @if (! empty($expenses))
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <th width="33%">{{ __('global.category') }}</th>
                                        <th width="33%">{{ __('global.quantity') }}</th>
                                        <th>Total</th>
                                    </thead>
    
                                    <tbody>                                
                                        @foreach ($expenses as $category)
                                            <tr>
                                                <td>{{ $category['category'] }}</td>
                                                <td>
                                                    <a class="badge-pill badge-success show-entries" data-from="{{ $from }}" data-to="{{ $to }}" data-category="{{ $category['id'] }}" data-type="account" href="javascript:void(0)" data-toggle="modal" data-target="#modal-entries">
                                                        {{ $category['quantity'] }}
                                                    </a>
                                                </td>
                                                <td>{{ toBrMoney($category['total'] )}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <h4>{{ __('messages.entries.not_found') }}</h4>
                        @endif
                        
                    @endisset
                </div>
        
                <div class="tab-pane fade show" id="credit_card" role="tabpanel" aria-labelledby="credit_card-tab">
                    @isset($cards)
                        @if (! empty($cards))
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <th width="33%">{{ __('global.category') }}</th>
                                        <th width="33%">{{ __('global.quantity') }}</th>
                                        <th>Total</th>
                                    </thead>
    
                                    <tbody>                                
                                        @foreach ($cards as $category)
                                            <tr>
                                                <td>{{ $category['category'] }}</td>
                                                <td>
                                                    <a class="badge-pill badge-success show-entries" data-from="{{ $from }}" data-to="{{ $to }}" data-category="{{ $category['id'] }}" data-type="card" href="javascript:void(0)" data-toggle="modal" data-target="#modal-entries">
                                                        {{ $category['quantity'] }}
                                                    </a>
                                                </td>
                                                <td>{{ toBrMoney($category['total'] )}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <h4>{{ __('messages.entries.not_found') }}</h4>
                        @endif                        
                    @endisset
                </div>
            </div>
        </div>
    </div>
@stop
