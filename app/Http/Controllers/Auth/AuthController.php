<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ResetCodePassword;
use Illuminate\Http\JsonResponse;
use App\Mail\SendCodeResetPassword;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);
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

        if (!$token = auth()->attempt(["email" => "admin@vamosavacilar.com", "password" => "password"])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $input = $request->all();
        $reposne = ['result' => false];

        $validator = Validator::make($input, [
            'email' => 'required|email|exists:users',
        ], [
            'email.required' => "L'adresse email est require.",
            'email.email' => "L'adresse email n'est pas valide.",
        ]);

        if ($validator->fails()) {
            $reposne['error'] = $validator->errors();
        } else {
            try {

                $resetCode = ResetCodePassword::where('email', $input['email'])->get();

                if (empty($resetCode)) {
                    ResetCodePassword::where('email', $input['email'])->delete();
                }

                $data['email'] = $input['email'];
                $data['code'] = mt_rand(100000, 999999);

                $codeData = ResetCodePassword::create($data);

                Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

                $reposne = ['result' => true];
            } catch (\Exception $e) {
                $reposne['error'] = 'Something Went Wrong!!';
            }
        }

        return response()->json($reposne, 200);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $input = $request->all();
        $reposne = ['result' => false];

        $validator = Validator::make($input, [
            'password' => 'required|confirmed',
            'code' => 'required|string|exists:reset_code_passwords',
        ], [
            'code.exists' => "Code invalide",
            'password.confirmed' => "Mot de passe doit être confirmer",
        ]);

        if ($validator->fails()) {
            $reposne['error'] = $validator->errors();
        } else {
            try {

                $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

                if ($passwordReset->created_at > now()->addHour()) {
                    $passwordReset->delete();
                    return response()->json([...$reposne, 'error' => 'Le code a expiré'], 200);
                }

                $user = User::firstWhere('email', $passwordReset->email);

                $user->update($request->only('password'));

                $passwordReset->delete();

                $reposne = ['result' => true];
            } catch (\Exception $e) {
                $reposne['error'] = 'Something Went Wrong!!';
            }
        }

        return response()->json($reposne, 200);
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
