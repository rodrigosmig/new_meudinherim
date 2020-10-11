<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Invoice;
use App\Models\InvoiceEntry;
use Illuminate\Http\Response;
use App\Services\InvoiceService;
use App\Services\InvoiceEntryService;
use RealRashid\SweetAlert\Facades\Alert;

class InvoiceEntryOwnerMiddleware
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
        if ($request->route()->hasParameter('invoice_id') 
            || $request->route()->hasParameter('entry_id')
            || $request->route()->hasParameter('card_id')
        ){  
            if ($request->route()->hasParameter('invoice_id')) {
                $invoice_id = $request->route()->parameter('invoice_id');
                $card_id    = $request->route()->parameter('card_id');

                $invoice = (new InvoiceService(new Invoice()))->findById($invoice_id);

                if($invoice->card_id == $card_id && $invoice->isOwner()) {
                    return $next($request);
                }
            }
            
            if ($request->route()->hasParameter('entry_id')) {
                $id = $request->route()->parameter('entry_id');
                
                $entry = (new InvoiceEntryService(new InvoiceEntry()))->findById($id);
                
                if($entry->isOwner()) {
                    return $next($request);
                }
            }

            if ($request->ajax()) {
                return response()
                    ->json(['title' => __('global.invalid_request'), 'text' => __('messages.entries.not_found')], Response::HTTP_NOT_FOUND);
            }

            Alert::error(__('global.invalid_request'), __('messages.not_found'));
            return redirect()->route('cards.index');
        }

        return $next($request);        
    }
}
