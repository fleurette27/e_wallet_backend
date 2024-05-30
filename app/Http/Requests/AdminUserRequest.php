<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name'=> 'required|min: 3',
            'email'=> 'required|email|unique:users',
            'password'=>'required|min :8',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire ',
            'name.min' => 'Votre nom doit contenir au moins trois caracteres',
            'email.required' => "L'email est obligatoire ",
            'email.email'=> "Ceci n'est pas le format d'un email ",
            'email.unique'=> "Cet email est deja pris",
            'password.required'=>'Le mot de passe est obligatoire',
            'password.min'=>'Le mot de passe doit contenir minimum 8 caracteres',
        ];
    }

}
