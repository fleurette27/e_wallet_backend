<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use App\Models\transaction;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $table = 'users'; // Nom de la table dans la base de données

     protected $primaryKey = 'id'; // Clé primaire de la table

     public $incrementing = true; // Indique si la clé primaire est un nombre auto-incrémenté

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'phoneNumber',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function transactions()
    {
        return $this->hasMany(transaction::class);
    }


    public function deposit($amount)
    {
        // Assurez-vous que le montant déposé est positif
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Le montant du dépôt doit être supérieur à zéro.");
        }

        // Mettre à jour le solde du compte
        $this->balance += $amount;
        $this->save();

        // Ajouter une transaction de dépôt
        transaction::create([
            'title' => 'Dépôt',
            'amount' => $amount,
            'date' => now(),
            'account_id' =>$this->id,
        ]);
    }


    public function withdraw($amount)
    {
        // Assurez-vous que le montant retiré est positif et que le solde est suffisant
        if ($amount <= 0 || $this->balance < $amount) {
            throw new \InvalidArgumentException("Montant de retrait invalide ou solde insuffisant.");
        }

        // Mettre à jour le solde du compte
        $this->balance -= $amount;
        $this->save();

        // Ajouter une transaction de retrait
        transaction::create([
            'title' => 'Retrait',
            'amount' => -$amount,
            'date' => now(),
            'account_id' => $this->id,
        ]);
    }

    public function transfer($amount, User $recipient)
    {
        // Assurez-vous que le montant transféré est positif et que le solde est suffisant
        if ($amount <= 0 || $this->balance < $amount) {
            throw new \InvalidArgumentException("Montant de transfert invalide ou solde insuffisant.");
        }

        // Effectuer le transfert depuis le compte source vers le compte destinataire
        DB::transaction(function () use ($amount, $recipient) {
            // Retirer le montant du compte source
            $this->withdraw($amount);

            // Déposer le montant dans le compte destinataire
            $recipient->deposit($amount);

            // Ajouter une transaction de transfert sortant
            transaction::create([
                'title' => 'Transfert sortant',
                'amount' => -$amount,
                'date' => now(),
                'account_id' => $this->id,
            ]);

            // Ajouter une transaction de transfert entrant
            transaction::create([
                'title' => 'Transfert entrant',
                'amount' => $amount,
                'date' => now(),
                'account_id' => $recipient->id,
            ]);
        });
    }


}
