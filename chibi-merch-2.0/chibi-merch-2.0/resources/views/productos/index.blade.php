@extends('layouts.app')

@section('titulo', 'Administrar productos - Chibi Merch')

@section('contenido')
    <section class="seccion">
        <h1 class="seccion__titulo">Administrar productos</h1>
        <p class="seccion__descripcion">CRUD de productos con Laravel.</p>

        <div class="formulario__acciones">
            <a class="boton boton--primario" href="{{ route('productos.create') }}">Nuevo producto</a>
            <a class="boton boton--secundario" href="{{ route('inicio') }}">Ver la tienda</a>
        </div>

        <div class="tabla-envoltura">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $producto)
                        <tr>
                            <td>
                                <img class="tabla__imagen" src="{{ asset($producto->imagen) }}"
                                     alt="{{ $producto->nombre }}">
                            </td>
                            <td>{{ $producto->nombre }}</td>
                            <td>S/ {{ number_format($producto->precio, 2) }}</td>
                            <td>{{ $producto->stock }}</td>
                            <td class="tabla__acciones">
                                <a href="{{ route('productos.edit', $producto->id) }}">Editar</a>

                                <form method="POST" action="{{ route('productos.destroy', $producto->id) }}"
                                      onsubmit="return confirm('¿Eliminar este producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="enlace-peligro" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection