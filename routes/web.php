<?php

use App\Http\Controllers\Web\adminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [adminController::class, 'login'])->name('adminLogin');
Route::post('/admin/login', [adminController::class, 'loginSubmit'])->name('adminLoginSubmit');
Route::get('/admin/register', [adminController::class, 'register'])->name('adminRegister');
Route::post('/admin/register', [adminController::class, 'registerSubmit'])->name('adminRegisterSubmit');
Route::post('/admin/logout', [adminController::class, 'adminLogout'])->name('adminLogout');
Route::get('/admin/forgot-password', [adminController::class, 'forgotView'])->name('adminForgotView');
Route::post('/admin/forgot-password', [adminController::class, 'forgotPwdEmail'])->name('adminForgotPwdEmail');
Route::get('/admin/reset-password/{token}', [adminController::class, 'resetPwdView'])->name('adminResetPwdView');
Route::post('/admin/reset-password', [adminController::class, 'resetPwdForm'])->name('adminResetPwdForm');

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/edit-name', [adminController::class, 'editName'])->name('adminEditName');
    Route::post('/admin/update-name/{userId}', [adminController::class, 'updateName'])->name('adminUpdateName');
    Route::get('/admin/edit-email', [adminController::class, 'editEmail'])->name('adminEditEmail');
    Route::post('/admin/update-email/{userId}', [adminController::class, 'updateEmail'])->name('adminUpdateEmail');
    Route::get('/admin/list-transactions', [adminController::class, 'listTransactions'])->name('adminListTransactions');
    Route::get('/admin/list-users', [adminController::class, 'listeUtilisateurs'])->name('adminListUsers');
    Route::delete('/admin/delete-user/{id}', [adminController::class, 'destroyUser'])->name('adminDeleteUser');
    Route::get('/admin/list-admins', [adminController::class, 'listeAdmins'])->name('adminListAdmins');
});
