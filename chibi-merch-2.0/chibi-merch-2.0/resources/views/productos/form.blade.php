@extends('layouts.app')

@section('titulo', ($producto->exists ? 'Editar' : 'Nuevo') . ' producto - Chibi Merch')

@section('contenido')
    <section class="seccion">
        <h1 class="seccion__titulo">
            {{ $producto->exists ? 'Editar producto' : 'Nuevo producto' }}
        </h1>

        <form method="POST" enctype="multipart/form-data" class="formulario"
              action="{{ $producto->exists ? route('productos.update', $producto->id) : route('productos.store') }}">
            @csrf
            @if ($producto->exists)
                @method('PUT')
            @endif

            <label class="campo">
                <span>Nombre</span>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
            </label>

            <label class="campo">
                <span>Descripcion</span>
                <textarea name="descripcion" rows="4" required>{{ old('descripcion', $producto->descripcion) }}</textarea>
            </label>

            <label class="campo">
                <span>Precio (S/)</span>
                <input type="number" name="precio" step="0.01" min="0"
                       value="{{ old('precio', $producto->precio) }}" required>
            </label>

            <label class="campo">
                <span>Stock</span>
                <input type="number" name="stock" min="0"
                       value="{{ old('stock', $producto->stock ?? 0) }}" required>
            </label>

            <label class="campo">
                <span>Imagen</span>
                <input type="file" name="imagen" accept="image/*">
                <span class="campo__ayuda">Opcional. Si no eliges nada, se conserva la imagen actual.</span>
            </label>

            <div class="formulario__acciones">
                <button class="boton boton--primario" type="submit">Guardar</button>
                <a class="boton boton--secundario" href="{{ route('productos.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection