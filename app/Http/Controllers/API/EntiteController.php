<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Entite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntiteController extends Controller
{
    /**
     * Lister toutes les entités avec leurs relations
     */
    public function index()
    {
        $entites = Entite::with(['parent', 'enfants', 'utilisateurs'])->get();

        return response()->json([
            'success' => true,
            'data' => $entites
        ]);
    }

    /**
     * Créer une nouvelle entité
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'code' => 'required|string|unique:entites,code',
            'type' => 'required|in:direction,departement,service,bureau',
            'parent_id' => 'nullable|exists:entites,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérification si parent_id == soi-même (invalide pour création)
        if ($request->parent_id && intval($request->parent_id) === intval($request->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Une entité ne peut pas être son propre parent.'
            ], 422);
        }

        $entite = Entite::create($request->only(['nom', 'code', 'type', 'parent_id']));

        return response()->json([
            'success' => true,
            'data' => $entite
        ], 201);
    }

    /**
     * Afficher une entité spécifique
     */
    public function show($id)
    {
        $entite = Entite::with(['parent', 'enfants', 'utilisateurs'])->find($id);

        if (!$entite) {
            return response()->json(['success' => false, 'message' => 'Entité non trouvée.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $entite
        ]);
    }

    /**
     * Mettre à jour une entité
     */
    public function update(Request $request, $id)
    {
        $entite = Entite::find($id);

        if (!$entite) {
            return response()->json(['success' => false, 'message' => 'Entité non trouvée.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string',
            'code' => 'sometimes|string|unique:entites,code,' . $id,
            'type' => 'sometimes|in:direction,departement,service,bureau',
            'parent_id' => 'nullable|exists:entites,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Vérification si parent_id == soi-même (invalide)
        if ($request->filled('parent_id') && intval($request->parent_id) === intval($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Une entité ne peut pas être son propre parent.'
            ], 422);
        }

        $entite->update($request->only(['nom', 'code', 'type', 'parent_id']));

        return response()->json([
            'success' => true,
            'data' => $entite
        ]);
    }

    /**
     * Supprimer une entité
     */
    public function destroy($id)
    {
        $entite = Entite::find($id);

        if (!$entite) {
            return response()->json(['success' => false, 'message' => 'Entité non trouvée.'], 404);
        }

        $entite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Entité supprimée avec succès.'
        ]);
    }
}
