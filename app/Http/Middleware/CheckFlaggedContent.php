<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFlaggedContent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if we're viewing a post that's been flagged
        if ($request->route('post')) {
            $post = $request->route('post');
            if ($post->is_flagged && $post->user_id !== auth()->id()) {
                abort(404, 'Content not available');
            }
        }

        return $response;
    }
}
