<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MODELO DE DATOS - CarritoItem
 *
 * Representa una fila de la tabla `carrito_items`: una linea del carrito
 * de una sesion visitante.
 *
 * RESPONSABILIDAD
 *   - Describir la estructura de una linea de carrito (producto + cantidad).
 *   - Declarar que campos pueden escribirse de forma masiva ($fillable),
 *     para proteger los campos internos (id, created_at, updated_at).
 *   - Definir la relacion con el producto que se esta comprando.
 *
 * NOTA DE ARQUITECTURA
 *   Este modelo NO decide si el stock alcanza ni calcula totales.
 *   Esa logica de negocio vive en App\Services\CarritoService.
 *
 * @property int         $id
 * @property int         $producto_id
 * @property string|null $session_id
 * @property int         $cantidad
 * @property Producto    $producto
 */
class CarritoItem extends Model
{
    /**
     * Campos que si pueden guardarse con create() o update().
     * Eloquent ignora cualquier otro campo en asignacion masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'producto_id',
        'session_id',
        'cantidad',
    ];

    /**
     * Relacion "muchos a uno": una linea de carrito pertenece a un producto.
     *
     * Se usa with('producto') para traer los datos del producto en la
     * misma consulta y evitar el problema de consultas N+1.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}