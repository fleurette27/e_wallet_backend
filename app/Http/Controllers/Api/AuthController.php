<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
class AuthController extends Controller

{

    //fonction pour la connexion


    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::guard('web')->attempt($credentials)) {
            return response(['message' => 'Informations de connexion non reconnues.'], 403);
        }

        $user = User::where('email', $request->email)->first();

        // Si tu veux retourner le token d'authentification
        $token = $user->createToken('')->plainTextToken;
        return response([
            'user' => auth()->user(),
            'token' => $token,
        ], 200);
    }


    // Méthode pour générer un numéro de compte aléatoire de 8 chiffres
    private function generateAccountNumber()
    {
        $accountNumber = '';

        // Générer 8 chiffres aléatoires
        for ($i = 0; $i < 8; $i++) {
            $accountNumber .= rand(0, 9);
        }

        return $accountNumber;
    }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phoneNumber' => 'required|string',
        ]);

        $accountNumber = $this->generateAccountNumber();
        $otp = rand(100000, 999999);
        $otpExpiresAt = Carbon::now()->addMinutes(3);

        $user = new User([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phoneNumber' => $validated['phoneNumber'],
            'otp' => $otp,
            'otp_expires_at' => $otpExpiresAt,
        ]);

        $user->account_number = $accountNumber;
        $user->save();

        //envoie de l'otp a son adresse mail
        Mail::to($user->email)->send(new SendOtpMail($otp));

        // Récupération de l'utilisateur fraîchement enregistré pour la connexion automatique
        $userOnline = User::where('email', $request->email)->first();
        //la connexion avec l'id
        Auth::loginUsingId($userOnline->id);
        return response([
            'message' => 'Compte créé. Un OTP a été envoyé à votre adresse email.',
            'user' => $userOnline,
            'token' => $user->createToken('')->plainTextToken,

        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|integer',
        ]);

        $user = User::find($validated['user_id']);
        $otpExpiration = Carbon::parse($user->otp_expires_at);
        if ($otpExpiration->isPast()) {
            return response(['message' => 'OTP expiré.'], 403);
        }

        if ($user->otp == $validated['otp']) {
            Auth::login($user);
            $token = $user->createToken('')->plainTextToken;

            return response([
                'message' => 'OTP vérifié avec succès.',
                'user' => $user,
                'token' => $token,
            ], 200);
        } else {
            return response(['message' => 'OTP invalide.'], 403);
        }
    }

    public function resendOtp(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($validated['user_id']);
        $otp = rand(100000, 999999);
        $otpExpiresAt = Carbon::now()->addMinutes(3);

        $user->otp = $otp;
        $user->otp_expires_at = $otpExpiresAt;
        $user->save();

        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response(['message' => 'Un nouvel OTP a été envoyé à votre adresse email.'], 200);
    }


    public function sendResetLinkEmail(Request $request)
    {

        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Utulisateur introuvable'], 404);
        }

        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $link = "myapp://reset-password?token=" . $token;
        
        Mail::send('flutter.password_reset', ['link' => $link], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json(['message' => 'Lien de reinisialisation envoyé']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min=6',
        ]);

        $passwordReset = DB::table('password_resets')->where('token', $request->token)->first();
        if (!$passwordReset) {
            return response()->json(['message' => 'Token invalide'], 400);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Utulisateur introuvable'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('token', $request->token)->delete();

        return response()->json(['message' => 'Mot de passe modifié avec succès']);
    }


    public function getUserData()
    {
        return response([
            'user' => auth()->user(),
        ], 200);
    }

    public function getUserDataByEmail($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return response([
                'user' => $user,
            ]);
        } else {
            return response(['error' => 'Erreur', 'message' => 'Utilisateur non trouvé.']);
        }
    }

    public function updateName(Request $request, $userId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Mise à jour du nom de l'utilisateur dans la base de données
        User::where('id', $userId)->update(['name' => $request->name]);

        return response(['message' => 'Nom d\'utilisateur mis à jour avec succès'], 200);
    }



    // Mettre à jour le mot de passe de l'utilisateur

    public function updatePassword(Request $request, $userId)
    {
        $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:6|different:currentPassword',
            'newPasswordConfirmation' => 'required|string|same:newPassword',
        ]);

        // Récupérer l'utilisateur depuis la base de données
        $user = User::findOrFail($userId);

        // Vérifier si le mot de passe actuel correspond
        if (!Hash::check($request->currentPassword, $user->password)) {
            return response(['message' => 'L\'actuel mot de passe est incorrect'], 400);
        }

        // Mettre à jour le mot de passe de l'utilisateur
        $user->update(['password' => Hash::make($request->newPassword)]);

        return response([
            'message' => 'Mot de passe mis à jour avec succès',
        ], 200);
    }


    // Mettre à jour l'e-mail de l'utilisateur
    public function updateEmail(Request $request,  $userId)
    {

        $request->validate([
            'email' => 'required|email|unique:users,email,',
        ]);

        User::where('id', $userId)->update(['email' => $request->email]);

        return response([
            'message' => 'Adresse e-mail mise à jour avec succès',
        ], 200);
    }

    // Mettre à jour le numéro de téléphone de l'utilisateur
    public function updatePhoneNumber(Request $request, $userId)
    {

        $request->validate([
            'phoneNumber' => 'required|string',
        ]);

        User::where('id', $userId)->update(['phoneNumber' => $request->phoneNumber]);

        return response(['message' => 'Numéro de téléphone mis à jour avec succès'], 200);
    }



    public function getTransactions()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = auth()->id();

        // Récupérer toutes les transactions de l'utilisateur
        $userTransactions = Transaction::where('account_id', $userId)
            ->with('user:id,name,account_number') // Inclure le nom de l'utilisateur
            ->orderByDesc('date')
            ->get();

        // Vérifier si l'utilisateur a des transactions
        if ($userTransactions->isEmpty()) {
            return response(['message' => 'Aucune transaction trouvée pour cet utilisateur.'], 404);
        }

        return response(['transactions' => $userTransactions]);
    }


    public function getRecenteTransactions()
    {
        // Récupérer l'ID de l'utilisateur authentifié
        $userId = auth()->id();

        // Récupérer les 4 dernières transactions de l'utilisateur
        $userTransactions = Transaction::where('account_id', $userId)
            ->with('user:id,name,account_number') // Inclure le nom de l'utilisateur
            ->orderByDesc('date')
            ->take(5) // Limiter à 5 résultats
            ->get();

        // Vérifier si l'utilisateur a des transactions
        if ($userTransactions->isEmpty()) {
            return response(['message' => 'Aucune transaction trouvée pour cet utilisateur.'], 404);
        }

        return response(['transactions' => $userTransactions]);
    }


    // Méthode pour effectuer un dépôt

    public function makeDeposit(Request $request)
    {
        // Validation des données
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);


        // Récupérer l'utilisateur authentifié
        $id = Auth::id();
        $user = User::findOrFail($id);
        // Effectuer le dépôt
        try {
            $user->deposit($request->amount);
            return response(['message' => 'Dépôt effectué avec succès']);
        } catch (\InvalidArgumentException $e) {
            return response(['error' => 'Invalid Argument', 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    // Méthode pour effectuer un retrait
    public function makeWithdrawal(Request $request)
    {
        // Validation des données
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Récupérer l'utilisateur authentifié
        $id = Auth::id();
        $user = User::findOrFail($id);
        // Effectuer le retrait
        try {
            $user->withdraw($request->amount);
            return response(['message' => 'Retrait effectué avec succès']);
        } catch (\InvalidArgumentException $e) {
            return response(['error' => 'Invalid Argument', 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    // Méthode pour effectuer un transfert d'argent
    public function transferMoney(Request $request)
    {

        // Validation des données
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'recipient_email' => 'required|email|exists:users,email',
        ]);


        // Récupérer l'utilisateur authentifié
        $id = Auth::user()->id;
        $sender = User::findOrFail($id);
        // Récupérer le destinataire
        $recipient = User::where('email', $request->recipient_email)->first();

        // Effectuer le transfert
        try {
            $sender->transfer($request->amount, $recipient);
            return response(['message' => 'Transfert d\'argent effectué avec succès']);
        } catch (\InvalidArgumentException $e) {
            return response(['error' => 'Invalid Argument', 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
