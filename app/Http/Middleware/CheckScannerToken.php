<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckScannerToken
{
    private mixed $scannerToken;
    /**
     * Constructor for configs.
     */
    public function __construct()
    {
        $this->scannerToken = config('services.scanner.api_token');
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->hasHeader('scanner_token')) {
            return response()->json(['error'=>'Scanner  token is missing.'], 401);
        }

        $token = $request->header('scanner_token');

        if($token !== $this->scannerToken){
            return response()->json(['error'=>'Invalid Scanner token.'], 401);
        }
        
        return $next($request);
    }
}
