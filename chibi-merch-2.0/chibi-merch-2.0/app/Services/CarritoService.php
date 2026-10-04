<?php

namespace App\Services;

use App\Exceptions\StockInsuficienteException;
use App\Models\Producto;
use App\Repositories\CarritoRepository;
use App\Repositories\ProductoRepository;
use Illuminate\Support\Facades\DB;

/**
 * SERVICIO - CarritoService
 *
 * CAPA: servicio (capa de logica de negocio), la capa puente entre la
 * interfaz de usuario (controladores y vistas) y la base de datos.
 *
 * RESPONSABILIDAD UNICA
 *   Aplicar TODAS las reglas del negocio del carrito:
 *     - Verificar que exista stock suficiente antes de agregar o cambiar
 *       una cantidad.
 *     - Decidir si una cantidad es 0 (eliminar la linea) o mayor.
 *     - Calcular el subtotal por producto y el total general.
 *     - Sumar la cantidad cuando el producto ya esta en el carrito, en vez
 *       de duplicar la linea.
 *     - Vaciar el carrito completo.
 *
 * QUE NO HACE (y por eso existe la arquitectura)
 *   - No genera HTML ni conoce las vistas Blade.
 *   - No escribe SQL ni usa Eloquent directamente: le delega eso al
 *     CarritoRepository.
 *   - No lee ni escribe $_SESSION para el contenido del carrito; solo pide
 *     el identificador de sesion como dato de entrada.
 *
 * POR QUE ES UNA CAPA SEPARADA
 *   - Es la "API" que consumen los controladores: agregar(), resumen(),
 *     actualizar(), vaciar(). Si mañana el carrito necesita cupones de
 *     descuento, solo se modifica esta clase.
 *   - Permite reutilizar la logica desde otro canal (por ejemplo una API)
 *     sin duplicar reglas.
 *   - Aísla la logica de negocio en un unico lugar, mas facil de probar y
 *     de mantener.
 *
 * COLABORADORES
 *   ProductoRepository  -> obtiene el producto y su stock.
 *   CarritoRepository   -> persiste las lineas en carrito_items.
 *   StockInsuficienteException -> comunica los errores previstos.
 */
class CarritoService
{
    /**
     * Inyeccion de dependencias: el servicio recibe sus colaboradores
     * por el constructor en vez de crear sus propias instancias.
     * Asi se pueden reemplazar por dobles en pruebas unitarias.
     */
    public function __construct(
        private readonly ProductoRepository $productos,
        private readonly CarritoRepository $carritos,
    ) {
    }

    /**
     * Agrega una cantidad de un producto al carrito de la sesion.
     *
     * REGLA DE NEGOCIO CENTRAL
     *   La cantidad final en el carrito nunca puede superar el stock
     *   disponible del producto.
     *
     * @throws StockInsuficienteException si no hay stock suficiente.
     */
    public function agregar(int $productoId, int $cantidad, string $sessionId): void
    {
        // 1. Verificar que el producto exista (404 si no).
        $producto = $this->productos->buscar($productoId);

        // 2. REGLA (defensiva): la validacion del formulario ya exige
        //    cantidad >= 1, pero el servicio no presupone que le llamen
        //    correctamente, asi que tambien la verifica.
        if ($cantidad < 1) {
            throw new StockInsuficienteException('La cantidad debe ser al menos 1.');
        }

        // 3. Transaccion: si algo falla, no queda nada a medias.
        //    Leemos y escribimos el carrito de forma atomica.
        DB::transaction(function () use ($producto, $cantidad, $sessionId) {
            $linea = $this->carritos->buscarLinea($sessionId, $producto->id);
            $cantidadFinal = ($linea?->cantidad ?? 0) + $cantidad;

            // 4. REGLA DE NEGOCIO: el total no puede superar el stock.
            if ($cantidadFinal > $producto->stock) {
                throw new StockInsuficienteException(
                    $producto->stock === 0
                        ? 'El producto "' . $producto->nombre . '" esta agotado.'
                        : 'Solo quedan ' . $producto->stock . ' unidades de "' . $producto->nombre . '".'
                );
            }

            // 5. Si la linea ya existe, se acumula; si no, se crea.
            if ($linea) {
                $this->carritos->cambiarCantidad($linea, $cantidadFinal);
            } else {
                $this->carritos->crearLinea($sessionId, $producto->id, $cantidad);
            }
        });
    }

    /**
     * Cambia la cantidad de un producto ya presente en el carrito.
     * Si la cantidad es 0, la linea se elimina.
     *
     * @throws StockInsuficienteException si se supera el stock.
     */
    public function actualizar(int $productoId, int $cantidad, string $sessionId): void
    {
        $linea = $this->carritos->buscarLinea($sessionId, $productoId);

        // Si el producto no esta en el carrito, no hay nada que cambiar.
        if (!$linea) {
            return;
        }

        // REGLA: cantidad 0 equivale a quitar el producto del carrito.
        if ($cantidad <= 0) {
            $this->carritos->eliminarLinea($linea);

            return;
        }

        $producto = $this->productos->buscar($productoId);

        // REGLA: no superar el stock.
        if ($cantidad > $producto->stock) {
            throw new StockInsuficienteException(
                'Solo hay ' . $producto->stock . ' unidades de "' . $producto->nombre . '".'
            );
        }

        $this->carritos->cambiarCantidad($linea, $cantidad);
    }

    /**
     * Calcula el detalle del carrito: lineas, total y unidades.
     *
     * ESTE METODO ES LA API PRINCIPAL DEL SERVICIO
     *   El controlador solo lo llama y pasa el resultado a la vista.
     *   Toda la aritmetica del carrito esta aqui, no en la vista ni en el
     *   controlador.
     *
     * @return array{detalle: list<array{producto: Producto, cantidad: int, subtotal: float}>, total: float, unidades: int}
     */
    public function resumen(string $sessionId): array
    {
        $lineas = $this->carritos->itemsDe($sessionId);

        $detalle = [];
        $total   = 0.0;
        $unidades = 0;

        foreach ($lineas as $linea) {
            $subtotal = (float) $linea->producto->precio * $linea->cantidad;
            $total   += $subtotal;
            $unidades += $linea->cantidad;

            $detalle[] = [
                'producto' => $linea->producto,
                'cantidad' => (int) $linea->cantidad,
                'subtotal' => $subtotal,
            ];
        }

        return [
            'detalle'  => $detalle,
            'total'    => round($total, 2),
            'unidades' => $unidades,
        ];
    }

    /**
     * Total de unidades del carrito de una sesion.
     * Lo usa el menu para mostrar el contador (Carrito (3)).
     */
    public function totalUnidades(string $sessionId): int
    {
        return $this->carritos->totalUnidades($sessionId);
    }

    /**
     * Elimina todas las lineas del carrito de la sesion.
     */
    public function vaciar(string $sessionId): void
    {
        $this->carritos->vaciar($sessionId);
    }

    /**
     * Elimina una sola linea del carrito.
     */
    public function quitar(int $productoId, string $sessionId): void
    {
        $linea = $this->carritos->buscarLinea($sessionId, $productoId);

        if ($linea) {
            $this->carritos->eliminarLinea($linea);
        }
    }
}