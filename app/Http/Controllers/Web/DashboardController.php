<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\AdminUser;
use App\Models\transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{

    public function listTransactions(Request $request)
    {
        $title = $request->input('title');
        $date = $request->input('date');

        $query = transaction::with('user'); // Utiliser la relation pour charger les utilisateurs associés

        if ($title) {
            $query->where('title', 'like', '%' . $title . '%');
        }

        if ($date) {
            $query->whereDate('date', $date);
        }

        $transactions = $query->latest()->paginate(10);

        return view('adminDashboard.transactionList', compact('transactions'));
    }




    public function listeUtilisateurs(Request $request)
    {
        $users = User::query();

        // Recherche par nom
        if ($request->has('name')) {
            $users->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Recherche par email
        if ($request->has('email')) {
            $users->where('email', 'like', '%' . $request->input('email') . '%');
        }

        // Recherche par numéro de compte
        if ($request->has('account_number')) {
            $users->where('account_number', 'like', '%' . $request->input('account_number') . '%');
        }

        // Récupérer les utilisateurs paginés
        $users = $users->latest()->paginate(10);

        return view('adminDashboard.userList', compact('users'));
    }



    public function listeAdmins(Request $request)
    {
        $admins = AdminUser::query();

        // Recherche par nom
        if ($request->has('name')) {
            $admins->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Recherche par email
        if ($request->has('email')) {
            $admins->where('email', 'like', '%' . $request->input('email') . '%');
        }

        // Récupérer les administrateurs paginés
        $admins = $admins->latest()->paginate(10);

        return view('adminDashboard.adminUserList', compact('admins'));
    }





    public function edit(AdminUser $user)
    {
        return view('adminUser.edit',compact('user'));
    }


    public function updateAdminUser(AdminUserRequest $request, AdminUser $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password=Hash::make($request->password);
            $user->save();
            return redirect()->back()->with('status', "Informations de connexions mises à jour");
    }

    public function destroyAdminUser($id)
    {
        $user = AdminUser::findOrFail($id);
        $user->delete();
        return redirect()->route('')->with('status', 'Utilisateur supprimé avec succès.');
    }


}
