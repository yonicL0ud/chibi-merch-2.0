<?php

namespace App\Http\Controllers;

use App\Exceptions\StockInsuficienteException;
use App\Services\CarritoService;
use Illuminate\Http\Request;

/**
 * CONTROLADOR - CarritoController
 *
 * CAPA: presentacion (primera capa de la arquitectura).
 *
 * RESPONSABILIDAD UNICA
 *   - Recibir la peticion HTTP (ruta, metodo, parametros).
 *   - Pedir la accion correspondiente al CarritoService.
 *   - Traducir el resultado en una respuesta web: una vista o una
 *     redireccion con un mensaje flash.
 *
 * QUE NO HACE
 *   - NO contiene logica de negocio. No comprueba stock, no calcula
 *     totales, no decide mensajes de error. Eso lo hace CarritoService.
 *   - NO escribe SQL ni usa Eloquent. Para eso esta CarritoRepository.
 *
 * COLABORADORES
 *   CarritoService -> toda la logica del carrito.
 *   StockInsuficienteException -> error previsto de negocio que el
 *     controlador traduce a un mensaje flash.
 *
 * El servicio se inyecta por el constructor: Laravel lo resuelve solo,
 * sin necesidad de instanciarlo a mano.
 */
class CarritoController extends Controller
{
    public function __construct(
        private readonly CarritoService $carrito,
    ) {
    }

    /**
     * Muestra el carrito: detalle, total y unidades.
     * GET /carrito
     */
    public function index()
    {
        $resumen = $this->carrito->resumen($this->sessionId());

        // compact() crea las variables detalle, total y unidades para la vista.
        return view('carrito.index', $resumen);
    }

    /**
     * Agrega un producto al carrito.
     * POST /carrito/agregar
     */
    public function agregar(Request $request)
    {
        // VALIDACION DE ENTRADA: solo comprueba que el formulario trae
        // datos bien formados. Las reglas de negocio van en el servicio.
        $datos = $request->validate([
            'producto_id' => 'required|integer',
            'cantidad'    => 'nullable|integer|min:1|max:99',
        ]);

        try {
            $this->carrito->agregar(
                (int) $datos['producto_id'],
                (int) ($datos['cantidad'] ?? 1),
                $this->sessionId(),
            );
        } catch (StockInsuficienteException $e) {
            // Error previsto: se muestra el mensaje que redacto el servicio.
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Producto agregado al carrito.');
    }

    /**
     * Cambia la cantidad de un producto del carrito.
     * PATCH /carrito/actualizar
     */
    public function actualizar(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => 'required|integer',
            'cantidad'    => 'required|integer|min:0|max:99',
        ]);

        try {
            $this->carrito->actualizar(
                (int) $datos['producto_id'],
                (int) $datos['cantidad'],
                $this->sessionId(),
            );
        } catch (StockInsuficienteException $e) {
            return back()->with('error', $e->mensajeUsuario());
        }

        return back()->with('exito', 'Carrito actualizado.');
    }

    /**
     * Quita un solo producto del carrito.
     * DELETE /carrito/quitar
     */
    public function quitar(int $id)
    {
        $this->carrito->quitar($id, $this->sessionId());

        return back()->with('exito', 'Producto quitado del carrito.');
    }

    /**
     * Vacia el carrito completo.
     * DELETE /carrito/vaciar
     */
    public function vaciar()
    {
        $this->carrito->vaciar($this->sessionId());

        return back()->with('exito', 'Carrito vaciado.');
    }

    /**
     * Identificador de la sesion del visitante.
     *
     * CARACTERISTICA CLAVE DE LA ARQUITECTURA
     *   El controlador NO guarda el carrito en la sesion; solo le pasa a la
     *   capa de servicio un identificador. El servicio lo usa para saber de
     *   quien es el carrito. Asi, la logica de negocio no depende de la
     *   sesion HTTP y podria reutilizarse desde una API o una cola de
     *   mensajes.
     */
    private function sessionId(): string
    {
        return (string) session()->getId();
    }
}