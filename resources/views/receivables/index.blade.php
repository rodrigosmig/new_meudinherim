@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/plugins/init-datepicker_range.js') }}"></script>
    <script src="{{ asset('js/receivables/index.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datetime-moment.js') }}"></script>
    <script src="{{ asset('js/plugins/init-datatable.js') }}"></script>
@endpush

@section('button-header')
    <a href="{{ route('receivables.create') }}" class="float-sm-right btn btn-sm btn-success shadow-sm"><i class="fas fa-plus"></i> {{__("global.add")}}</a>
@endsection

@section('js')
    <script>
        var receivable_title = '{{ __('messages.ajax_title') }}';
        var receivable_text = '{{ __('messages.account_scheduling.ajax_text') }}';
        var button_cancel = '{{ __('global.cancel') }}';
        var button_confirm = '{{ __('global.confirm') }}';
    </script>
@stop

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">
        <div class="card-body">
            <label>{{ __('global.filter_by_range') }}:</label>
            <div class="form-inline">
                <form action="{{ route('receivables.filter') }}" method="POST">
                    @include('includes.form_filter')
                </form>
            </div>

            <table class="table datatable">
                <thead>
                    <th>{{ __('global.due_date') }}</th>
                    <th>{{ __('global.paid_date') }}</th>
                    <th>{{ __('global.description') }}</th>
                    <th>{{ __('global.category') }}</th>
                    <th>{{ __('global.value') }}</th>
                    <th>{{ __('global.monthly') }}</th>
                    <th>{{ __('global.received') }}</th>
                    <th>{{ __('global.actions') }}</th>
                </thead>
                <tbody>
                    @foreach ($receivables as $receivable)
                        <tr>
                            <td>
                                {{ toBrDate($receivable->due_date) }}
                            </td>
                            <td>
                                @if ($receivable->isPaid())
                                    {{ toBrDate($receivable->paid_date) }}
                                @endif
                            </td>
                            <td>
                                {{ $receivable->description }}
                            </td>
                            <td>
                                {{ $receivable->category->name }}
                            </td>
                            <td>
                                {{ toBrMoney($receivable->value) }}
                            </td>
                            <td align="center">
                                @if ($receivable->monthly)
                                    <i class="fas fa-check" style="color: green"></i>
                                @else
                                    <i class="fas fa-times" style="color: red"></i>
                                @endif
                                
                            </td>
                            <td align="center">
                                @if ($receivable->isPaid())
                                    <i class="fas fa-check" style="color: green"></i>
                                @else
                                    <i class="fas fa-times" style="color: red"></i>
                                @endif
                            </td>
                            <td class="table-actions">
                                <div class="row">
                                    @if (! $receivable->isPaid())
                                        <a class="btn btn-info btn-sm edit" href="{{ route('receivables.edit', $receivable->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm delete" data-receivable="{{ $receivable->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <a class="btn btn-success btn-sm edit" href="{{ route('receivables.show', $receivable->id) }}" data-toggle="tooltip" data-placement="top"  title="{{ __('global.receive') }}">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-danger btn-sm cancel_receivement" data-receivable="{{ $receivable->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.cancel_receivement') }}">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
