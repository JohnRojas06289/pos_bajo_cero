@extends('layouts.public')

@section('title', 'Bajo Cero | Nosotros')

@section('content')
<!-- Header -->
<header class="py-5 bg-black border-bottom border-dark">
    <div class="container px-5">
        <div class="text-center my-5">
            <h1 class="display-4 fw-bolder text-white mb-2">NUESTRA HISTORIA</h1>
            <p class="lead fw-normal text-muted mb-4">Más que una marca, somos un movimiento. Nacidos en las calles, diseñados para destacar.</p>
        </div>
    </div>
</header>

<!-- Story Section -->
<section class="py-5">
    <div class="container px-5 my-5">
        <div class="row gx-5 align-items-center">
            <div class="col-lg-6">
                <div class="card bg-dark border-secondary p-5 h-100 d-flex justify-content-center">
                    <div>
                        <h2 class="fw-bolder text-primary mb-4">EL ORIGEN</h2>
                        <p class="text-white-50 lead mb-4">Bajo Cero nació en 2020 con una misión clara: redefinir la moda urbana en Bogotá. Lo que comenzó como un pequeño proyecto de diseño de gorras personalizadas, se transformó rápidamente en una marca referente de estilo y calidad.</p>
                        <p class="text-white-50 lead mb-0">Nos inspiramos en la arquitectura de la ciudad, el arte callejero y la música para crear prendas que no solo visten, sino que expresan una identidad. Cada chaqueta y cada gorra cuenta una historia de resistencia y autenticidad.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <!-- Image Placeholder -->
                <div style="width: 100%; height: 500px; background-color: #222; border: 1px solid #333; position: relative; overflow: hidden;">
                    <i class="fas fa-city fa-10x" style="position: absolute; bottom: -20px; right: -20px; color: #111;"></i>
                    <div style="display: flex; height: 100%; align-items: center; justify-content: center; flex-direction: column;">
                         <i class="fas fa-camera-retro fa-4x text-muted mb-3"></i>
                         <span class="text-muted">FOTOGRAFÍA URBANA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-5 bg-darker" style="background-color: #050505;">
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder text-white">NUESTROS <span class="text-success">VALORES</span></h2>
        </div>
        <div class="row gx-5 text-center">
            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="card bg-dark border-secondary h-100 p-4">
                    <div class="card-body">
                         <i class="fas fa-gem fa-3x text-primary mb-3"></i>
                         <h4 class="text-white fw-bold">CALIDAD PREMIUM</h4>
                         <p class="text-white-50 mb-0">No escatimamos en detalles. Usamos los mejores materiales para que tus prendas duren años, no meses.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="card bg-dark border-secondary h-100 p-4">
                    <div class="card-body">
                         <i class="fas fa-paint-brush fa-3x text-warning mb-3"></i>
                         <h4 class="text-white fw-bold">DISEÑO ORIGINAL</h4>
                         <p class="text-white-50 mb-0">Cada colección es una obra de arte. Diseños propios creados por artistas locales.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card bg-dark border-secondary h-100 p-4">
                    <div class="card-body">
                         <i class="fas fa-users fa-3x text-info mb-3"></i>
                         <h4 class="text-white fw-bold">COMUNIDAD</h4>
                         <p class="text-white-50 mb-0">Somos más que clientes; somos una familia. Apoyamos el talento local y la cultura urbana.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section (Optional) -->
<section class="py-5">
    <div class="container px-5 my-5 text-center">
         <h2 class="fw-bolder text-white mb-5">EL EQUIPO</h2>
         <div class="row justify-content-center">
              <div class="col-lg-3 col-6 mb-5">
                  <div class="rounded-circle overflow-hidden mx-auto mb-3" style="width: 150px; height: 150px; background-color: #333; display: flex; align-items: center; justify-content: center;">
                       <i class="fas fa-user fa-3x text-muted"></i>
                  </div>
                  <h5 class="text-white">Jairo Rojas</h5>
                  <p class="text-primary small text-uppercase">Fundador & CEO</p>
              </div>
              <div class="col-lg-3 col-6 mb-5">
                  <div class="rounded-circle overflow-hidden mx-auto mb-3" style="width: 150px; height: 150px; background-color: #333; display: flex; align-items: center; justify-content: center;">
                       <i class="fas fa-user fa-3x text-muted"></i>
                  </div>
                  <h5 class="text-white">Equipo Diseño</h5>
                  <p class="text-primary small text-uppercase">Creativos</p>
              </div>
         </div>
    </div>
</section>
@endsection
