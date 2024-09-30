<?php

declare(strict_types=1);

namespace Mjakubowski\FirebaseAuth;

use _PHPStan_c55d0e35f\Nette\Neon\Exception;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Kreait\Firebase\Factory;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Illuminate\Contracts\Auth\Authenticatable;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;

class FirebaseResolver implements Guard
{

    public function fromRequest(Request $request)
    {
        $exceptions = [];

        $project = config('firebase.default');

        $service_account_keys = [
            'credentials',
            'android_credentials',
        ];

        foreach ($service_account_keys as $service_account_key) {
            $service_account = base_path(config("firebase.projects.{$project}.{$service_account_key}"));

            $factory = (new Factory())->withServiceAccount($service_account);

            try {
                return $this->verifyToken($request, $factory);
            } catch (\Throwable $exception) {
                if ($exception instanceof IdTokenVerificationFailed) {
                    if (str_contains($exception->getMessage(), 'token is expired')) {
                        return null;
                    }
                }

                $exceptions[] = $exception;
            }
        }

        if (count($exceptions) === 0) {
            return null;
        }

        if (config('app.debug')) {
            throw $exceptions[count($exceptions) - 1];
        }

        return null;
    }

    private function verifyToken(Request $request, Factory $factory)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return null;
        }

        $firebase_token = $factory->createAuth()->verifyIdToken($token);

        /** @var FirebaseAuthenticable $user */
        $user = app(config('auth.providers.firebase.model'));

        $user->resolveByClaims($firebase_token->claims()->all())->setFirebaseToken($token);

        return $user;
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
