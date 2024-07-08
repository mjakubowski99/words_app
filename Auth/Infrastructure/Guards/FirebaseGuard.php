<?php

declare(strict_types=1);

namespace Auth\Infrastructure\Guards;

use Illuminate\Http\Request;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Auth\Infrastructure\Entities\FirebaseAuthenticable;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;

class FirebaseGuard
{
    public function __construct(
        private readonly IdTokenVerifier $verifier,
    ) {}

    public function user(Request $request): ?FirebaseAuthenticable
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return null;
        }

        try {
            $firebase_token = $this->verifier->verifyIdToken($token);

            $user = app(config('auth.providers.firebase.model'));

            if (!$user instanceof FirebaseAuthenticable) {
                throw new \UnexpectedValueException('Unsupported provider');
            }

            $user->resolveByClaims($firebase_token->payload())
                ->setFirebaseToken($token);

            return $user;
        } catch (\Exception $e) {
            if ($e instanceof IdTokenVerificationFailed) {
                if (str_contains($e->getMessage(), 'token is expired')) {
                    return null;
                }
            }

            if (config('app.debug')) {
                throw $e;
            }

            return null;
        }
    }
}
