@extends('layouts.app')

@section('titulo', 'Chibi Merch - Tienda')

@section('contenido')
    <section class="seccion">
        <h1 class="seccion__titulo">Productos kawaii</h1>
        <p class="seccion__descripcion">Todo lo que necesitas para tus estudios, con un toque chibi.</p>

        <div class="catalogo">
            @foreach ($productos as $producto)
                <article class="tarjeta">
                    <a href="{{ route('producto.mostrar', $producto->id) }}">
                        <img class="tarjeta__imagen" src="{{ asset($producto->imagen) }}"
                             alt="{{ $producto->nombre }}">
                    </a>
                    <div class="tarjeta__cuerpo">
                        <h2 class="tarjeta__nombre">{{ $producto->nombre }}</h2>
                        <p class="tarjeta__descripcion">
                            {{ \Illuminate\Support\Str::limit($producto->descripcion, 70) }}
                        </p>
                        <p class="tarjeta__precio">S/ {{ number_format($producto->precio, 2) }}</p>
                        <p class="tarjeta__stock">Disponibles: {{ $producto->stock }}</p>

                        @if ($producto->stock > 0)
                            <form method="POST" action="{{ route('carrito.agregar') }}">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <button class="boton boton--primario tarjeta__boton" type="submit">
                                    Agregar al carrito
                                </button>
                            </form>
                        @else
                            <p class="tarjeta__agotado">Agotado</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection