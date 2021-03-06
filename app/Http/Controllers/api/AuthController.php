<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Carbon\Exceptions\Exception as ExceptionsException;
use Exception;
use Illuminate\Http\Request;
use Hash;
use Auth;
use JWTAuth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function store(Request $request)
    {

        try {
            $credentials = $request->only(['email', 'password']);

            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Email ou senha incorreto!'], 401);
            }

            return $this->respondWithToken($token);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro interno do servidor!'], 500);
        }
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Usuário deslogado com sucesso!']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
