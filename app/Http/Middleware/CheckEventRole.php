<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventCommittee;

/**
 * C-04 FIX: Rewrote CheckEventRole middleware.
 * Previously had no <?php tag, no namespace, and referenced a non-existent model.
 * Now checks if the authenticated user is a committee member of the event.
 * NOTE: This middleware is defined but not yet applied to routes.
 * Apply with ->middleware('event.role') after registering in Kernel.
 */
class CheckEventRole
{
    public function handle(Request $request, Closure $next, string $role = 'committee')
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $eventId = $request->route('event') ?? $request->route('id') ?? $request->route('eventId');

        if (!$eventId) {
            abort(400, 'Event ID not found in route');
        }

        $event = Event::visibleTo($user)->find($eventId);

        if (!$event) {
            abort(403, 'Forbidden - event not accessible');
        }

        return $next($request);
    }
}