<?php

declare(strict_types=1);

namespace Auth\Infrastructure\Middleware;

use UseCases\User\Check;
use UseCases\User\Create;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Auth\Infrastructure\Entities\FirebaseAuthenticable;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * @param  Request                 $request
     * @throws AuthenticationException
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        if (!$this->isFirebaseGuard($guards)) {
            $this->authenticate($request, $guards);

            return $next($request);
        }

        $user = $request->user('firebase');

        if (!$user instanceof FirebaseAuthenticable) {
            $this->authenticate($request, $guards);

            return $next($request);
        }

        $this->createUserIfNeeded($user);

        $this->authenticate($request, $guards);

        return $next($request);
    }

    private function createUserIfNeeded(FirebaseAuthenticable $user): void
    {
        /** @var Check $check_user */
        $check_user = app()->make(Check::class);

        if (!$check_user->exists($user)) {
            /** @var Create $create_user */
            $create_user = app()->make(Create::class);
            $create_user->create($user);
        }
    }

    private function isFirebaseGuard($guards): bool
    {
        foreach ($guards as $guard) {
            if ($guard === 'firebase') {
                return true;
            }
        }

        return false;
    }
}
