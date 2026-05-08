<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\API\VehiculeController as ApiVehiculeController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\MotifController;
use App\Http\Controllers\DemandeCourseController;
use App\Http\Controllers\PlanningGardeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\CritereDeNotationController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\JournalSmsController;
use App\Http\Controllers\API\StatistiqueController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\EntiteController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('auth/profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('dashboard/stats', [DashboardController::class, 'getDashboardStats']);

    Route::prefix('user')->group(function () {
        Route::get('all', [UserController::class, 'getAllUser']);
        Route::get('get/{userId}', [UserController::class, 'getUserById']);
        Route::post('update', [UserController::class, 'updateUser']);
        Route::post('delete', [UserController::class, 'deleteUser']);
        Route::post('changePassword', [UserController::class, 'changePassword']);
    });

    Route::prefix('vehicule')->group(function () {
        Route::get('list', [VehiculeController::class, 'getVehicules']);
        Route::get('type', [VehiculeController::class, 'getTypesVehicules']);
        Route::get('get/{vehiculeId}', [VehiculeController::class, 'getVehiculeById']);
        Route::post('save', [VehiculeController::class, 'saveVehicule']);
    });

    Route::prefix('demande')->group(function () {
        Route::post('save', [DemandeCourseController::class, 'saveDemande']);
        Route::get('list/{user_id}/{role}', [DemandeCourseController::class, 'listDemandeVehicule']);
        Route::get('get/{demandeId}', [DemandeCourseController::class, 'getDemandeCourseById']);
        Route::post('edit', [DemandeCourseController::class, 'editDemande']);
        Route::post('delete', [DemandeCourseController::class, 'deleteDemandeCourse']);
        Route::post('start/{demandeId}', [DemandeCourseController::class, 'demmarerCourse']);
        Route::post('close/{demandeId}', [DemandeCourseController::class, 'arreterCourse']);
        Route::get('en-cours/{user_id}/{role}', [DemandeCourseController::class, 'getDemandeCourseEnCour']);
        Route::get('notation-check/{user_id}', [DemandeCourseController::class, 'verifierNotation']);
    });

    Route::prefix('affectation')->group(function () {
        Route::get('list', [DemandeCourseController::class, 'getDemandeAffecte']);
        Route::post('save', [DemandeCourseController::class, 'affecterDemande']);
    });

    Route::apiResource('entites', EntiteController::class);
});
