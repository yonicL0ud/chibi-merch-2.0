<?php

namespace App\Services;

use App\Models\Producto;
use App\Repositories\ProductoRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * SERVICIO - ProductoService
 *
 * CAPA: servicio (capa de logica de negocio).
 *
 * RESPONSABILIDAD UNICA
 *   - Definir las reglas de validacion de un producto.
 *   - Aplicar la regla de negocio del campo `imagen`: si el usuario no
 *     sube ninguna foto, se asigna una imagen por defecto en lugar de dejar
 *     el campo vacio (la columna es NOT NULL y causaria error en MySQL).
 *   - Orquestar el repositorio para crear, actualizar y eliminar.
 *
 * QUE NO HACE
 *   - No escribe SQL (delega en ProductoRepository).
 *   - No devuelve vistas ni respuestas HTTP.
 *
 * COLABORADORES
 *   ProductoRepository -> persistencia y almacenamiento de imagenes.
 */
class ProductoService
{
    public function __construct(
        private readonly ProductoRepository $productos,
    ) {
    }

    /**
     * Reglas de validacion del formulario de producto.
     *
     * El segundo array traduce los nombres de campo al espanol para que
     * los mensajes de error se lea en el idioma del usuario.
     *
     * @return array<string, mixed>
     */
    public function reglas(): array
    {
        return [
            'nombre'      => 'required|string|max:120',
            'descripcion' => 'required|string|max:1000',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'imagen'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    /**
     * Nombres de los campos en espanol, para los mensajes de error.
     *
     * @return array<string, string>
     */
    public function nombresEnEspanol(): array
    {
        return [
            'nombre'      => 'nombre',
            'descripcion' => 'descripcion',
            'precio'      => 'precio',
            'stock'       => 'stock',
            'imagen'      => 'imagen',
        ];
    }

    /**
     * Valida la peticion y devuelve solo los campos permitidos.
     *
     * @return array<string, mixed>
     */
    public function validar(Request $request): array
    {
        return $request->validate(
            $this->reglas(),
            [],
            $this->nombresEnEspanol(),
        );
    }

    /**
     * Crea un producto aplicando la regla del campo imagen.
     */
    public function crear(Request $request): Producto
    {
        $datos = $this->validar($request);

        // REGLA DE NEGOCIO: siempre debe haber una imagen.
        $datos['imagen'] = $this->resolverImagen(
            $request->file('imagen'),
            ProductoRepository::IMAGEN_POR_DEFECTO,
        );

        return $this->productos->crear($datos);
    }

    /**
     * Actualiza un producto. Si no se sube imagen nueva, conserva la
     * imagen que ya tenia.
     */
    public function actualizar(Request $request, Producto $producto): Producto
    {
        $datos = $this->validar($request);

        $imagen = $request->file('imagen');

        // En una edicion, si no hay imagen nueva se mantiene la actual.
        if ($imagen) {
            $datos['imagen'] = $this->resolverImagen($imagen, $producto->imagen);
        }

        return $this->productos->actualizar($producto, $datos);
    }

    public function eliminar(int $id): void
    {
        $this->productos->eliminar($id);
    }

    /**
     * Lista todos los productos para la vista de administracion.
     * No hay logica de negocio aqui: es una lectura simple que el
     * repositorio resuelve en una sola consulta.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Producto>
     */
    public function todos()
    {
        return $this->productos->todos();
    }

    public function buscar(int $id): Producto
    {
        return $this->productos->buscar($id);
    }

    /**
     * Decide que ruta de imagen se guarda.
     *
     * @param  string $porDefecto  Ruta a usar si el usuario no subio nada.
     */
    private function resolverImagen(?UploadedFile $imagen, string $porDefecto): string
    {
        if (!$imagen) {
            return $porDefecto;
        }

        return $this->productos->guardarImagen($imagen);
    }
}