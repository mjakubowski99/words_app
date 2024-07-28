<?php

declare(strict_types=1);

namespace Mjakubowski\FirebaseAuth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Illuminate\Contracts\Auth\Authenticatable;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;

class FirebaseResolver implements Guard
{
    public function __construct(
        private readonly IdTokenVerifier $verifier,
    ) {}

    public function fromRequest(Request $request)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return null;
        }

        try {
            $firebase_token = $this->verifier->verifyIdToken($token);

            /** @var FirebaseAuthenticable $user */
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

    public function check()
    {
        throw new \Exception('Not implemented');
    }

    public function guest()
    {
        throw new \Exception('Not implemented');
    }

    public function user()
    {
        throw new \Exception('Not implemented');
    }

    public function id()
    {
        throw new \Exception('Not implemented');
    }

    public function validate(array $credentials = [])
    {
        throw new \Exception('Not implemented');
    }

    public function hasUser()
    {
        throw new \Exception('Not implemented');
    }

    public function setUser(Authenticatable $user)
    {
        throw new \Exception('Not implemented');
    }
}
