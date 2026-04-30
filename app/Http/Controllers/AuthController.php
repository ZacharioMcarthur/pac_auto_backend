<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Authentification utilisateur
     * 
     * POST /api/login
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError(
                    $validator->errors()->toArray(),
                    'Paramètres invalides'
                );
            }

            $credentials = $request->only(['email', 'password']);
            
            if (!Auth::attempt($credentials)) {
                return ApiResponse::unauthorized('Email ou mot de passe incorrect');
            }

            /** @var User $user */
            $user = Auth::user();

            if (!$user || $user->statut === 'inactif') {
                Auth::logout();
                return ApiResponse::unauthorized('Compte inexistant ou désactivé');
            }

            $accessToken = $user->createToken('parcauto_token')->plainTextToken;

            return ApiResponse::success([
                'user' => $user->load(['role', 'entite']),
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
            ], 'Authentification réussie', 200);

        } catch (Exception $ex) {
            Log::error('Erreur lors de la connexion : ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }

    /**
     * Récupérer le profil de l'utilisateur connecté
     * 
     * GET /api/auth/profile
     */
    public function profile()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user) {
                return ApiResponse::unauthorized('Utilisateur non authentifié');
            }

            return ApiResponse::success(
                $user->load(['role', 'entite']), 
                'Profil récupéré avec succès'
            );

        } catch (Exception $ex) {
            Log::error('Erreur lors de la récupération du profil : ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }

    /**
     * Déconnexion (révoque le token actuel)
     * 
     * POST /api/auth/logout
     */
    public function logout()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if ($user) {
                /** @var \Laravel\Sanctum\PersonalAccessToken $token */
                $token = $user->currentAccessToken();
                
                if ($token) {
                    $token->delete();
                }
            }

            return ApiResponse::success(null, 'Déconnecté avec succès');

        } catch (Exception $ex) {
            Log::error('Erreur lors de la déconnexion : ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }
}