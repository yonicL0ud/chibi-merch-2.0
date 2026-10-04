<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MODELO DE DATOS - Producto
 *
 * Representa una fila de la tabla `productos`: un articulo de la tienda.
 *
 * RESPONSABILIDAD
 *   - Describir la estructura de un producto.
 *   - Declarar los campos que pueden escribirse de forma masiva
 *     ($fillable), fragmentos de seguridad contra asignacion masiva.
 *   - Definir la relacion con las lineas de carrito del producto.
 *
 * @property int         $id
 * @property string      $nombre
 * @property string      $descripcion
 * @property float       $precio
 * @property int         $stock
 * @property string      $imagen
 * @property HasMany     $items
 */
class Producto extends Model
{
    /**
     * Campos permitidos en asignacion masiva.
     * Cualquier campo que no este aqui se descarta en silencio.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'imagen',
    ];

    /**
     * Relacion "uno a muchos": un producto puede estar en varios carritos.
     *
     * Gracias a onDelete('cascade') en la migracion, al borrar un producto
     * sus lineas de carrito se borran solas.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CarritoItem::class);
    }
}