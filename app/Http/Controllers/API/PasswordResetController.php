<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password; // Pour gérer l'envoi et la validation de la reinitialisations
use Illuminate\Support\Facades\Hash;     // Pour hasher le nouveau mot de passe
use Illuminate\Support\Str;              // Pour générer un nouveau remember_token

class PasswordResetController extends Controller
{
    /**
     * Envoie un email avec un lien de réinitialisation du mot de passe
     */
    public function sendResetLink(Request $request)
    {
        // Validation de l'email obligatoire et format correct
        $request->validate(['email' => 'required|email']);

        // Envoi du lien de réinitialisation par Laravel
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Retourne un message JSON selon succès ou erreur
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }

    /**
     * Réinitialise le mot de passe avec le token reçu par email
     */
    public function reset(Request $request)
    {
        // Validation des données requises
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8', // password_confirmation attendu aussi
        ]);

        // Tentative de réinitialisation du mot de passe
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Mise à jour sécurisée du mot de passe et du remember_token
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        // Retour JSON selon succès ou échec
        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }
}
