@extends('layouts.app')

@section('title','Perfil')

@push('css')
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
@endpush

@section('content')

<div class="container-fluid">
    <h1 class="mt-1 mb-4 text-center">Configurar perfil</h1>

    <div class="card">
        <div class="card-header">
            <p class="lead fw-bold">Configure y personalize su perfil</p>
        </div>
        <form action="{{route('profile.update',['profile' => auth()->user() ])}}" method="POST">
            @method('PATCH')
            @csrf
            <div class="card-body">
                <div class="row g-4">

                    <!---Name--->
                    <div class="col-12">
                        <x-forms.input id='name'
                            required='true'
                            labelText='Nombre de usuario'
                            :defaultValue='auth()->user()->name' />
                    </div>

                    <!----Email--->
                    <div class="col-12">
                        <x-forms.input id='email'
                            required='true'
                            type='email'
                            labelText='Correo electrónico'
                            :defaultValue='auth()->user()->email' />
                    </div>

                    <!----Password--->
                    <div class="col-12">
                        <x-forms.input id='password'
                            type='password'
                            labelText='Nueva contraseña' />
                    </div>

                    @if(auth()->user()->hasRole('administrador'))
                    <!----POS Security Code--->
                    <div class="col-12">
                        <x-forms.input id='pos_code'
                            type='text'
                            labelText='Código de Seguridad POS (Supervisor)'
                            placeholder='Ej: 1602'
                            :defaultValue='auth()->user()->pos_code' />
                        <small class="text-muted">Este código se usará para autorizar cambios de precio en el punto de venta por parte de otros empleados.</small>
                    </div>
                    @endif

                </div>
            </div>

            <div class="card-footer">
                <div class="col text-center">
                    @can('editar-perfil')
                    <input class="btn btn-success" type="submit" value="Guardar cambios">
                    @endcan
                </div>
            </div>

        </form>
    </div>

</div>
@endsection

@push('js')

@endpush


