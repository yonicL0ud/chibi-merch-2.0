@extends('layouts.app')

@section('titulo', 'Contacto - Chibi Merch')

@section('contenido')
    <section class="seccion">
        <h1 class="seccion__titulo">Contacto</h1>
        <p class="seccion__descripcion">Escribenos si tienes dudas sobre un producto o tu pedido.</p>

        <form method="POST" action="{{ route('contacto.store') }}" class="formulario">
            @csrf

            <label class="campo">
                <span>Nombre</span>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required>
            </label>

            <label class="campo">
                <span>Correo</span>
                <input type="email" name="correo" value="{{ old('correo') }}" required>
            </label>

            <label class="campo">
                <span>Asunto</span>
                <input type="text" name="asunto" value="{{ old('asunto') }}" required>
            </label>

            <label class="campo">
                <span>Mensaje</span>
                <textarea name="mensaje" rows="5" required>{{ old('mensaje') }}</textarea>
            </label>

            <div class="formulario__acciones">
                <button class="boton boton--primario" type="submit">Enviar mensaje</button>
            </div>
        </form>
    </section>
@endsection