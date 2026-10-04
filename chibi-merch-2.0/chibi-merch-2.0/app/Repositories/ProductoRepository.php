<?php

namespace App\Repositories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

/**
 * REPOSITORIO - ProductoRepository
 *
 * CAPA: acceso a datos (tercera capa de la arquitectura).
 *
 * RESPONSABILIDAD UNICA
 *   Hablar con la tabla `productos`: consultar, guardar, actualizar y
 *   borrar, ademas de almacenar archivos de imagen en disco.
 *
 * QUE NO HACE
 *   - No valida datos (eso lo hace ProductoService).
 *   - No decide reglas de negocio como "el stock no puede ser negativo".
 *   - No devuelve respuestas HTTP ni vistas.
 *
 * POR QUE EXISTE
 *   Separa "como se guarda" de "que es valido". Si manana cambio MySQL
 *   por otro motor, solo cambio esta clase y el resto del sistema sigue
 *   funcionando. Tambien permite reutilizar el mismo acceso a datos desde
 *   el catalogo, desde el CRUD y desde el carrito.
 */
class ProductoRepository
{
    /**
     * Devuelve todos los productos ordenados por id.
     *
     * @return Collection<int, Producto>
     */
    public function todos(): Collection
    {
        return Producto::orderBy('id')->get();
    }

    /**
     * Busca un producto por su id.
     *
     * findOrFail() lanza ModelNotFoundException (HTTP 404) si no existe,
     * de modo que el controlador no necesita comprobar nada.
     */
    public function buscar(int $id): Producto
    {
        return Producto::findOrFail($id);
    }

    /**
     * Crea un producto nuevo y lo devuelve ya guardado.
     *
     * @param  array<string, mixed> $datos
     */
    public function crear(array $datos): Producto
    {
        return Producto::create($datos);
    }

    /**
     * Actualiza un producto existente con los datos validados.
     *
     * @param  array<string, mixed> $datos
     */
    public function actualizar(Producto $producto, array $datos): Producto
    {
        $producto->update($datos);

        return $producto;
    }

    /**
     * Elimina un producto de la base de datos.
     * Las lineas de carrito asociadas se borran en cascada.
     */
    public function eliminar(int $id): void
    {
        Producto::findOrFail($id)->delete();
    }

/**
 * Guarda una imagen subida dentro de storage/app/public/productos y
 * devuelve la ruta publica lista para guardar en la columna `imagen`.
 *
 * `store()` devuelve algo como `productos/7Hk2a.jpg`, pero el archivo real
 * se publica en `/storage/productos/7Hk2a.jpg` porque `php artisan
 * storage:link` crea el enlace `public/storage`. Por eso se antepone
 * `storage/`: sin el, las vistas generarian una URL inexistente.
 *
 * Es infraestructura de almacenamiento, por eso vive en el
 * repositorio y no en el controlador.
 */
public function guardarImagen(UploadedFile $imagen): string
{
    return 'storage/' . $imagen->store('productos', 'public');
}

    /**
     * Rutas de imagenes que puede usar el sistema.
     * Las expone el repositorio porque son datos, no reglas de negocio.
     */
    public const IMAGEN_POR_DEFECTO = 'img/productos/sin-imagen.png';
}