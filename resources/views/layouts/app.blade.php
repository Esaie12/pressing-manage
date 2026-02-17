<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Pressing Platform')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        body { background: #f5f7fb; }
        .navbar-brand small { display:block; font-size:.7rem; letter-spacing:.08em; text-transform: uppercase; }
        .stat-card { border:0; border-radius: 1rem; }
        .toast-container { z-index: 2000; }
    </style>
</head>
<body>
@auth
@php
    $notifCount = 0;
    if(auth()->user()->role === \App\Models\User::ROLE_OWNER){
        $notifCount = \App\Models\Order::whereHas('agency', fn($q)=>$q->where('pressing_id', auth()->user()->pressing_id))
            ->where('status','ready')->count();
    } elseif(auth()->user()->role === \App\Models\User::ROLE_EMPLOYEE){
        $notifCount = \App\Models\Order::where('agency_id', auth()->user()->agency_id)->where('status','ready')->count();
    }

    $userNotifications = \App\Models\UserNotification::where('user_id', auth()->id())->latest()->limit(15)->get();
    $notifCount += \App\Models\UserNotification::where('user_id', auth()->id())->where('is_read', false)->count();
@endphp
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container-fluid container-xl">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <small class="text-primary">Pressing Platform</small>
            @yield('heading', 'Dashboard')
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @if(auth()->user()->role !== \App\Models\User::ROLE_OWNER)
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('dashboard') }}">Accueil</a></li>
                @endif

                @if(auth()->user()->role === \App\Models\User::ROLE_ADMIN)
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.ui.owners*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.ui.owners') }}">Propriétaires</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.ui.subscriptions*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.ui.subscriptions') }}">Abonnements</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.ui.pricing*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.ui.pricing') }}">Pricing</a></li>
                @endif

                @if(auth()->user()->role === \App\Models\User::ROLE_OWNER)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('owner.ui.orders*','owner.ui.invoices*','owner.ui.requests*') ? 'active fw-semibold' : '' }}" href="#" role="button" data-bs-toggle="dropdown">Opérations</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('owner.ui.orders') }}">Commandes</a></li>
                            <li><a class="dropdown-item" href="{{ route('owner.ui.invoices') }}">Factures</a></li>
                            <li><a class="dropdown-item" href="{{ route('owner.ui.requests') }}">Demandes employés</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('owner.ui.stats*','owner.ui.expenses*','owner.ui.pricing*') ? 'active fw-semibold' : '' }}" href="#" role="button" data-bs-toggle="dropdown">Pilotage</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('owner.ui.stats') }}">Statistiques</a></li>
                            <li><a class="dropdown-item" href="{{ route('owner.ui.expenses') }}">Dépenses</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('owner.ui.pricing*') ? 'active fw-semibold' : '' }}" href="{{ route('owner.ui.pricing') }}">Abonnement</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('owner.ui.settings*','owner.ui.agencies*','owner.ui.employees*','owner.ui.services*') ? 'active fw-semibold' : '' }}" href="#" role="button" data-bs-toggle="dropdown">Paramètres</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('owner.ui.settings') }}">Mon pressing</a></li>
                            <li><a class="dropdown-item" href="{{ route('owner.ui.agencies') }}">Mes agences</a></li>
                            <li><a class="dropdown-item" href="{{ route('owner.ui.employees') }}">Employés</a></li>
                            <li><a class="dropdown-item" href="{{ route('owner.ui.services') }}">Services</a></li>
                        </ul>
                    </li>
                @endif

                @if(auth()->user()->role === \App\Models\User::ROLE_EMPLOYEE)
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('employee.ui.orders*') ? 'active fw-semibold' : '' }}" href="{{ route('employee.ui.orders') }}">Mes commandes</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('employee.ui.invoices*') ? 'active fw-semibold' : '' }}" href="{{ route('employee.ui.invoices') }}">Mes factures</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('employee.ui.requests*') ? 'active fw-semibold' : '' }}" href="{{ route('employee.ui.requests') }}">Demandes</a></li>
                @endif
            </ul>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#notifModal">
                    🔔
                    @if($notifCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $notifCount }}</span>
                    @endif
                </button>

                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">Profil</a>

                <form method="POST" action="/logout" class="d-flex">
                    @csrf
                    <button class="btn btn-outline-danger" type="submit">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="modal fade" id="notifModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Notifications</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        @if($notifCount > 0)
          <p>Vous avez <strong>{{ $notifCount }}</strong> notification(s).</p>
        @else
          <p class="text-muted">Aucune nouvelle notification.</p>
        @endif

        <ul class="list-group mb-3">
            @forelse($userNotifications as $notification)
                <li class="list-group-item">
                    <div class="fw-semibold">{{ $notification->title }}</div>
                    <div class="small text-muted">{{ $notification->message }}</div>
                    <div class="small text-secondary">{{ $notification->created_at?->diffForHumans() }}</div>
                </li>
            @empty
                <li class="list-group-item text-muted">Aucune notification applicative.</li>
            @endforelse
        </ul>

        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('notifications.markAllRead') }}">@csrf<button class="btn btn-outline-primary btn-sm">Tout marquer lu</button></form>
            <form method="POST" action="{{ route('notifications.clearAll') }}" onsubmit="return confirm('Vider toutes vos notifications ?')">@csrf<button class="btn btn-outline-danger btn-sm">Vider toutes</button></form>
        </div>
      </div>
    </div>
  </div>
</div>
@endauth

<main class="container-xl py-4">
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @yield('content')
</main>

<div class="toast-container position-fixed top-0 end-0 p-3">
    @if(session('success'))<div class="toast align-items-center text-bg-success border-0 js-toast" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">{{ session('success') }}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>@endif
    @if(session('error'))<div class="toast align-items-center text-bg-danger border-0 js-toast" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">{{ session('error') }}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>@endif
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
window.initSelect2 = function(elements){ const $els=$(elements || '.js-select2'); $els.each(function(){ if(!$(this).hasClass('select2-hidden-accessible')) $(this).select2({theme:'bootstrap-5',width:'100%'}); }); }


function attachPasswordToggles(root=document){
    root.querySelectorAll('input[type="password"]').forEach((input)=>{
        if(input.dataset.toggleReady==='1') return;
        input.dataset.toggleReady='1';
        const group=document.createElement('div');
        group.className='input-group';
        input.parentNode.insertBefore(group,input);
        group.appendChild(input);
        const btn=document.createElement('button');
        btn.type='button';
        btn.className='btn btn-outline-secondary';
        btn.textContent='Afficher';
        btn.addEventListener('click',()=>{
            const isPwd=input.type==='password';
            input.type=isPwd?'text':'password';
            btn.textContent=isPwd?'Cacher':'Afficher';
        });
        group.appendChild(btn);
    });
}
attachPasswordToggles();

document.querySelectorAll('.js-toast').forEach((el)=>{ new bootstrap.Toast(el,{delay:3500}).show(); });
$(function(){ $('.datatable').DataTable({ pageLength:10, language:{ search:'Recherche:', lengthMenu:'Afficher _MENU_ lignes', info:'Affichage _START_ à _END_ sur _TOTAL_', paginate:{ previous:'Préc', next:'Suiv' }, emptyTable:'Aucune donnée disponible' } }); window.initSelect2(); });
</script>
</body>
</html>
