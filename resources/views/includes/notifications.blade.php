<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" title="{{ __('global.notifications') }}">
        <i class="far fa-bell"></i>
        <span class="badge badge-info navbar-badge">{{ $count }}</span>
      </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 320px">
        <h5 class="dropdown-item text-center">{{ __('global.notifications') }}</h5>
        <div class="dropdown-divider"></div>
        @forelse ($unread_notifications as $notification)
            @foreach ($notification->data as $key => $item)
                <a href='{{ route('notifications.as_read', [$notification->id, $item['id']]) }}' class="dropdown-item">
                    <div class="media">                
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                <b>
                                    {{ __('global.' . $key ) }}
                                </b>
                            </h3>
                            <p class="text-sm text-muted">{{ __('global.description') }}: {{ $item['description'] }}</p>
                            <p class="text-sm text-muted">{{ __('global.due_date') }}: <b>{{ toBrDate($item['due_date']) }}</b></p>
                            <p class="text-sm text-muted">{{ __('global.value') }}:  <b>{{ toBrMoney($item['value']) }}</b></p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>                               
            @endforeach
            @empty
                <a href="" class="dropdown-item">
                    <div class="media">                
                        <div class="media-body">
                            <h3 class="dropdown-item-title text-center">
                                <b>
                                    {{ __('global.no_notification') }}
                                </b>
                            </h3>
                        </div>
                    </div>
                </a>
        @endforelse

        @if ($unread_notifications->isNotEmpty())
            <div class="dropdown-divider"></div>
                <a href="{{ route('notifications.all_read') }}" class="dropdown-item dropdown-footer text-center">{{__('global.all_read') }}</a>
            </div>
        @endif
        
  </li>