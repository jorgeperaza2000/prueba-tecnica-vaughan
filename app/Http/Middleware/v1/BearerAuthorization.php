<?php

namespace App\Http\Middleware\v1;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class BearerAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->authorizationHeaderIsNotPresent($request)) {

            throw ValidationException::withMessages([
                'Authorization' => ['The Authorization header is missing.'],
            ]);
        }

        if ($this->authorizationHeaderNotContainBearer($request)) {

            throw ValidationException::withMessages([
                'Authorization' => ['The Authorization header is invalid.'],
            ]);
        }

        if ($this->isValidParentheses($request->header('Authorization'))) {

            throw ValidationException::withMessages([
                'Authorization' => ['The Authorization token is invalid.'],
            ]);
        }

        return $next($request);
    }

    private function authorizationHeaderIsNotPresent(Request $request): bool
    {

        return ! $request->hasHeader('Authorization');
    }

    private function authorizationHeaderNotContainBearer(Request $request): bool
    {

        return ! str_starts_with($request->header('Authorization'), 'Bearer ');
    }

    private function isValidParentheses(string $authorization): bool
    {
        $token = trim(str_replace('Bearer', '', $authorization));

        $allowedCharacters = [
            ')' => '(',
            '}' => '{',
            ']' => '[',
        ];

        $openingChars = [];
        foreach (str_split($token) as $char) {

            if (in_array($char, $allowedCharacters)) {

                $openingChars[] = $char;
            } elseif (isset($allowedCharacters[$char])) {

                if (array_pop($openingChars) !== $allowedCharacters[$char]) {

                    return true;
                }
            }
        }

        return ! empty($openingChars);
    }
}
