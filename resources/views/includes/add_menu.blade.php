<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" title="{{ __('global.add_menu') }}">
        <i class="fas fa-plus-circle"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 250px">
        <span class="dropdown-item dropdown-header">{{ __('global.add_menu') }}</span>
        <div class="dropdown-divider"></div>
        <a href="{{ route('invoice_entries.create') }}" class="dropdown-item">
            <i class="fas fa-money-check-alt"></i> {{ __('global.invoice_entry') }}
            <span class="float-right text-muted text-sm"><i class="fas fa-plus"></i></span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('account_entries.create') }}" class="dropdown-item">
            <i class="fas fa-money-bill-wave"></i> {{ __('global.account_entry') }}
            <span class="float-right text-muted text-sm"><i class="fas fa-plus"></i></span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('categories.create') }}" class="dropdown-item">
            <i class="fas fa-tags"></i> {{ __('global.category') }}
            <span class="float-right text-muted text-sm"><i class="fas fa-plus"></i></span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('accounts.transfer_store') }}" class="dropdown-item">
            <i class="fas fa-exchange-alt"></i> {{ __('global.bank_transfer') }}
            <span class="float-right text-muted text-sm"><i class="fas fa-plus"></i></span>
        </a>
    </div>
</li>