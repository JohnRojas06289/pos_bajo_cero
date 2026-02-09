@extends('layouts.public')

@section('title', 'Bajo Cero | Contacto')

@section('content')
<div class="container px-5 mt-5 pt-5">
    <div class="text-center mb-5">
        <h1 class="fw-bolder text-white">CONTÁCTANOS</h1>
        <p class="lead fw-normal text-muted mb-0">Estamos aquí para ayudarte. Escríbenos y volveremos contigo.</p>
        <div style="width: 50px; height: 3px; background-color: var(--primary-color); margin: 20px auto;"></div>
    </div>

    <div class="row gx-5">
        <!-- Contact Info -->
        <div class="col-lg-5 mb-5 mb-lg-0">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-body p-5">
                    <h3 class="fw-bold text-white mb-4">INFORMACIÓN</h3>
                    
                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0 btn-neon" style="padding: 10px 15px; border-radius: 50%;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="fw-bold text-primary">UBICACIÓN</h5>
                            <p class="text-white-50">San Victorino, Bogotá D.C.<br>Calle 10 #10-25, Local 105</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0 btn-neon" style="padding: 10px 15px; border-radius: 50%;">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="fw-bold text-success">WHATSAPP</h5>
                            <p class="text-white-50">+57 300 123 4567<br>Atención rápida</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-shrink-0 btn-neon" style="padding: 10px 15px; border-radius: 50%;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="fw-bold text-primary">EMAIL</h5>
                            <p class="text-white-50">info@bajocero.com<br>soporte@bajocero.com</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 btn-neon" style="padding: 10px 15px; border-radius: 50%;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="fw-bold text-warning">HORARIO</h5>
                            <p class="text-white-50">Lunes - Sábado: 9:00 AM - 7:00 PM<br>Domingos: Cerrado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card bg-dark border-secondary p-4 p-md-5">
                <h3 class="fw-bold text-white mb-4">ENVÍANOS UN MENSAJE</h3>
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-dark" placeholder="Nombre Completo" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" class="form-control form-control-dark" placeholder="Correo Electrónico" required>
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control form-control-dark" placeholder="Asunto" required>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control form-control-dark" rows="5" placeholder="Tu mensaje..." required></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-neon w-100 py-3">ENVIAR MENSAJE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Map Placeholder -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-dark border-secondary overflow-hidden">
                <div style="width: 100%; height: 400px; background-color: #222; display: flex; align-items: center; justify-content: center; position: relative;">
                    <!-- Simulating darker map -->
                    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/e/ec/World_map_blank_without_borders.svg/2000px-World_map_blank_without_borders.svg.png'); opacity: 0.1;"></div>
                    <div class="text-center z-index-1">
                        <i class="fas fa-map-marked-alt fa-3x text-primary mb-3"></i>
                        <h4 class="text-white">UBICACIÓN EN EL MAPA</h4>
                        <p class="text-muted">(San Victorino, Bogotá)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
