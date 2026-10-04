<?php

namespace App\Http\Controllers;

use App\Repositories\ProductoRepository;

/**
 * CONTROLADOR - CatalogoController
 *
 * CAPA: presentacion (primera capa de la arquitectura).
 *
 * RESPONSABILIDAD UNICA
 *   Mostrar el catalogo publico y la ficha de un producto.
 *
 * POR QUE USA EL REPOSITORIO Y NO EL SERVICIO
 *   A diferencia del carrito y del CRUD, aqui NO hay reglas de negocio:
 *   no se valida stock, no se calculan totales, no se toman decisiones.
 *   Solo se leen datos. En ese caso, consultar directamente al
 *   repositorio es la opcion mas simple y evita un servicio vacio.
 *   Documentar esta eleccion es parte del criterio de "definicion de
 *   componentes y responsabilidades": cada capa debe usarse cuando aporta
 *   valor, no por customo.
 *
 * COLABORADORES
 *   ProductoRepository -> lectura de productos.
 */
class CatalogoController extends Controller
{
    public function __construct(
        private readonly ProductoRepository $productos,
    ) {
    }

    /**
     * Catalogo publico con todos los productos.
     * GET /
     */
    public function index()
    {
        $productos = $this->productos->todos();

        return view('catalogo.index', compact('productos'));
    }

    /**
     * Ficha de un producto.
     * GET /producto/{id}
     */
    public function mostrar($id)
    {
        // buscar() usa findOrFail: si el id no existe, Laravel responde 404.
        $producto = $this->productos->buscar((int) $id);

        return view('catalogo.mostrar', compact('producto'));
    }
}