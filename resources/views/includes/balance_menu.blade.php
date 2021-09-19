<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" title="{{ __('global.account_balance') }}">
        <i class="fas fa-dollar-sign"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 200px">
        <h5 class="dropdown-item text-center">{{ __('global.accounts') }}</h5>
        <div class="dropdown-divider"></div>
        @foreach ($all_account_balances['balances'] as $key => $balance)
            <a href="{{ route('accounts.entries', $balance['account_id']) }}" class="dropdown-item">
                <div class="media">                
                    <div class="media-body">
                        <h3 class="dropdown-item-title">
                            {{ $balance['account_name'] }}
                        </h3>
                        <p class="text-sm text-muted">Saldo:
                            <span style="color: {{ $balance['balance'] < 0 ? 'red' : 'blue' }}">
                                {{ toBrMoney($balance['balance']) }}
                            </span>                               
                        </p>
                    </div>
                </div>
            </a>
            <div class="dropdown-divider"></div>
        @endforeach      
        <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer text-center"><strong>Total</strong>: <span style="color: {{ $all_account_balances['total'] < 0 ? 'red' : 'blue' }}">{{ toBrMoney($all_account_balances['total']) }}</span></a>
        </div>
  </li>