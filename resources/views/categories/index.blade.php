@extends('layouts.app')

@push('js')
    <script src="{{ asset('js/categories/index.js') }}"></script>
@endpush

@section('js')
    <script>
        var category_title = '{{ __('messages.ajax_title') }}';
        var category_text = '{{ __('messages.categories.ajax_text') }}';
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

@section('button-header')
    <a href="{{ route('categories.create') }}" class="float-sm-right btn btn-sm btn-success shadow-sm"><i class="fas fa-plus"></i> {{__("global.add")}}</a>
@endsection

@section('plugins.Datatables', true)

@section('plugins.Sweetalert2', true)

@section('content')
<div class="card">
    <div class="card-header d-flex p-0">
        <ul class="nav nav-pills ml-auto p-2">
            <li class="nav-item"><a id="incoming-tab" class="nav-link active" href="#incoming" data-toggle="tab">{{__("global.incoming")}}</a></li>
            <li class="nav-item"><a id="outgoing-tab" class="nav-link" href="#outgoing" data-toggle="tab">{{__("global.outgoing")}}</a></li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="incoming" role="tabpanel" aria-labelledby="incoming-tab">
                @if ($incoming->isNotEmpty())
                    <table class="table datatable">
                        <thead>
                            <th>{{ __('global.name') }}</th>
                            <th>{{ __('global.actions') }}</th>
                        </thead>
                        <tbody>
                            @foreach ($incoming as $category)
                                @include('categories.partials.status-table')
                            @endforeach 
                        </tbody>
                    </table>
                @else
                    <h5 style="margin-top:20px">{{__("messages.categories.not_found")}}</h5>
                @endif
            </div>
            <div class="tab-pane fade" id="outgoing" role="tabpanel" aria-labelledby="outgoing-tab">
                @if ($outgoing->isNotEmpty())
                    <table class="table datatable">
                        <thead>
                            <th>{{ __('global.name') }}</th>
                            <th>{{ __('global.actions') }}</th>
                        </thead>
                        <tbody>
                            @foreach ($outgoing as $category)
                                @include('categories.partials.status-table')
                            @endforeach 
                        </tbody>
                    </table>
                @else
                    <h5 style="margin-top:20px">{{__("messages.categories.not_found")}}</h5>
                @endif
            </div>
        </div>
    </div>
</div>
    
@stop
