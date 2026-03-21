@if (session('success') && !request()->routeIs('ventas.create'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
        });
    });
</script>
@endif

@if (session('error') && !request()->routeIs('ventas.create'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ToastError = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        ToastError.fire({
            icon: 'error',
            title: "{{ session('error') }}"
        });
    });
</script>
@endif

@if (session('login'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let message = "{{ session('login') }}";
        Swal.fire(message);
    });
</script>
@endif
