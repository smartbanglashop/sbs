<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\IpBlock;
class IpFilter
{
    
    public function handle(Request $request, Closure $next)
    {
        $ipblock = IpBlock::where('ip_no',$request->getClientIp())->first();
        if($ipblock){
            abort(403, "We cannot complete your request right now due to network security checks. Technical Note ".$ipblock->reason);
        }
        return $next($request);
    }
}
