@extends('./../layouts/template')

@section('page-content')

<div class="row justify-content-center align-items-center" style="height: 100vh;">
    <div class="col-md-4"></div>
    <div class="col-md-4 mt-2">
        <div class="card">
            <div class="card-body">
            @if(session()->has('statut'))
                <li class="alert alert-danger">{{session()->get('statut')}}</li>
            @endif
            <form action="{{route('adminRegisterSubmit')}}" method="POST" class="form-product">
                <!-- il est important d'indiquer la methode post et egalement creer une route post
            et pour recuperer la valeur rentrée dans les champs par l'utilisateur on ajoute un type value -->
                @method('post')
                @csrf

                <h4>Créer un compte</h4>
                <div class="form-group">
                        <label for="nom">Nom :</label>
                        <input class="form-control mt-2" type="text" id="nom"
                        placeholder="Nom" name="nom" value="{{ old('nom') }}" >
                        @error('nom')
                        <div class="text text-danger">
                        {{$message}}
                        </div>
                        @enderror

                </div>
                <div class="form-group">
                        <label for="email">Email :</label>
                        <input class="form-control mt-2" type="text" id="email"
                        placeholder="Email" name="email" value="{{ old('email') }}" >
                        @error('email')
                        <div class="text text-danger">
                        {{$message}}
                        </div>
                        @enderror

                </div>
                <div class="form-group">
                        <label for="password">Mot de passe :</label>
                        <input class="form-control mt-2" type="password" id="password"
                        placeholder="Mot de passe" name="password" >
                        @error('password')
                        <div class="text text-danger">
                        {{$message}}
                        </div>
                        @enderror

                </div>
                <div class="form-group">
                        <label for="password_confirmation">Confirmer le mot de passe :</label>
                        <input class="form-control mt-2" type="password" id="password_confirmation"
                        placeholder="Confirmer le mot de passe" name="password_confirmation">
                        @error('password_confirmation')
                        <div class="alert-alert danger">
                        {{$message}}
                        </div>
                        @enderror

                </div>
                <button type="submit" class="btn btn-primary btn-sm mt-2">Inscription</button>
            </form>
            <p class="mt-2">Deja un compte ?  <a href="{{route('adminLogin')}}">Connectez-vous!</a></p>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>

</div>


  @endsection

