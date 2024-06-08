<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\fedaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//le lien pour l'inscription
Route::post('/register', [AuthController::class, 'register']);
//le lien pour le login ,la connexion
Route::post('/login', [AuthController::class, 'login']);
//le lien pour la verification de l'otp envoyé dans la boite mail
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
//le lien pour renvoyer l'otp apres son expiration
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
//le lien pour l'email pour la reinitialisation du mot de passe
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
//le lien pour modifier le mot de passe
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

// //pour l'integration de fedapay mais cela à été annulé
// Route::post('/feda/transaction',[fedaController::class,'CreateTransaction']);

//on a acces a ces lien que lorsqu'on est connecté
Route::group(['middleware' => ['auth:sanctum']], function () {
    // recuperer les données de l'utulisateur connecté
    Route::get('/user', [AuthController::class, 'getUserData']);
    //je n'ai plus eu besoin de cet lien mais permet d'avoir les informations de l'utulisateur grace a l'email
    Route::get('/user/{email}', [AuthController::class, 'getUserDataByEmail']);
    //lien pour modifie son nom
    Route::put('/user/name/{userId}', [AuthController::class, 'updateName']);
    //lien pour modifier son mot de passe une fois connecté
    Route::put('/user/password/{userId}', [AuthController::class, 'updatePassword']);
    //pour modifier son email
    Route::put('/user/email/{userId}', [AuthController::class, 'updateEmail']);
    //pour modifier son numero de telephone
    Route::put('/user/phone-number/{userId}', [AuthController::class, 'updatePhoneNumber']);
    // la liste de toute les transactions
    Route::get('/transactions', [AuthController::class, 'getTransactions']);
    //la liste des 5 dernieres transactions
    Route::get('/recenteTransactions', [AuthController::class, 'getRecenteTransactions']);
    //lorsque le depot avec les agregateurs ont été un succes,on fait appel a ce lien pour enregistrer la transaction (depot)
    Route::post('/depot', [AuthController::class, 'makeDeposit']);
    //lorsque le retrait avec les agregateurs ont été un succes,on fait appel a ce lien pour enregistrer la transaction (retrait)
    Route::post('/retrait', [AuthController::class, 'makeWithdrawal']);
    //le lien pour effectuer des transferts entre utulisateur ainsi que l'enregistrement de la transaction(transfert)
    Route::post('/transfert', [AuthController::class, 'transferMoney']);

    // //le lien pour creer une transaction feda mais ce lien n'est plus utulisé
    // Route::post('/feda/retrait',[fedaController::class,'pay']);
    // //le lien pour creer un payout (retrait) feda mais ce lien n'est plus utulisé
    // Route::post('/feda/depot',[fedaController::class,'createAndCheckFedaTransaction']);

});
