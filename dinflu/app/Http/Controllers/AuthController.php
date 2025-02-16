<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * @OA\Info(title="D-Influ API", version="1.0")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Registrar um novo usuário",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Usuário registrado com sucesso"),
     *     @OA\Response(response=400, description="Erro na validação"),
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autenticar usuário e gerar token JWT",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Token JWT gerado"),
     *     @OA\Response(response=401, description="Credenciais inválidas"),
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Verifica se o parâmetro remember foi enviado
        $remember = $request->input('remember', false);

        // Tenta gerar o token
        if (!$token = Auth::attempt($credentials, ['remember' => $remember])) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Encerrar sessão do usuário",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logout realizado com sucesso"),
     * )
     */
    public function logout()
    {
        try {
            // Invalida o token JWT
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'Logout realizado com sucesso'], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Falha ao tentar fazer logout'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Obter dados do usuário autenticado",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Dados do usuário"),
     * )
     */
    public function me()
    {
        return response()->json(Auth::user());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
