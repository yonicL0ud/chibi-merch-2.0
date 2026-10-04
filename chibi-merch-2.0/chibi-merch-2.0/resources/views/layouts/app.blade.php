<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Chibi Merch')</title>
    <link rel="stylesheet" href="{{ asset('css/chibi-merch.css') }}">
</head>
<body>
    <header class="cabecera">
        <div class="cabecera__contenedor">
            <a class="marca" href="{{ route('inicio') }}">Chibi Merch</a>

            <button class="nav__toggle" id="nav__toggle" type="button" aria-label="Abrir menu">
                Menu
            </button>

            <nav class="nav" id="nav">
                <ul class="nav__lista">
                    <li><a href="{{ route('inicio') }}">Tienda</a></li>
                    <li><a href="{{ route('carrito.index') }}">Carrito ({{ $unidadesCarrito }})</a></li>
                    <li><a href="{{ route('contacto.index') }}">Contacto</a></li>
                    <li><a href="{{ route('productos.index') }}">Administrar productos</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="contenedor">
        @include('parciales.alertas')
        @yield('contenido')
    </main>

    <footer class="pie">
        <div class="contenedor">
            <p class="pie__marca">Chibi Merch</p>
            <p class="pie__texto">Proyecto academico de Programacion Web 2026 - Laravel + MySQL - Compra simulada</p>
        </div>
    </footer>

    <script src="{{ asset('js/chibi-merch.js') }}"></script>
</body>
</html>