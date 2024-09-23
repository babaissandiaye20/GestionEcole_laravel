<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\UserFirebaseController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReferentielController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/create_test', [FirebaseController::class, 'create']);
Route::get('/read_test', [FirebaseController::class, 'findAll']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [UserFirebaseController::class, 'createUser']);
Route::get('/users/{id}', [UserFirebaseController::class, 'getUserById']);
Route::put('/users/{id}', [UserFirebaseController::class, 'updateUser']);
Route::delete('/users/{id}', [UserFirebaseController::class, 'deleteUser']);
Route::get('/users/search', [UserFirebaseController::class, 'findUserByField']);
Route::get('/users', [UserFirebaseController::class, 'getAllUsers']);
Route::get('export-users', [UserFirebaseController::class, 'exportUsers']);
Route::post('/import-users', [UserFirebaseController::class, 'importUsers']);
Route::post('/login-firebase', [UserFirebaseController::class, 'authenticateWithCredentials']);
Route::put('user/{id}/apprenant', [UserFirebaseController::class, 'updateUserRoleToApprenant']);


// Routes pour le contrôleur ReferentielController
Route::prefix('referentiels')->group(function () {
    Route::post('/', [ReferentielController::class, 'store']);// Créer un référentiel
     Route::get('/', [ReferentielController::class, 'index']);
    Route::get('/{id}', [ReferentielController::class, 'show']); // Afficher un référentiel par ID
    Route::put('/{id}', [ReferentielController::class, 'update']); // Mettre à jour un référentiel par ID
    Route::delete('/{id}', [ReferentielController::class, 'destroy']); // Supprimer un référentiel par ID
});



Route::post('/promotions', [PromotionController::class, 'create']);
Route::put('/promotions/{id}/etat', [PromotionController::class, 'updateEtat']);
Route::get('/promotions/active', [PromotionController::class, 'getActive']);
Route::get('/promotions/{id}', [PromotionController::class, 'findById']);
Route::delete('/promotions/{id}', [PromotionController::class, 'delete']);
Route::put('/promotions/{id}/update', [PromotionController::class, 'updatePromotion']);
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
