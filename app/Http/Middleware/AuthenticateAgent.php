<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAgent
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = trim((string) (
            $request->header('X-Agent-Token')
            ?: $request->bearerToken()
        ));

        if ($token === '') {
            return response()->json([
                'message' => 'Token agent tidak ditemukan.',
            ], 401);
        }

        $school = School::where('agent_token', $token)->first();

        if (! $school) {
            return response()->json([
                'message' => 'Token agent tidak valid.',
            ], 401);
        }

        if (! $school->status) {
            return response()->json([
                'message' => 'Agent sekolah ini sedang tidak aktif.',
            ], 403);
        }

        $request->attributes->set('agent_school', $school);

        return $next($request);
    }
}
