<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Account;
use App\Models\AccountEntry;
use Illuminate\Http\Response;
use App\Services\AccountEntryService;
use RealRashid\SweetAlert\Facades\Alert;

class AccountEntryOwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->route()->hasParameter('account_entry')){  
            
            $entry_id = $request->route()->parameter('account_entry');

            $entry = (new AccountEntryService(new AccountEntry(), new Account()))->findById($entry_id);

            if($entry && $entry->user_id === auth()->user()->id) {
                return $next($request);
            }

            if ($request->ajax()) {
                return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.categories.not_found')], Response::HTTP_NOT_FOUND);
            }

            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('account_entries.index');
        }

        return $next($request);
    }
}
