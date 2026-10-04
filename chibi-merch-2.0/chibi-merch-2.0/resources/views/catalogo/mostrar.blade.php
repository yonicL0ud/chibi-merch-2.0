@extends('layouts.app')

@section('titulo', $producto->nombre . ' - Chibi Merch')

@section('contenido')
    <section class="seccion">
        <a class="boton boton--secundario" href="{{ route('inicio') }}">Volver a la tienda</a>

        <div class="detalle">
            <img class="detalle__imagen" src="{{ asset($producto->imagen) }}" alt="{{ $producto->nombre }}">

            <div class="detalle__info">
                <h1 class="detalle__titulo">{{ $producto->nombre }}</h1>
                <p class="detalle__precio">S/ {{ number_format($producto->precio, 2) }}</p>
                <p class="detalle__texto">{{ $producto->descripcion }}</p>
                <p class="tarjeta__stock">Disponibles: {{ $producto->stock }}</p>

                @if ($producto->stock > 0)
                    <form method="POST" action="{{ route('carrito.agregar') }}" class="formulario">
                        @csrf
                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">

                        <label class="campo">
                            <span>Cantidad</span>
                            <input type="number" name="cantidad" value="1" min="1" max="{{ $producto->stock }}">
                        </label>

                        <button class="boton boton--primario" type="submit">Agregar al carrito</button>
                    </form>
                @else
                    <p class="tarjeta__agotado">Producto agotado</p>
                @endif
            </div>
        </div>
    </section>
@endsection