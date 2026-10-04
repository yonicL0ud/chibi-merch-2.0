@extends('layouts.app')

@section('titulo', 'Carrito - Chibi Merch')

@section('contenido')
    <section class="seccion">
        <h1 class="seccion__titulo">Tu carrito</h1>

        @if (count($detalle) == 0)
            <div class="vacio">
                <span aria-hidden="true">(,,◕⤙◕,,)</span>
                <p>Tu carrito está vacío.</p>
                <a class="boton boton--primario" href="{{ route('inicio') }}">Ver productos</a>
            </div>
        @else
            <div class="carrito">
                <div>
                    @foreach ($detalle as $fila)
                        <div class="item">
                            <img class="item__imagen" src="{{ asset($fila['producto']->imagen) }}"
                                 alt="{{ $fila['producto']->nombre }}">

                            <div class="item__datos">
                                <h2 class="item__nombre">{{ $fila['producto']->nombre }}</h2>
                                <p class="item__precio">
                                    S/ {{ number_format($fila['producto']->precio, 2) }} c/u
                                </p>
                            </div>

                            <div class="item__acciones">
                                <form method="POST" action="{{ route('carrito.actualizar') }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="producto_id" value="{{ $fila['producto']->id }}">
                                    <div class="item__controles">
                                        <input class="cantidad" type="number" name="cantidad"
                                               value="{{ $fila['cantidad'] }}" min="0"
                                               max="{{ $fila['producto']->stock }}">
                                    </div>
                                    <button class="boton boton--secundario" type="submit">Actualizar</button>
                                </form>

                                <form method="POST" action="{{ route('carrito.quitar', $fila['producto']->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="enlace-peligro" type="submit">Quitar</button>
                                </form>
                            </div>

                            <p class="item__subtotal">S/ {{ number_format($fila['subtotal'], 2) }}</p>
                        </div>
                    @endforeach
                </div>

                <aside class="carrito__resumen">
                    <h2>Resumen</h2>
                    <div class="resumen">
                        <div class="resumen__fila">
                            <span>Productos ({{ $unidades }})</span>
                            <span></span>
                        </div>
                        <div class="resumen__fila resumen__fila--total">
                            <span>Total</span>
                            <span>S/ {{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <div class="carrito__acciones">
                        <a class="boton boton--primario boton--ancho" href="{{ route('inicio') }}">
                            Seguir comprando
                        </a>

                        <form method="POST" action="{{ route('carrito.vaciar') }}"
                              onsubmit="return confirm('¿Vaciar el carrito?');">
                            @csrf
                            @method('DELETE')
                            <button class="boton boton--secundario boton--ancho" type="submit">
                                Vaciar carrito
                            </button>
                        </form>
                    </div>

                    <p class="carrito__nota">Compra simulada: no se piden datos de pago.</p>
                </aside>
            </div>
        @endif
    </section>
@endsection