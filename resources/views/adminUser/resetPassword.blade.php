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

            <form action="{{route('adminResetPwdForm')}}" method="POST" class="form-product">
                <!-- il est important d'indiquer la methode post et egalement creer une route post
            et pour recuperer la valeur rentrée dans les champs par l'utilisateur on ajoute un type value -->
                @method('post')
                @csrf

                <h4>Reinitialiser votre mot de passe</h4>

                <div class="form-group">
                        <label for="email">Email :</label>
                        <input class="form-control mt-2" type="text" id="email"
                        placeholder="Email" name="email" value="{{ old('email') }}" >

                </div>
                <div class="form-group">
                        <label for="password">Nouveau mot de passe :</label>
                        <input class="form-control mt-2" type="text" id="password"
                        placeholder="password" name="password" value="{{ old('password') }}"  >

                </div>
                <div class="form-group">
                        <label for="password_confirmation"> Confirmer le Mot de passe :</label>
                        <input class="form-control mt-2" type="text" id="password_confirmation"
                        placeholder=" Confirmer le Mot de passe" name="password_confirmation"  >

                </div>
                <div class="form-group">
                        <input class="form-control mt-2" type="hidden"
                        name="token" value="{{ $token }}" >
                </div>

                <button type="submit" class="btn btn-primary btn-sm mt-2">Reinitialiser</button>
            </form>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>

</div>


  @endsection
