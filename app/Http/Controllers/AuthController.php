<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Authentification utilisateur
     * 
     * POST /api/login
     * Body: { "email": "user@example.com", "password": "password" }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validation des paramètres
            $validator = Validator::make($request->all(), [
                'email' => 'email|required',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError(
                    $validator->errors()->toArray(),
                    'Paramètres invalides'
                );
            }

            // Tentative de connexion
            $loginData = $request->only('email', 'password');
            if (!auth()->attempt($loginData)) {
                return ApiResponse::unauthorized('Email ou mot de passe incorrect');
            }

            // Vérifier que l'utilisateur est actif
            if (auth()->user() === null) {
                return ApiResponse::unauthorized('Compte désactivé');
            }

            // Générer le token d'authentification
            $accessToken = auth()->user()->createToken('authToken')->accessToken;

            return ApiResponse::success([
                'user' => auth()->user(),
                'access_token' => $accessToken,
            ], 'Authentification réussie', 200);

        } catch (Exception $ex) {
            Log::error('Erreur lors de la connexion: ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }

    /**
     * Récupérer le profil de l'utilisateur connecté
     * 
     * GET /api/auth/profile
     * Headers: Authorization: Bearer {token}
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return ApiResponse::unauthorized('Utilisateur non authentifié');
            }

            return ApiResponse::success($user, 'Profil récupéré avec succès');

        } catch (Exception $ex) {
            Log::error('Erreur lors de la récupération du profil: ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }

    /**
     * Déconnexion (révoque le token)
     * 
     * POST /api/auth/logout
     * Headers: Authorization: Bearer {token}
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $user = auth()->user();
            
            if ($user) {
                // Révoquer tous les tokens de l'utilisateur
                $user->tokens()->delete();
            }

            return ApiResponse::success(null, 'Déconnecté avec succès');

        } catch (Exception $ex) {
            Log::error('Erreur lors de la déconnexion: ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }
}
