<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" title="{{ __('global.invoices') }}">
        <i class="fa fa-credit-card"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 320px">
        <h5 class="dropdown-item text-center">{{ __('global.invoices') }}</h5>
        <div class="dropdown-divider"></div>
        @foreach ($all_open_invoices as $key => $invoice)
            @if($key !== 'total')
                <a href="#" class="dropdown-item">
                    <div class="media">                
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                <b>
                                    {{ $key }}
                                </b>
                            </h3>
                            <p class="text-sm text-muted">{{ __('global.due_date') }}: {{ toBrDate($invoice->due_date) }}</p>
                            <p class="text-sm text-muted">{{ __('global.value') }}:  <span style="color: red">{{ toBrMoney($invoice->amount) }}</span></p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
            @endif
        @endforeach
        <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer text-center"><strong>Total</strong>: <span style="color: red">{{ toBrMoney($all_open_invoices['total']) }}</span></a>
        </div>
  </li>