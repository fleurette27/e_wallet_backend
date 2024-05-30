<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller

{
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
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'dob' => 'required|date',
        'phoneNumber' => 'required|string',
    ]);

    $accountNumber = $this->generateAccountNumber();

    $user = new User([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'dob' => $validated['dob'],
        'phoneNumber' => $validated['phoneNumber'],
    ]);
    $user->account_number=$accountNumber;
    $user->save();


    // Connexion automatique après la création du compte
    $credentials = $request->only('email', 'password');

    if (!Auth::guard('web')->attempt($credentials)) {
        return response(['message' => 'Informations de connexion non reconnues.'], 403);
    }

    // Récupération de l'utilisateur fraîchement enregistré
    $user = User::where('email', $request->email)->first();

    // Auth::loginUsingId($account->id);

    return response([
    'message' => 'Compte créé et connecté avec succès',
    'user' => $user,
    'token' => $user->createToken('')->plainTextToken,],200);
    }


    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::guard('web')->attempt($credentials)) {
                return response(['message' => 'Informations de connexion non reconnues.'], 403);
        }

        $user =User::where('email', $request->email)->first();

        // Si tu veux retourner le token d'authentification
        $token = $user->createToken('')->plainTextToken;
         return response([
            'user' => auth()->user(),
            'token' => $token,
        ], 200);

    }


    public function getUserData()
    {
        return response([
            'user' => auth()->user(),
        ], 200);
    }

    public function getUserDataByEmail($email)
    {
        $user= User::where('email', $email)->first();
        if ($user) {
            return response([
                'user'=> $user,
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
        $user->update(['password' =>Hash::make($request->newPassword)]);

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
        $user=User::findOrFail($id);
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
        $user=User::findOrFail($id);
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
        $sender=User::findOrFail($id);
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
