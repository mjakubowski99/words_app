<?php

declare(strict_types=1);

namespace Mjakubowski\FirebaseAuth;

use Illuminate\Http\Request;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;
use Kreait\Firebase\JWT\IdTokenVerifier;

class FirebaseGuard
{
    public function __construct(
        private readonly IdTokenVerifier $verifier,
    ) {}

    public function user(Request $request)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return null;
        }

        try {
            $firebase_token = $this->verifier->verifyIdToken($token);

            $user = app(config('auth.providers.firebase.model'));

            $user->resolveByClaims($firebase_token->payload())->setFirebaseToken($token);

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
