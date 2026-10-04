<?php

namespace App\Repositories;

use App\Models\CarritoItem;
use Illuminate\Database\Eloquent\Collection;

/**
 * REPOSITORIO - CarritoRepository
 *
 * CAPA: acceso a datos (tercera capa de la arquitectura).
 *
 * RESPONSABILIDAD UNICA
 *   Hablar con la tabla `carrito_items`: leer las lineas de una sesion,
 *   crear o actualizar cantidades y vaciar el carrito.
 *
 * QUE NO HACE
 *   - No verifica si el stock alcanza (eso es logica de negocio y
 *     corresponde a CarritoService).
 *   - No calcula totales ni decide mensajes para el usuario.
 *
 * POR QUE EL CARRITO VIVE EN LA BASE DE DATOS Y NO EN LA SESION
 *   La sesion de PHP es temporal y se pierde al cerrar el navegador.
 *   Guardar el carrito en `carrito_items` permite:
 *     1. Que el carrito sobreviva a un cierre de pestana.
 *     2. Consultar y demostrar con SQL que el carrito realmente funciona.
 *     3. En el futuro, asociar el carrito a un usuario con una llave
 *        foranea, sin rehacer el sistema.
 */
class CarritoRepository
{
    /**
     * Devuelve las lineas de carrito de una sesion, con el producto
     * ya cargado para no disparar una consulta por cada linea.
     *
     * @return Collection<int, CarritoItem>
     */
    public function itemsDe(string $sessionId): Collection
    {
        return CarritoItem::with('producto')
            ->where('session_id', $sessionId)
            ->orderBy('id')
            ->get();
    }

    /**
     * Busca la linea de carrito de un producto concreto dentro de una
     * sesion. Devuelve null si el producto todavia no esta en el carrito.
     */
    public function buscarLinea(string $sessionId, int $productoId): ?CarritoItem
    {
        return CarritoItem::where('session_id', $sessionId)
            ->where('producto_id', $productoId)
            ->first();
    }

    /**
     * Crea una linea de carrito nueva.
     *
     * @return CarritoItem
     */
    public function crearLinea(string $sessionId, int $productoId, int $cantidad): CarritoItem
    {
        return CarritoItem::create([
            'session_id' => $sessionId,
            'producto_id' => $productoId,
            'cantidad'    => $cantidad,
        ]);
    }

    /**
     * Cambia la cantidad de una linea de carrito existente.
     */
    public function cambiarCantidad(CarritoItem $item, int $cantidad): CarritoItem
    {
        $item->cantidad = $cantidad;
        $item->save();

        return $item;
    }

    /**
     * Elimina una linea de carrito.
     */
    public function eliminarLinea(CarritoItem $item): void
    {
        $item->delete();
    }

    /**
     * Borra todas las lineas de carrito de una sesion.
     */
    public function vaciar(string $sessionId): void
    {
        CarritoItem::where('session_id', $sessionId)->delete();
    }

    /**
     * Suma total de unidades en el carrito de una sesion.
     * Se usa para el contador del menu y para el resumen.
     */
    public function totalUnidades(string $sessionId): int
    {
        return (int) CarritoItem::where('session_id', $sessionId)->sum('cantidad');
    }
}