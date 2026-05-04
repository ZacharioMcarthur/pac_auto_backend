<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    public function getAllUser()
    {
        try {
            $users = User::with(['categorieUser', 'direction', 'role'])
                ->where('statut', 1)
                ->orderByDesc('created_at')
                ->get();

            return ApiResponse::success($users, 'Utilisateurs récupérés avec succès');
        } catch (Exception $ex) {
            Log::error('Erreur getAllUser : ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue.', 500);
        }
    }

    public function getUserById($userId)
    {
        try {
            $user = User::with(['categorieUser', 'direction', 'role'])->find($userId);

            if (!$user) {
                return ApiResponse::notFound('Utilisateur non trouvé.');
            }

            return ApiResponse::success($user, 'Utilisateur récupéré avec succès');
        } catch (Exception $ex) {
            Log::error('Erreur getUserById : ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue.', 500);
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
            return ApiResponse::validationError($validator->errors()->toArray());
        }

        try {
            $user = User::find($request->input('user_id'));

            if (!Hash::check($request->input('current_password'), $user->password)) {
                return ApiResponse::unauthorized('Mot de passe actuel incorrect.');
            }

            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            return ApiResponse::success(null, 'Mot de passe modifié avec succès.');
        } catch (Exception $ex) {
            Log::error('Erreur changePassword : ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue.', 500);
        }
    }

    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->toArray());
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

            return ApiResponse::success($user, 'Utilisateur modifié avec succès');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Erreur updateUser : ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue.', 500);
        }
    }

    public function deleteUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $user = User::find($request->input('user_id'));

            $user->update([
                'statut' => false
            ]);

            DB::commit();

            return ApiResponse::success(null, 'Utilisateur désactivé avec succès');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Erreur deleteUser : ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue.', 500);
        }
    }

    public function loadUserByEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->toArray());
        }

        try {
            $user = User::with(['role', 'demandeVehicule', 'direction', 'categorieUser'])
                ->where('email', $request->input('email'))
                ->first();

            if (!$user) {
                return ApiResponse::notFound('Utilisateur introuvable.');
            }

            return ApiResponse::success($user, 'Utilisateur récupéré avec succès');
        } catch (Exception $ex) {
            Log::error('Erreur loadUserByEmail : ' . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue.', 500);
        }
    }
}
