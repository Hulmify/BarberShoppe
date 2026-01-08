<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Shop;

class IdentifyShop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Check for Custom Domain Mapping
        // If the host is one of our platform domains, we should likely not be here or return null.
        // However, the route constraints should prevent this. 
        
        $shop = Shop::where('custom_domain', $host)->first();

        // Optional: Check for Subdomain (slug.app_domain)
        // This requires parsing the host against config('app.url')
        
        if (!$shop) {
            // Fallback: If we are in the wildcard route, checking logic might vary.
            // For now, abort if not found.
            abort(404, 'Shop not found for domain: ' . $host);
        }

        // Inject Shop into Request and View
        $request->attributes->set('shop', $shop);
        view()->share('currentShop', $shop);

        return $next($request);
    }
}
