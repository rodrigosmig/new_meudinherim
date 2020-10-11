@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/dashboard/dashboard.js') }}"></script>
    <script src="{{ asset('js/plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/plugins/raphael.min.js') }}"></script>
    <script src="{{ asset('js/plugins/morris.js') }}"></script>
    <script>
        var total_income = @json($total_income_in_six_months);
        var total_expense = @json($total_expense_in_six_months);
        var total_invoices = @json($total_invoices_in_six_monthss);
        var total_income_category = @json($total_income_category);
        var total_expense_category = @json($total_expense_category);
        var total_card_expense_category = @json($total_card_expense_category);
        var months = @json($months);
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('css/plugins/morris.css') }}">
@endpush

@section('button-header')
    <div class="row float-sm-right cleafix">
        <form id="form_month" method="POST" action="{{ route('dashboard.months') }}">
            @csrf
            <button id="last_month" type="submit" class="btn btn-info shadow-sm text-white" data-month="last" title="{{__('global.last_month') }}">
                <i class="fas fa-chevron-left"></i>
            </button>
    
            <button id="next_month" type="submit" class="btn btn-info shadow-sm text-white" data-month="next" title="{{__('global.next_month') }}">
                <i class="fas fa-chevron-right"></i>
            </button>
        </form>
    </div>
    
@endsection

@section('content')
    <div class="row justify-content-md-center">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-arrow-up"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('global.incomes') }}</span>
                    <span class="info-box-number">
                        {{ toBrMoney($total_income) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('global.expenses') }}</span>
                    <span class="info-box-number">
                        {{ toBrMoney($total_expense) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-credit-card"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('global.credit-card') }}</span>
                    <span class="info-box-number">
                        {{ toBrMoney($total_invoices) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix hidden-md-up"></div>

    
    <div class="row justify-content-md-center">
        <div class="col-md-3">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">{{ __('global.monthly_entries_by_category') }} (%)</h3>
                </div>
                <div class="card-body">
                    <div id="incomeCategoryChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">{{ __('global.monthly_spend_by_category') }} (%)</h3>
                </div>
                <div class="card-body">
                    <div id="expenseCategoryChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">{{ __('global.monthly_cards_by_category') }} (%)</h3>
                </div>
                <div class="card-body">
                    <div id="cardsCategoryChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">{{ __('global.lasts_six_months') }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="">
                                <h3 class="card-title">{{ __('global.accounts') }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-4">
                                <canvas id="total-account-chart" height="200"></canvas>
                            </div>
                
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square text-primary"></i> {{ __('global.total_income') }}
                                </span>
                
                                <span>
                                    <i class="fas fa-square text-red"></i> {{ __('global.total_expense') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
        
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">{{ __('global.credit-card') }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-4">
                                <canvas id="credit-card-chart" height="200"></canvas>
                            </div>
                
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square text-yellow"></i> {{ __('global.credit-card') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection
