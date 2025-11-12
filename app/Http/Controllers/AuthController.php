<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    /**
     * Register
     */
    public function register(AuthRegisterRequest $request) {
        try {
            //validation des données
            $request->validated();
            //creation de l'utilisateur
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            //renvoie de la reponse
            return response()->json($user, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Login
     */
    public function login(AuthLoginRequest $request) {
        try {
           //validation des données
            $request->validated();

            //recuperation des données
            $user = User::where('email', $request->email)->first();

            //verification des données
            if (!$user || Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => "l'eamil ou le mot de passe est incorrect"
                ]);
            }

            //creation du token de connexion
            $token = $user->createToken('auth_token')->plainTextToken;

            //retour de la reponse
            return response()->json([
                'status' => 200,
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (Exception $e) {
           return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Logout
     */
    public function Logout (Request $request) {
        try {
           //recuperation de l'utilisateur
            $user = $request->user()->currentAccessToken()->delete();
            //renvoi de la reponse
            return response()->json($user, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Info
     */
    public function me (Request $request){
        return response()->json($request->user());
    }
}
