<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use RealRashid\SweetAlert\Facades\Alert;
use App\Repositories\Core\Eloquent\CategoryRepository;

class CategoryOwnerMiddleware
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
        if ($request->route()->hasParameter('category')) {
            $id = $request->route()->parameter('category');

            $category = (new CategoryRepository())->findById($id);
            
            if($category) {
                if($category->user_id === auth()->user()->id) {
                    return $next($request);
                }
            }

            if ($request->ajax()) {
                return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.categories.not_found')], Response::HTTP_NOT_FOUND);
            }

            Alert::error(__('global.invalid_request'), __('messages.categories.not_found'));

            return redirect()->route('categories.index');
        }

        return $next($request);
    }
}
