@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/account_entries/index.js') }}"></script>
@endpush

@section('js')
    <script>
        var entry_title = '{{ __('messages.ajax_title') }}';
        var entry_text = '{{ __('messages.entries.ajax_text') }}';
        var button_cancel = '{{ __('global.cancel') }}';
        var button_confirm = '{{ __('global.confirm') }}';
    </script>

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()

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
        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="pt-2 px-3"><h3 class="card-title">{{ __('global.accounts') }}</h3></li>
                @foreach ($account_entries as $key => $data)
                    <li class="nav-item"><a class="nav-link" href="#tab-{{ $key }}" data-toggle="tab">{{ $data['name'] }}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active">fdgsdfgdf</div>
                @foreach ($account_entries as $key => $data)
                    <div class="tab-pane" id="tab-{{ $key }}">
                        <table class="table datatable">
                            <thead>
                                <th>{{ __('global.date') }}</th>
                                <th>{{ __('global.description') }}</th>
                                <th>{{ __('global.category') }}</th>
                                <th>{{ __('global.value') }}</th>
                                <th>{{ __('global.actions') }}</th>
                            </thead>
                            <tbody>
                                @foreach ($data['items'] as $entry)
                                    <tr>
                                        <td>{{ toBrDate($entry->date) }}</td>
                                        <td>{{ $entry->description }}</td>
                                        <td>{{ $entry->category->name }}</td>
                                        @if ($entry->isExpenseCategory())
                                            <td style="color: red">{{ toBrMoney($entry->value) }}</td>
                                        @else
                                            <td style="color: blue">{{ toBrMoney($entry->value) }}</td>
                                        @endif
                                        
                                        <td class="table-actions">
                                            <div class="row">
                                                <a class="btn btn-info btn-sm edit" href="{{ route('account_entries.edit', $entry->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.edit') }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm delete" data-entry="{{ $entry->id }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
      </div>
@stop