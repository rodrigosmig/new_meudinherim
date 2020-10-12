@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/payables/index.js') }}"></script>
@endpush

@section('button-header')
    <a href="{{ route('payables.create') }}" class="float-sm-right btn btn-sm btn-success shadow-sm"><i class="fas fa-plus"></i> {{__("global.add")}}</a>
@endsection

@section('js')
    <script>
        var payable_title = '{{ __('messages.ajax_title') }}';
        var payable_text = '{{ __('messages.account_scheduling.ajax_text') }}';
        var button_cancel = '{{ __('global.cancel') }}';
        var button_confirm = '{{ __('global.confirm') }}';

        $(function () {
            $('.datatable').DataTable({
                "language": {
                    "url": "{{ asset('js/plugins/datatable-portuguese.json') }}"
                }
            });
        })
    </script>
@stop


@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
    <div class="card">
        <div class="card-body">
            <label>{{ __('global.filter_by_range') }}:</label>
            <div class="form-inline">
                <form action="{{ route('payables.filter') }}" method="POST">
                    @include('includes.form_filter')
                </form>
            </div>

            <table class="table datatadsfsdble">
                <thead>
                    <th>{{ __('global.date') }}</th>
                    <th>{{ __('global.description') }}</th>
                    <th>{{ __('global.category') }}</th>
                    <th>{{ __('global.value') }}</th>
                    <th>{{ __('global.status') }}</th>
                    <th>{{ __('global.actions') }}</th>
                </thead>
                <tbody>
                    @foreach ($payables as $payable)
                        <tr>
                            <td>
                                {{ toBrDate($payable->date) }}
                            </td>
                            <td>
                                {{ $payable->description }}
                            </td>
                            <td>
                                {{ $payable->category->name }}
                            </td>
                            <td>
                                {{ toBrMoney($payable->value) }}
                            </td>
                            <td>
                                @if ($payable->isPaid())
                                    <span style="color: green">{{ __('global.paid') }}</span>
                                @else
                                    <span style="color: red">{{ __('global.open') }}</span>
                                @endif
                            </td>
                            <td class="table-actions">
                                <div class="row">
                                    <a class="btn btn-info btn-sm edit" href="{{ route('payables.edit', $payable->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete" data-payable="{{ $payable->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @if (! $payable->isPaid())
                                        <a class="btn btn-success btn-sm edit" href="" data-toggle="tooltip" data-placement="top" title="{{ __('global.pay') }}">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </a>
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
