<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use RealRashid\SweetAlert\Facades\Alert;
use App\Repositories\Core\Eloquent\CardRepository;

class CardOwnerMiddleware
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
        if ($request->route()->hasParameter('card')) {
            $id = $request->route()->parameter('card');

            $card = (new CardRepository())->findById($id);
            
            if($card) {
                if($card->user_id === auth()->user()->id) {
                    return $next($request);
                }
            }

            if ($request->ajax()) {
                return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.cards.not_found')], Response::HTTP_NOT_FOUND);
            }

            Alert::error(__('global.invalid_request'), __('messages.cards.not_found'));

            return redirect()->route('cards.index');
        }

        return $next($request);
    }
}
