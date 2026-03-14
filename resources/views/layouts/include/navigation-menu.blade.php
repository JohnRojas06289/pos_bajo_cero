<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav" style="padding: 0.5rem 0 0.75rem;">

                {{-- ══ INICIO ══ --}}
                @can('ver-panel')
                <div class="sb-sidenav-menu-heading">Inicio</div>
                <x-nav.nav-link content='Panel'      icon='fas fa-chart-line'   :href="route('panel')" />
                @endcan

                {{-- ══ VENTAS ══ --}}
                @canany(['ver-venta','ver-caja','ver-movimiento'])
                <div class="sb-sidenav-menu-heading">Ventas</div>

                @can('crear-venta')
                <x-nav.nav-link content='POS'        icon='fas fa-cash-register' :href="route('ventas.create')" />
                @endcan

                @can('ver-venta')
                <x-nav.nav-link content='Historial'  icon='fas fa-receipt'       :href="route('ventas.index')" />
                @endcan

                @can('ver-caja')
                <x-nav.nav-link content='Cajas'      icon='fas fa-money-bill-wave' :href="route('cajas.index')" />
                @endcan

                @can('ver-movimiento')
                <x-nav.nav-link content='Movimientos' icon='fas fa-exchange-alt'  :href="route('movimientos.index')" />
                @endcan
                @endcanany

                {{-- ══ INVENTARIO ══ --}}
                @canany(['ver-producto','ver-inventario','ver-kardex','ver-compra'])
                <div class="sb-sidenav-menu-heading">Inventario</div>

                @can('ver-producto')
                <x-nav.nav-link content='Productos'  icon='fas fa-vest'           :href="route('productos.index')" />
                @endcan

                @can('ver-inventario')
                <x-nav.nav-link content='Inventario' icon='fas fa-warehouse'      :href="route('inventario.index')" />
                @endcan

                @can('ver-kardex')
                <x-nav.nav-link content='Kardex'     icon='fas fa-file-invoice'   :href="route('kardex.index')" />
                @endcan

                @can('ver-compra')
                <x-nav.link-collapsed id="collapseCompras" icon="fas fa-truck-ramp-box" content="Compras">
                    @can('ver-compra')
                    <x-nav.link-collapsed-item :href="route('compras.index')"  content="Ver historial" />
                    @endcan
                    @can('crear-compra')
                    <x-nav.link-collapsed-item :href="route('compras.create')" content="Nueva compra" />
                    @endcan
                </x-nav.link-collapsed>
                @endcan
                @endcanany

                {{-- ══ CATÁLOGO ══ --}}
                @canany(['ver-categoria','ver-marca','ver-presentacione'])
                <div class="sb-sidenav-menu-heading">Catálogo</div>

                @can('ver-categoria')
                <x-nav.nav-link content='Categorías'    icon='fas fa-tag'              :href="route('categorias.index')" />
                @endcan

                @can('ver-marca')
                <x-nav.nav-link content='Marcas'        icon='fas fa-trademark'         :href="route('marcas.index')" />
                @endcan

                @can('ver-presentacione')
                <x-nav.nav-link content='Tallas'        icon='fas fa-ruler-horizontal'  :href="route('presentaciones.index')" />
                @endcan
                @endcanany

                {{-- ══ PERSONAS ══ --}}
                @canany(['ver-cliente','ver-proveedore','ver-empleado'])
                <div class="sb-sidenav-menu-heading">Personas</div>

                @can('ver-cliente')
                <x-nav.nav-link content='Clientes'    icon='fas fa-users'              :href="route('clientes.index')" />
                @endcan

                @can('ver-proveedore')
                <x-nav.nav-link content='Proveedores' icon='fas fa-user-tie'           :href="route('proveedores.index')" />
                @endcan

                @can('ver-empleado')
                <x-nav.nav-link content='Empleados'   icon='fas fa-id-badge'           :href="route('empleados.index')" />
                @endcan
                @endcanany

                {{-- ══ ADMINISTRACIÓN ══ --}}
                @canany(['ver-empresa','ver-user','ver-role'])
                <div class="sb-sidenav-menu-heading">Admin</div>

                @can('ver-empresa')
                <x-nav.nav-link content='Empresa'   icon='fas fa-building'           :href="route('empresa.index')" />
                @endcan

                @can('ver-user')
                <x-nav.nav-link content='Usuarios'  icon='fas fa-user-circle'        :href="route('users.index')" />
                @endcan

                @can('ver-role')
                <x-nav.nav-link content='Roles'     icon='fas fa-shield-halved'      :href="route('roles.index')" />
                @endcan
                @endcanany

                {{-- ══ SISTEMA ══ --}}
                @can('ver-registro-actividad')
                <div class="sb-sidenav-menu-heading">Sistema</div>
                <x-nav.nav-link content='Actividad' icon='fas fa-history'  :href="route('activityLog.index')" />
                @endcan

                {{-- Theme toggle --}}
                <div class="theme-toggle-wrapper" style="margin-top: 0.5rem;">
                    <label class="theme-toggle-btn" for="themeToggle" title="Cambiar tema">
                        <i class="fas fa-sun toggle-icon" id="themeToggleIcon"></i>
                        <span id="themeToggleLabel" style="font-size:0.845rem; font-weight:500; color: var(--sidebar-text);">Tema claro</span>
                        <div class="toggle-switch" style="pointer-events:none;">
                            <input type="checkbox" id="themeToggle" style="pointer-events:auto;">
                            <span class="toggle-slider"></span>
                        </div>
                    </label>
                </div>

            </div>
        </div>

        {{-- Footer del sidebar --}}
        <div class="sb-sidenav-footer" style="padding: 0.75rem 0.625rem 1rem;">
            <div class="sidebar-user-footer">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->getRoleNames()->first() ?? 'Usuario' }}</div>
                </div>
            </div>
        </div>

    </nav>
</div>
