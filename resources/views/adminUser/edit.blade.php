@extends('./../layouts/admin')

@section('content')
<div class="row justify-content-center align-items-center" style="height: 100vh;">
 <div class="col-md-4"></div>
    <div class="col-md-4 mt-2">
        <div class="card">
            <div class="card-body">
            @if(session()->has('statut'))
                <li class="alert alert-danger">{{session()->get('statut')}}</li>
            @endif

            <form action="{{route('adminUserUpdate',$user->id)}}" method="post" class="form-product">
                <!-- il est important d'indiquer la methode post et egalement creer une route post
            et pour recuperer la valeur rentrée dans les champs par l'utilisateur on ajoute un type value -->
                @method('PUT')
                @csrf

                <h4>Modifier les informations de l'utilisateur</h4>
                <div class="form-group">
                        <label for="nom">Nom :</label>
                        <input class="form-control mt-2" type="text" id="nom"
                        placeholder="Nom" name="name" value="{{ $user->name }}" >
                </div>

                <div class="form-group">
                        <label for="email">Email :</label>
                        <input class="form-control mt-2" type="text" id="email"
                        placeholder="Email" name="email" value="{{ $user->email }}" >
                </div>

                <div class="form-group">
                        <label for="password">Mot de passe :</label>
                        <input class="form-control mt-2" type="text" id="password"
                        placeholder="Mot de passe" name="password" value="*************" >
                </div>

                <button type=submit class="btn btn-primary btn-sm mt-2">Mettre à jour</button>
            </form>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>

@endsection

