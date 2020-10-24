@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/plugins/init-datepicker_range.js') }}"></script>
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="float-left">{{ __('global.accounts_payable') }}</h4>
        </div>

        <div class="card-body">
            <label>{{ __('global.filter_by_range') }}:</label>
            <div class="form-inline">
                <form action="{{ route('reports.payables.filter') }}" method="POST">
                    @include('includes.form_filter')
                </form>
            </div>

            @isset($payables)
                @if ($payables->isNotEmpty())
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
                            @foreach ($payables as $payable)
                                <tr>
                                    <td>{{ toBrDate($payable->due_date) }}</td>
                                    <td>{{ isset($payable->paid_date) ? toBrDate($payable->paid_date) : '' }}</td>
                                    <td>{{ $payable->description }}</td>
                                    <td>{{ $payable->category->name }}</td>
                                    <td>{{ toBrMoney($payable->value) }}</td>
                                    <td>
                                        @if($payable->isPaid())
                                            {{ __('global.paid') }}
                                        @else
                                            {{ __('global.open') }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    
                @endif
                
            @endisset
        </div>

        @isset($total)
            <div class="card-footer">
                <div class="col-6">
                    <h5>
                        {{ __('global.initial_date') }}: <b>{{ toBrDate($from) }}</b> - {{ __('global.final_date') }}: <b>{{ toBrDate($to) }}</b>
                    </h6>
                    <div class="table-responsive table-borderless">
                        <table class="table">
                            <tr>
                                <th style="width:30%">{{ __('global.total_opens') }}:</th>
                                <td>{{ toBrMoney($total['open']) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('global.total_paids') }}:</th>
                                <td>{{ toBrMoney($total['paid']) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        @endisset
    </div>
@endsection