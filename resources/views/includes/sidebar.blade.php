<nav class="sidenav shadow-right sidenav-light">
    <div class="sidenav-menu">
        <div class="nav accordion" id="accordionSidenav">

            <div class="sidenav-menu-heading">USUARIOS</div>
            <a class="nav-link" href="{{ route('usuarios.index') }}">
                <div class="nav-link-icon"><i class="fas fa-users"></i></div>
                Listado
            </a>
            <a class="nav-link" href="{{ route('usuarios.create') }}">
                <div class="nav-link-icon"><i class="fas fa-user-plus"></i></div>
                Nuevo usuario
            </a>

            <div class="sidenav-menu-heading">CONFIGURAR</div>
            <a class="nav-link" href="{{ route('horarios.index') }}">
                <div class="nav-link-icon"><i class="fas fa-clock"></i></div>
                Horarios
            </a>

        </div>
    </div>

    {{-- Sidenav Footer --}}
    <div class="sidenav-footer">
        <div class="sidenav-footer-content">
            <a class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i data-feather="log-out"></i>
                {{ __('Logout') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
            <div class="sidenav-footer-subtitle">
                Usuario: <strong>{{ auth()->user()?->name }}</strong>
            </div>
        </div>
    </div>
</nav>