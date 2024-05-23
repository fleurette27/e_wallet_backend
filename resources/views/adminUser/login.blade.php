@extends('./../layouts/template.blade.php')

@section('page-content')
<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4 mt-2">
        <div class="card">
            <div class="card-body">
                @if(session()->has('error'))
                <li class="alert alert-danger">{{session()->get('error')}}</li>
                @endif

            <form action="{{route('adminLoginSubmit')}}" method="POST" class="form-product">
                <!-- il est important d'indiquer la methode post et egalement creer une route post
            et pour recuperer la valeur rentrée dans les champs par l'utilisateur on ajoute un type value -->
                @method('post')
                @csrf

                <h4>Connexion</h4>
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
                <button type=submit class="btn btn-primary btn-sm mt-2">Connexion</button>
            </form>
            <p class="mt-2">Mot de passe oublié ? <a href="{{route('adminForgotView')}}">Réninitialiser le mot de passe !</a></p>
            <p class="mt-2">Pas de compte ?  <a href="{{route('register')}}">Inscrivez_vous!</a></p>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>

</div>


  @endsection

