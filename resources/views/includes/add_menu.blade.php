<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" title="{{ __('global.quick_menu') }}">
        <i class="fas fa-plus-circle"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">{{ __('global.quick_menu') }}</span>
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
        <span class="dropdown-item dropdown-header">{{ __('global.others') }}</span>
        <div class="dropdown-divider"></div>
        <a href="{{ route('categories.create') }}" class="dropdown-item">
            <i class="fas fa-tags"></i> {{ __('global.add_category') }}
            <span class="float-right text-muted text-sm"><i class="fas fa-plus"></i></span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('accounts.create') }}" class="dropdown-item">
            <i class="fas fa-university"></i> {{ __('global.add_account') }}
            <span class="float-right text-muted text-sm"><i class="fas fa-plus"></i></span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('cards.create') }}" class="dropdown-item">
            <i class="fa fa-credit-card"></i> {{ __('global.add_card') }}
            <span class="float-right text-muted text-sm"><i class="fas fa-plus"></i></span>
        </a>
    </div>
</li>