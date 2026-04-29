<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Page d'accueil (par défaut)
Route::get('/', function () {
    return view('welcome');
});

// 🔐 Route factice pour que Laravel puisse générer le lien de réinitialisation du mot de passe
// Elle est utilisée uniquement dans l'email envoyé par Password::sendResetLink()
Route::get('/reset-password/{token}', function ($token) {
    return response()->json([
        'message' => 'Lien de réinitialisation reçu',
        'token' => $token
    ]);
})->name('password.reset');
