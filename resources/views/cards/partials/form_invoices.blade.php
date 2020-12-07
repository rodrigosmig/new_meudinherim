<table class="table datatable">
    <thead>
        <th>{{ __('global.closing_date') }}</th>
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
                <td>{{ toBrDate($invoice->closing_date) }}</td>
                <td>{{ toBrDate($invoice->due_date) }}</td>
                @if (! isset($card))
                    <td>{{ $invoice->card->name }}</td>
                @endif
                <td style="color: red">{{ toBrMoney($invoice->amount) }}</td>
                <td>
                    <a class="btn btn-success btn-sm edit" href="{{ route('invoice_entries.index', [$invoice->card->id, $invoice->id]) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.view_entries') }}">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    @if ($invoice->isClosed() && ! $invoice->payable)
                        <a class="btn btn-info btn-sm" href="{{ route('cards.invoices.generate-payment', [$invoice->id, $invoice->card->id]) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.generate_payment') }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </a>
                    @endif
                    @if ($invoice->payable && ! $invoice->isPaid())
                        <a class="btn btn-success btn-sm edit" href="{{ route('payables.show', $invoice->payable->id) }}" data-toggle="tooltip" data-placement="top" title="{{ __('global.pay') }}">
                            <i class="fas fa-money-bill-alt"></i>
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>