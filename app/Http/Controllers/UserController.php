<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    /**
     * 1. Récupération de tous les utilisateurs actifs avec relations
     */
    public function getAllUser()
    {
        try {
            $users = User::with( ['categorieUser', 'direction', 'role'])
                ->where('statut', 1)
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'data' => $users,
                'message' => '',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error('Erreur getAllUser : ' . $ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ]);
        }
    }

    /**
     * 2. Récupération d’un utilisateur par son identifiant
     */
    public function getUserById($userId)
    {
        try {
            $user = User::with(['categorieUser', 'direction', 'role'])->find($userId);

            if (!$user) {
                return response()->json([
                    'error' => 'not_found',
                    'message' => 'Utilisateur non trouvé.',
                    'status' => 404
                ]);
            }

            return response()->json([
                'data' => $user,
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error('Erreur getUserById : ' . $ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ]);
        }
    }

    public function getUserByIdArray($userId)
    {
        return $this->getUserById($userId);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
            'new_password_confirmation' => 'sometimes|required_with:new_password|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'validation_error',
                'message' => $validator->errors(),
                'status' => 422
            ]);
        }

        try {
            $user = User::find($request->input('user_id'));

            if (!Hash::check($request->input('current_password'), $user->password)) {
                return response()->json([
                    'error' => 'invalid_password',
                    'message' => 'Mot de passe actuel incorrect.',
                    'status' => 401
                ], 401);
            }

            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            return response()->json([
                'message' => 'Mot de passe modifié avec succès.',
                'status' => 200
            ], 200);
        } catch (Exception $ex) {
            Log::error('Erreur changePassword : ' . $ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ]);
        }
    }

    /**
     * 3. Mise à jour des informations d’un utilisateur
     */
    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'validation_error',
                'message' => $validator->errors(),
                'status' => 422
            ]);
        }

        DB::beginTransaction();
        try {
            $user = User::find($request->input('user_id'));

            $user->update([
                'nom' => $request->input('nom'),
                'prenom' => $request->input('prenom'),
                'email' => $request->input('email')
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Utilisateur modifié avec succès',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Erreur updateUser : ' . $ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ]);
        }
    }

    /**
     * 4. Suppression logique (désactivation) d’un utilisateur
     */
    public function deleteUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'validation_error',
                'message' => $validator->errors(),
                'status' => 422
            ]);
        }

        DB::beginTransaction();
        try {
            $user = User::find($request->input('user_id'));

            $user->update([
                'statut' => false
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Utilisateur désactivé avec succès',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Erreur deleteUser : ' . $ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ]);
        }
    }

    /**
     * 5. Récupération d’un utilisateur par son email
     */
    public function loadUserByEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'validation_error',
                'message' => $validator->errors(),
                'status' => 422
            ]);
        }

        try {
            $user = User::with(['role', 'demandeVehicule', 'direction', 'categorieUser'])
                ->where('email', $request->input('email'))
                ->first();

            if (!$user) {
                return response()->json([
                    'error' => 'not_found',
                    'message' => 'Utilisateur introuvable.',
                    'status' => 404
                ]);
            }

            return response()->json([
                'data' => $user,
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error('Erreur loadUserByEmail : ' . $ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ]);
        }
    }
}
