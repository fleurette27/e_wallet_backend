<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\transaction;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class adminController extends Controller
{
    public function login()
    {
        return view('adminUser.login');
    }

    public function register()
    {
        return view('adminUser.register');
    }

    public function registerSubmit(Request $request)
    {
        // Validation des données d'entrée

        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]
        );


        // Créer un nouvel utilisateur
        AdminUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Connecter l'utilisateur directement
        $credentials = $request->only('email', 'password');


        if (Auth::guard('admin')->attempt($credentials)) {

            $request->session()->regenerate();

            if (Auth::user()->type == 0) {
                return redirect()->route('');
            } elseif (Auth::user()->type == 1) {
                return redirect()->route('');
            } else {
                return redirect()->route('');
            }
        } else {
            return redirect()->back()->with('status', "Informations de connexions non reconnues");
        }
        // Rediriger vers une vue appropriée, par exemple vers le tableau de bord
        return redirect()->route('')->with('status', 'Utilisateur enregistré et connecté avec succès');
    }





    public function loginSubmit(Request $request)
    {

        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );

        $input = $request->all();
        $data = [
            'email' => $input['email'],
            'password' => $input['password']
        ];

        // check if the given user exists in db
        if (Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']])) {

            $request->session()->regenerate();
            if (Auth::user()->type == 0) {
                return redirect()->route('');
            } elseif (Auth::user()->type == 1) {
                return redirect()->route('');
            } else {
                return redirect()->back()->with('status', "Informations de connexions non reconnues");
            }
        } else {
            return redirect()->route('')->with('status', "Utilisateur connecté avec succès");
        }
    }




    public function adminLogout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }




    public function forgotView()
    {
        return view('adminUser.forgotPassword');
    }




    public function forgotPwdEmail(Request $request)
    {

        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withstatuss(['email' => __($status)]);
    }




    public function resetPwdView(string $token)
    {
        return view(
            'adminUser.resetPassword',
            ['token' => $token]
        );
    }



    public function resetPwdForm(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (AdminUser $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withstatuss(['email' => [__($status)]]);
    }




    public function editName()
    {
        return view('');
    }




    public function updateName(Request $request, $userId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Mise à jour du nom de l'utilisateur dans la base de données
        AdminUser::where('id', $userId)->update(['name' => $request->name]);

        return redirect()->back()->with('status', "Modification éffectuée avec succès");
    }




    public function editEmail()
    {
        return view('');
    }





    public function updateEmail(Request $request,  $userId)
    {

        $request->validate([
            'email' => 'required|email|unique:users,email,',
        ]);

        AdminUser::where('id', $userId)->update(['email' => $request->email]);

        return redirect()->back()->with('status', "Modification éffectuée avec succès");
    }






    public function listTransactions(Request $request)
    {
        $title = $request->input('title');
        $date = $request->input('date');

        $query = transaction::query();

        if ($title) {
            $query->where('title', 'like', '%' . $title . '%');
        }

        if ($date) {
            $query->whereDate('date', $date);
        }

        $transactions = $query->latest()->paginate(10);

        return view('', compact('transactions'));
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

        return view('', compact(''));
    }




    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('')->with('success', 'Utilisateur supprimé avec succès.');
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

        return view('', compact('admins'));
    }
}
