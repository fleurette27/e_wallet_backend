@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card">
        <div class="card-header py-3 d-flex">
            <h6 class="m-0 font-weight-bold text-primary">
                Administrateurs
            </h6>
            <div class="ml-auto">
                <form method="GET" action="{{ route('adminListAdmins') }}" class="form-inline">
                    <div class="form-group mb-2">
                        <label for="name" class="sr-only">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="{{ request('name') }}">
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="email" class="sr-only">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ request('email') }}">
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('adminUserEdit', $admin->id) }}" class="btn btn-info">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>
                                    <form onclick="return confirm('Êtes-vous sûr ? ')" class="d-inline" action="{{ route('adminDeleteUser', $admin->id) }}" method="POST">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Pas d'administrateurs</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $admins->links() }}
        </div>
    </div>
</div>
@endsection
