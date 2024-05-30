@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card">
        <div class="card-header py-3 d-flex">
            <h6 class="m-0 font-weight-bold text-primary">
                Utilisateurs
            </h6>
            <div class="ml-auto">
                <form method="GET" action="{{ route('adminListUsers') }}" class="form-inline">
                    <div class="form-group mb-2">
                        <label for="name" class="sr-only">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="{{ request('name') }}">
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="email" class="sr-only">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ request('email') }}">
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="account_number" class="sr-only">Numéro de compte</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Numéro de compte" value="{{ request('account_number') }}">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Rechercher</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Numéro de compte</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->PhoneNumber }}</td>
                            <td>{{ $user->account_number }}</td>
                            <td>{{ $user->balance }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Pas d'utilisateurs</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
