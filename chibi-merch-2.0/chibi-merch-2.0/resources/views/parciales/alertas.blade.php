@if (session('exito'))
    <div class="alerta alerta--ok">{{ session('exito') }}</div>
@endif

@if (session('error'))
    <div class="alerta alerta--error">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alerta alerta--error">
        Revisa estos datos:
        <ul class="alerta__lista">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif