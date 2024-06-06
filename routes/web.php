<?php

use App\Http\Controllers\Web\adminController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

//il faut noter qu'il existe trois admins ,le type 0= une personne de la boite desirant connaitre
//les utulisateurs de l'application,le type 1=un admin avec des droits de voir les differentes transactions,les infos sur les utulisateurs
//le type 2=le super admin avec le droit de tout voir et meme modifier des informations sur les autres admins voir les supprimers


// Route pour afficher le formulaire de connexion de l'administrateur
Route::get('/', [adminController::class, 'login'])->name('adminLogin');

// Route pour soumettre le formulaire de connexion de l'administrateur
Route::post('/admin/login', [adminController::class, 'loginSubmit'])->name('adminLoginSubmit');

// Route pour afficher le formulaire d'inscription de l'administrateur
Route::get('/admin/register', [adminController::class, 'register'])->name('adminRegister');

// Route pour soumettre le formulaire d'inscription de l'administrateur
Route::post('/admin/register', [adminController::class, 'registerSubmit'])->name('adminRegisterSubmit');

// Route pour déconnecter l'administrateur
Route::post('/admin/logout', [adminController::class, 'adminLogout'])->name('adminLogout');

// Route pour afficher la vue de récupération de mot de passe de l'administrateur
Route::get('/admin/forgot-password', [adminController::class, 'forgotView'])->name('adminForgotView');

// Route pour envoyer l'e-mail de récupération de mot de passe à l'administrateur
Route::post('/admin/forgot-password', [adminController::class, 'forgotPwdEmail'])->name('adminForgotPwdEmail');

// Route pour afficher la vue de réinitialisation de mot de passe de l'administrateur
Route::get('/reset-password/{token}', [adminController::class, 'resetPwdView'])->name('adminResetPwdView');

// Route pour soumettre le formulaire de réinitialisation de mot de passe de l'administrateur
Route::post('/admin/reset-password', [adminController::class, 'resetPwdForm'])->name('adminResetPwdForm');

// Routes protégées nécessitant une authentification de l'administrateur
Route::middleware('auth:admin')->group(function () {
    // Route pour afficher le formulaire d'édition de l'admin par le super administrateur
    Route::get('/admin/users/{user}/edit', [DashboardController::class, 'edit'])->name('adminUserEdit');

    // Route pour mettre à jour les informations de l'admin par le super administrateur
    Route::put('/admin/users/{user}/update', [DashboardController::class, 'updateAdminUser'])->name('adminUserUpdate');

    // Route pour supprimer l'admin par le super administrateur
    Route::delete('/admin/delete-user/{id}', [DashboardController::class, 'destroyAdminUser'])->name('adminDeleteUser');

    // Route pour afficher la liste des transactions par l'administrateur
    Route::get('/admin/list-transactions', [DashboardController::class, 'listTransactions'])->name('adminListTransactions');

    // Route pour afficher la liste des utilisateurs par l'administrateur
    Route::get('/admin/list-users', [DashboardController::class, 'listeUtilisateurs'])->name('adminListUsers');

    // Route pour afficher la liste des administrateurs par le super administrateur
    Route::get('/admin/list-admins', [DashboardController::class, 'listeAdmins'])->name('adminListAdmins');
});
