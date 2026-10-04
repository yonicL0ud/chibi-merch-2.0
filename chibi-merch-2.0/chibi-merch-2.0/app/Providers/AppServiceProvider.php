<?php

namespace App\Providers;

use App\Services\CarritoService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * PROVEEDOR DE SERVICIOS DE LA APLICACION - AppServiceProvider
 *
 * RESPONSABILIDAD
 *   Configurar los ajustes globales de la aplicacion y las tareas que
 *   deben ejecutarse en cada peticion:
 *     1. Corregir las URLs cuando la app corre detras de un proxy
 *        (Codespaces).
 *     2. Compartir el contador del carrito con todas las vistas.
 *
 * COMPONENTE: orquestacion de la capa de servicio.
 *   El view composer es el punto donde se conecta la capa de servicio con
 *   la capa de presentacion: pide el total de unidades al CarritoService y
 *   lo inyecta en el layout. De ese modo, el menu y la vista del carrito
 *   siempre muestran el mismo dato y no se duplica la consulta.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra los bindings del contenedor de servicios.
     */
    public function register(): void
    {
        //
    }

    /**
     * Ejecuta el arranque de la aplicacion.
     */
    public function boot(): void
    {
        // --- 1. URLs detras del proxy de Codespaces ---
        // Sin esto, Laravel genera enlaces a http://localhost:8000 y el
        // navegador recibe ERR_CONNECTION_REFUSED.
        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
        }

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // --- 2. Contador del carrito disponible en todas las vistas ---
        // View::composer se ejecuta cada vez que se renderiza el layout,
        // y adjunta la variable $unidadesCarrito sin que las vistas
        // tengan que consultar nada por su cuenta.
        View::composer('layouts.app', function ($view) {
            $view->with(
                'unidadesCarrito',
                app(CarritoService::class)->totalUnidades(
                    (string) session()->getId()
                ),
            );
        });
    }
}