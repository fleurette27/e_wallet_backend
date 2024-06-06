@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card">
        <div class="card-header py-3 d-flex">
            <h6 class="m-0 font-weight-bold text-primary">
                Transactions
            </h6>
            <div class="ml-auto">
                <form method="GET" action="{{ route('adminListTransactions') }}" class="form-inline">
                    <div class="form-group mb-2">
                        <label for="title" class="sr-only">Recherche sur le Titre</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Titre" value="{{ request('title') }}">
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="date" class="sr-only">Recherche sur la Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
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
                            <th>Titre</th>
                            <th>Utilisateur</th>
                            <th>Numero_de_compte</th>
                            <th>Montant</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transaction->title }}</td>
                            <td>{{ $transaction->user->name }}</td>
                            <td>{{ $transaction->user->account_number }}</td>
                            <td>{{ $transaction->amount }}</td>
                            <td>{{ $transaction->date }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Pas de transactions</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
