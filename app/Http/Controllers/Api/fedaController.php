<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class fedaController extends Controller
{


    public function createAndCheckFedaTransaction(Request $request)
    {
        // Validation du montant de la transaction
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Récupérer l'utilisateur authentifié
        $id = Auth::id();
        $user = User::findOrFail($id);

        // Création de la transaction FedaPay
        \FedaPay\FedaPay::setApiKey('sk_live_XW6dfE4yb46GvVgCsTn_Gwgo');
        \FedaPay\FedaPay::setEnvironment('live');

        try {
            $transaction = \FedaPay\Transaction::create([
                "description" => "Depot",
                "amount" => $request->amount,
                "callback_url" => "http://localhost:8000",
                "currency" => [
                    "code" => "952"
                ],
                "mode" => "mtn",
                "customer" => [
                    "firstname" => $user->name,
                    "email" => $user->email,
                    "phone_number" => [
                        "number" => $user->phoneNumber,
                        "country" => "bj"
                    ]
                ]
            ]);

            $token = $transaction->generateToken();

            // Envoyer l'URL de la transaction à l'utilisateur
            $responseData = [
                'message' => 'URL de la transaction envoyée à l\'utilisateur',
                'url' => $token->url
            ];

            // Vérification du statut de la transaction
            $payoutStatus = \FedaPay\Transaction::retrieve($transaction->id);

            if ($payoutStatus->status == "approved") {
                // Effectuer le dépôt
                $user->deposit($request->amount);
                $responseData['message'] = 'Paiement effectué';
            } else {
                $responseData['message'] = 'Transaction en attente d\'approbation';
            }

            return response($responseData, 200);

        } catch (\InvalidArgumentException $e) {
            return response(['error' => 'Invalid Argument', 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }


    public function pay (Request $request){

        // Validation du montant de la transaction
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Récupérer l'utilisateur authentifié
        $id = Auth::id();
        $user = User::findOrFail($id);


        \FedaPay\FedaPay::setApiKey('sk_live_XW6dfE4yb46GvVgCsTn_Gwgo');
        \FedaPay\FedaPay::setEnvironment('live');
        try {
        $payout=\FedaPay\Payout::create([
            "description" => "Retrait",
            "amount" => $request->amount,
            "callback_url" => "http://localhost:8000",
            "currency" => [
                "code" => "952"
            ],
            "mode" => "mtn",
            "customer" => [
                "firstname" => $user->name,
                "email" => $user->email,
                // "phone_number" => [
                //     "number" => $user->phoneNumber,
                //     "country" => "bj"
                // ]
            ]
                ]);
        $payout->sendNow();

        $responseData = [
            'payout' => $payout
        ];

        // Vérification du statut de la transaction
        $payoutStatus = \FedaPay\Payout::retrieve($payout->id);

        if ($payoutStatus->status == "sent") {
            // Effectuer le dépôt
            $user->withdraw($request->amount);
            $responseData['message'] = 'Retrait effectué';
        } else {
            $responseData['message'] = 'Retrait en attente d\'approbation';
        }

        return response($responseData, 200);

    } catch (\InvalidArgumentException $e) {
        return response(['error' => 'Invalid Argument', 'message' => $e->getMessage()], 400);
    } catch (\Exception $e) {
        return response(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
    }

    }



    // public function createTransaction(Request $request)
    // {
    //     // Validation du montant de la transaction
    //     $request->validate([
    //         'amount' => 'required|numeric|min:0',
    //     ]);

    //     // Récupérer l'utilisateur authentifié
    //     $id = Auth::id();
    //     $user = User::findOrFail($id);

    //     // Création de la transaction FedaPay
    //     \FedaPay\FedaPay::setApiKey('sk_live_XW6dfE4yb46GvVgCsTn_Gwgo');
    //     \FedaPay\FedaPay::setEnvironment('live');

    //     try {
    //         $transaction = \FedaPay\Transaction::create([
    //             "description" => "Dépot",
    //             "amount" => $request->amount,
    //             "currency" => [
    //                 "code" => "952"
    //             ],
    //             "customer" => [
    //                 "firstname" => $user->name,
    //                 "email" => $user->email,
    //                 "phone_number" => [
    //                     "number" => $user->phoneNumber,
    //                     "country" => "bj"
    //                 ]
    //             ]
    //         ]);

    //         $token = $transaction->generateToken();

    //         return response([
    //             'url' => $token->url,
    //             'transaction_id' => $transaction->id,
    //             'transaction_amount'=>$transaction->amount,
    //         ], 200);
    //     } catch (\InvalidArgumentException $e) {
    //         return response(['error' => 'Invalid Argument', 'message' => $e->getMessage()], 400);
    //     } catch (\Exception $e) {
    //         return response(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
    //     }
    // }



    // public function getDetailTransaction(Request $request)
    // { // Validation de l'identifiant de la transaction
    //         $request->validate([
    //             'transaction_id' => 'required|integer',
    //         ]);

    //         try {
    //             // Récupération de l'ID de la transaction et du montant
    //             $transactionId = $request->transaction_id;
    //             $transactionAmount = $request->transaction_amount;

    //             // Récupération de la transaction
    //             $transaction = \FedaPay\Transaction::retrieve($transactionId);

    //             if ($transaction->status == "approved") {
    //                 // Récupérer l'utilisateur authentifié
    //                 $id = Auth::id();
    //                 $user = User::findOrFail($id);

    //                 // Effectuer le dépôt
    //                 $user->deposit($transactionAmount);

    //                 return response([
    //                     'message' => 'Paiement effectué',
    //                 ], 200);
    //             } else {
    //                 return response([
    //                     'message' => 'Transaction en attente ou autre statut',
    //                 ], 200);
    //             }
    //         } catch (\Exception $e) {
    //             return response(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
    //         }
    //     }



}
