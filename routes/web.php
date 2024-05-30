<?php

use App\Http\Controllers\Web\adminController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [adminController::class, 'login'])->name('adminLogin');
Route::post('/admin/login', [adminController::class, 'loginSubmit'])->name('adminLoginSubmit');
Route::get('/admin/register', [adminController::class, 'register'])->name('adminRegister');
Route::post('/admin/register', [adminController::class, 'registerSubmit'])->name('adminRegisterSubmit');
Route::post('/admin/logout', [adminController::class, 'adminLogout'])->name('adminLogout');
Route::get('/admin/forgot-password', [adminController::class, 'forgotView'])->name('adminForgotView');
Route::post('/admin/forgot-password', [adminController::class, 'forgotPwdEmail'])->name('adminForgotPwdEmail');
Route::get('/reset-password/{token}', [adminController::class, 'resetPwdView'])->name('adminResetPwdView');
Route::post('/admin/reset-password', [adminController::class, 'resetPwdForm'])->name('adminResetPwdForm');

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/users/{user}/edit', [DashboardController::class, 'edit'])->name('adminUserEdit');
    Route::put('/admin/users/{user}/update', [DashboardController::class, 'updateAdminUser'])->name('adminUserUpdate');
    Route::delete('/admin/delete-user/{id}', [DashboardController::class, 'destroyAdminUser'])->name('adminDeleteUser');
    Route::get('/admin/list-transactions', [DashboardController::class, 'listTransactions'])->name('adminListTransactions');
    Route::get('/admin/list-users', [DashboardController::class, 'listeUtilisateurs'])->name('adminListUsers');
    Route::get('/admin/list-admins', [DashboardController::class, 'listeAdmins'])->name('adminListAdmins');
});
