<table class="table datatable">
    <thead>
        <th>{{ __('global.due_date') }}</th>
        @if (! isset($card))
            <th>{{ __('global.card') }}</th>
        @endif
        <th>{{ __('global.amount') }}</th>
        <th>{{ __('global.actions') }}</th>
    </thead>
    <tbody>
        @foreach ($open_invoices as $invoice)
            <tr>
                <td>{{ toBrDate($invoice->due_date) }}</td>
                @if (! isset($card))
                    <td>{{ $invoice->card->name }}</td>
                @endif
                <td style="color: red">{{ toBrMoney($invoice->amount) }}</td>
                <td>
                    <a class="btn btn-success btn-sm edit" href="{{ route('invoice_entries.index', [$invoice->id, $invoice->card->id]) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.view_entries') }}">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>