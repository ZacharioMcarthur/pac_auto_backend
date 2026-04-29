<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Classe pour standardiser toutes les réponses API
 * 
 * Assure une cohérence dans le format JSON retourné à tous les clients
 * (mobile Flutter, frontend web, etc.)
 */
class ApiResponse
{
    /**
     * Réponse de succès
     * 
     * @param mixed $data Les données à retourner
     * @param string $message Message de succès (optionnel)
     * @param int $statusCode Code HTTP (200 par défaut)
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Opération réussie', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Réponse d'erreur de validation
     * 
     * @param array $errors Erreurs de validation
     * @param string $message Message d'erreur
     * @return JsonResponse
     */
    public static function validationError(array $errors, string $message = 'Erreur de validation'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status' => 422,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Réponse d'erreur générique
     * 
     * @param string $message Message d'erreur
     * @param int $statusCode Code HTTP (500 par défaut)
     * @param mixed $data Données supplémentaires (optionnel)
     * @return JsonResponse
     */
    public static function error(string $message = 'Une erreur est survenue', int $statusCode = 500, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Réponse non autorisée (401)
     * 
     * @param string $message
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Non autorisé'): JsonResponse
    {
        return self::error($message, 401);
    }

    /**
     * Ressource non trouvée (404)
     * 
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return self::error($message, 404);
    }

    /**
     * Accès interdit (403)
     * 
     * @param string $message
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Accès interdit'): JsonResponse
    {
        return self::error($message, 403);
    }

    /**
     * Créé avec succès (201)
     * 
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function created($data = null, string $message = 'Créé avec succès'): JsonResponse
    {
        return self::success($data, $message, 201);
    }
}
