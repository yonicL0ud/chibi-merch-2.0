<?php

namespace App\Http\Controllers;

use App\Services\ProductoService;
use Illuminate\Http\Request;

/**
 * CONTROLADOR - ProductoController
 *
 * CAPA: presentacion (primera capa de la arquitectura).
 *
 * RESPONSABILIDAD UNICA
 *   Exponer el CRUD de productos. Recibe la peticion, la delega al
 *   ProductoService y devuelve la vista o la redireccion.
 *
 * QUE NO HACE
 *   - No define reglas de validacion: estan en ProductoService.
 *   - No decide que imagen se guarda: es regla del servicio.
 *   - No escribe SQL: delega en ProductoRepository.
 *
 * COLABORADORES
 *   ProductoService -> validacion, regla de imagen y orquestacion.
 */
class ProductoController extends Controller
{
    public function __construct(
        private readonly ProductoService $productos,
    ) {
    }

    /**
     * Lista todos los productos.
     * GET /productos
     */
    public function index()
    {
        $productos = $this->productos->todos();

        return view('productos.index', compact('productos'));
    }

    /**
     * Muestra el formulario de alta con un producto vacio.
     * GET /productos/create
     */
    public function create()
    {
        return view('productos.form', ['producto' => new \App\Models\Producto()]);
    }

    /**
     * Guarda un producto nuevo.
     * POST /productos
     */
    public function store(Request $request)
    {
        $this->productos->crear($request);

        return redirect()->route('productos.index')
            ->with('exito', 'Producto creado correctamente.');
    }

    /**
     * Muestra el formulario de edicion.
     * GET /productos/{id}/edit
     */
    public function edit($id)
    {
        $producto = $this->productos->buscar((int) $id);

        return view('productos.form', compact('producto'));
    }

    /**
     * Actualiza un producto existente.
     * PUT /productos/{id}
     */
    public function update(Request $request, $id)
    {
        $producto = $this->productos->buscar((int) $id);

        $this->productos->actualizar($request, $producto);

        return redirect()->route('productos.index')
            ->with('exito', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina un producto.
     * DELETE /productos/{id}
     */
    public function destroy($id)
    {
        $this->productos->eliminar((int) $id);

        return redirect()->route('productos.index')
            ->with('exito', 'Producto eliminado correctamente.');
    }
}