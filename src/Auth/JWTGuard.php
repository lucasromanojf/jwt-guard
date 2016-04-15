<?php

namespace LucasRomano\JWTGuard\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use LucasRomano\JWTGuard\JWT\Token\CommonJWT;
use LucasRomano\JWTGuard\JWT\Token\RefreshJWT;
use LucasRomano\JWTGuard\JWT\JWTManager;

class JWTGuard implements Guard, JWTGuardInterface
{
    use GuardHelpers, JWTGuardTrait;

    public function __construct(UserProvider $provider, Request $request, JWTManager $jwtManager)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->jwtManager = $jwtManager;
        $this->inputKey = 'api_token';
    }

    public function getUserFromToken()
    {
        if ($this->token() instanceof CommonJWT && $this->validateToken() === true) {
            return $this->provider->retrieveById($this->token()->get()->user->id);
        }

        return null;
    }

    public function blacklistToken()
    {
        if (($this->token() instanceof CommonJWT || $this->token() instanceof RefreshJWT) && $this->validateToken() === true) {
            $this->token()->blacklist();
            return response()->json('Token is blacklisted.');
        }

        return response()->json('Unauthorized.', 401);
    }
}
