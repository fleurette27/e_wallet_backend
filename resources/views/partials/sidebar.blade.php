<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-text mx-3">SeedPay</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <span>Gestion de l'administration</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if (Auth::user()->type == 0)
                    <a class="collapse-item" href="{{ route('adminListTransactions') }}"><i class="fa fa-currency-exchange"></i>Transactions</a>
                @endif
                @if (Auth::user()->type == 1)
                    <a class="collapse-item" href="{{ route('adminListTransactions') }}"><i class="fa fa-currency-exchange"></i>Transactions</a>
                    <a class="collapse-item" href="{{ route('adminListUsers') }}"><i class="fa fa-user mr-2"></i>Utilisateurs</a>
                @endif
                @if (Auth::user()->type == 2)
                    <a class="collapse-item" href="{{ route('adminListTransactions') }}"><i class="bi bi-currency-exchange"></i>Transactions</a>
                    <a class="collapse-item" href="{{ route('adminListUsers') }}"><i class="fa fa-user mr-2"></i>Utilisateurs</a>
                    <a class="collapse-item" href="{{ route('adminListAdmins') }}"><i class="fa fa-person-badge"></i>Admins</a>
                @endif
            </div>
        </div>
    </li>

    <li class="nav-item">
        <!-- Autre élément de navigation -->
    </li>
</ul>
