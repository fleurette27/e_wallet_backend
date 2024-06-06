<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\AdminUser;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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

    public function registerSubmit(AdminUserRequest $request)
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

        //pourquoi preciser le guard ,tout simplement parceque j'ai creer une autre tables de connexion en dehors
        //de users soit admin_users pour separer les administrateurs des utulisateurs de l'application donc
        //il est primordiale de preciser le guard('admin') car celui par defaut est web et il correspond a la tables users
        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            if ($user->type === 0) {
                return redirect()->route('adminListTransactions');
            } elseif ($user->type == 1) {
                return redirect()->route('adminListUsers');
            } else {
                return redirect()->route('adminListAdmins');
            }
        } else {
            return redirect()->back()->with('statut', "Informations de connexion non reconnues");
        }
    }





    public function loginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            if ($user->type === 0) {
                return redirect()->route('adminListTransactions');
            } elseif ($user->type == 1) {
                return redirect()->route('adminListUsers');
            } else {
                return redirect()->route('adminListAdmins');
            }
        } else {
            return redirect()->back()->with('statut', "Informations de connexion non reconnues");
        }
    }



    public function adminLogout(Request $request)
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
    $request->validate([
        'email' => 'required|email',
    ]);

    $user = AdminUser::where('email', $request->email)->first();

    if (!$user) {
        return back()->with('error', 'Cet email n\'existe pas dans notre système.');
    }

    $token = Password::getRepository()->create($user);

    // Envoyer l'email avec le lien de réinitialisation et le token

    Notification::send($user, new ResetPasswordNotification($token));

    return back()->with('success', 'Un email de réinitialisation de mot de passe a été envoyé à votre adresse email.');
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
            function ($user, $password) { // Utiliser $user générique pour l'utilisateur
                // Utiliser la classe AdminUser
                AdminUser::where('email', $user['email'])
                    ->update([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60)
                    ]);

                // Émettre un événement de réinitialisation de mot de passe
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('adminLogin')->with('status', __($status))
            : back()->with('status', __($status)); // Utiliser 'status' au lieu de 'statuss'
    }

}
