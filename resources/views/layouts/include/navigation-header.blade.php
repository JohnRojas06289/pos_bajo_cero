<?php
use App\Models\Empresa;
$empresa = Empresa::first();
if (!$empresa) {
    $empresa = (object)['nombre' => 'Bajo Cero'];
}
?>

<nav class="sb-topnav navbar navbar-expand navbar-dark">

    <!-- Brand -->
    <a class="navbar-brand ps-3 pe-2" href="{{ route('panel') }}">
        <img src="{{ asset('images/logo-bajo-cero.png') }}" alt="Bajo Cero" style="height:32px;width:auto;object-fit:contain;filter:brightness(0) invert(1);opacity:0.92;">
        <span class="ms-2">Bajo<span class="brand-accent">Cero</span></span>
    </a>

    <!-- Sidebar Toggle -->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
        <i class="fas fa-bars text-white opacity-75"></i>
    </button>

    <!-- Right nav -->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4 align-items-center gap-1">

        <!-- User Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
               id="navbarDropdown" href="#" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
                <span class="user-name-display d-none d-md-inline">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                @can('ver-perfil')
                <li>
                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                        <i class="fas fa-cog me-2" style="color: var(--info);"></i>Configuraciones
                    </a>
                </li>
                @endcan
                @can('ver-registro-actividad')
                <li>
                    <a class="dropdown-item" href="{{ route('activityLog.index') }}">
                        <i class="fas fa-history me-2" style="color: var(--info);"></i>Registro de actividad
                    </a>
                </li>
                @endcan
                <li><hr class="dropdown-divider" /></li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</nav>
