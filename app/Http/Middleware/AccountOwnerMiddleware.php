<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use RealRashid\SweetAlert\Facades\Alert;
use App\Repositories\Core\Eloquent\AccountRepository;

class AccountOwnerMiddleware
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
        if ($request->route()->hasParameter('account')) {
            $id = $request->route()->parameter('account');

            $account = (new AccountRepository())->findById($id);
            
            if($account) {
                if($account->user_id === auth()->user()->id) {
                    return $next($request);
                }
            }

            if ($request->ajax()) {
                return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.accounts.not_found')], Response::HTTP_NOT_FOUND);
            }

            Alert::error(__('global.invalid_request'), __('messages.accounts.not_found'));

            return redirect()->route('accounts.index');
        }

        return $next($request);
    }
}
