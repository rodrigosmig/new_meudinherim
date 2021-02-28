@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/plugins/init-datepicker_range.js') }}"></script>
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="float-left">{{ __('global.accounts_receivable') }}</h4>
        </div>

        <div class="card-body">
            <label>{{ __('global.filter_by_range') }}:</label>
            <div class="form-inline">
                <form action="{{ route('reports.receivables.filter') }}" method="POST">
                    @include('includes.form_filter')
                </form>
            </div>

            @isset($receivables)
                @if ($receivables->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-margin-top">
                            <thead>
                                <th width="12%">{{ __('global.due_date') }}</th>
                                <th width="12%">{{ __('global.paid_date') }}</th>
                                <th>{{ __('global.description') }}</th>
                                <th>{{ __('global.category') }}</th>
                                <th>{{ __('global.value') }}</th>
                                <th>{{ __('global.status') }}</th>
                            </thead>
    
                            <tbody>
                                @foreach ($receivables as $receivable)
                                    <tr>
                                        <td>{{ toBrDate($receivable->due_date) }}</td>
                                        <td>{{ isset($receivable->paid_date) ? toBrDate($receivable->paid_date) : '' }}</td>
                                        <td>{{ $receivable->description }}</td>
                                        <td>{{ $receivable->category->name }}</td>
                                        <td>{{ toBrMoney($receivable->value) }}</td>
                                        <td>
                                            @if($receivable->isPaid())
                                                {{ __('global.received') }}
                                            @else
                                                {{ __('global.open') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>                    
                @else
                    
                @endif
                
            @endisset
        </div>

        @isset($total)
            <div class="card-footer">
                <div class="col-10">
                    <h5>
                        {{ __('global.initial_date') }}: <b>{{ toBrDate($from) }}</b> - {{ __('global.final_date') }}: <b>{{ toBrDate($to) }}</b>
                    </h6>
                    <div class="table-responsive table-borderless">
                        <table class="table">
                            @if ($filter['status'] !== 'paid' )
                                <tr>
                                    <th style="width:30%">{{ __('global.total_opens') }}:</th>
                                    <td>{{ toBrMoney($total['open']) }}</td>
                                </tr>
                            @endif
                            @if ($filter['status'] !== 'open' )
                                <tr>
                                    <th>{{ __('global.total_receiveds') }}:</th>
                                    <td>{{ toBrMoney($total['paid']) }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        @endisset
    </div>
@endsection