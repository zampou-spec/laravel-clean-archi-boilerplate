<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function user(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function register(Request $request): JsonResponse
    {
        $input = $request->all();
        $reposne = ['result' => false];

        $validator = Validator::make($input, User::$validationRules['register'], [
            'last_name.required' => 'Le nom est obligatoire.',
            'first_name.required' => 'Le prénom est obligatoire.',
            'email.required' => "L'adresse email est obligatoire.",
            'password.required' => "Le mot de passe est obligatoire.",
            'country.required' => "Indicatif du pays est obligatoire.",
            'mobile_number.required' => "Numéro de téléphone est obligatoire.",

            'email.email' => "L'adresse email n'est pas valide.",
            'email.unique' => "L'adresse email est déjà utiliser",
            'mobile_number.unique' => "Numéro de téléphone est déjà utiliser",

            'password.confirmed' => "Mot de passe doit être confirmer",
        ]);

        if ($validator->fails()) {
            $reposne['error'] = $validator->errors();
        } else {
            try {
                User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'country' => $request->country,
                    'email' => $request->email,
                    'mobile_number' => $request->mobile_number,
                    'password' => $request->password,
                    'email_verified_at' => now()
                ]);

                $reposne = ['result' => true];
            } catch (\Exception $e) {
                $reposne['error'] = 'Something Went Wrong!!';
            }
        }

        return response()->json($reposne, 200);
    }

    public function login(Request $request): JsonResponse
    {
        if ($request->mode === 'email') {
            $credentials = ['email' => $request->id];
        } else {
            $credentials = ['mobile_number' => $request->id];
        }

        $credentials['password'] = $request->password;

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token): JsonResponse
    {
        $user = auth()->user()->toArray();

        return response()->json([
            ...$user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
