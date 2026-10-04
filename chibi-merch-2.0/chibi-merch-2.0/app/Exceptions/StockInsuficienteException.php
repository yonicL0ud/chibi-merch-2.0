<?php

namespace App\Exceptions;

use Exception;

/**
 * EXCEPCION DE NEGOCIO - StockInsuficienteException
 *
 * COMPONENTE: gestion de errores y excepciones de la capa de servicio.
 *
 * QUE REPRESENTA
 *   Un error previsto por las reglas del negocio: se pidio una cantidad
 *   de productos que supera el stock disponible.
 *
 * POR QUE EXISTE UNA EXCEPCION PROPIA
 *   - Distingue un error del NEGOCIO (el usuario exagero, no hay stock)
 *     de un error TECNICO (la base de datos no responde).
 *   - Permite que CarritoService decida y lance, y que el controlador
 *     catch y muestre un mensaje amigable, sin que la capa de servicio
 *     conozca la vista ni la sesion HTTP.
 *   - Evita llenos de if anidados comparando booleanos de retorno.
 *
 * FLUJO
 *   CarritoService lanza  ->  CarritoController captura  ->  mensaje flash
 */
class StockInsuficienteException extends Exception
{
    /**
     * Mensaje que se mostrara al usuario final, ya redactado.
     */
    private string $mensajeUsuario;

    public function __construct(string $mensajeUsuario)
    {
        // El mensaje tecnico va al constructor de Exception; el mensaje
        // para el usuario se guarda aparte.
        parent::__construct($mensajeUsuario);

        $this->mensajeUsuario = $mensajeUsuario;
    }

    /**
     * Devuelve el mensaje redactado para mostrar en la pagina.
     */
    public function mensajeUsuario(): string
    {
        return $this->mensajeUsuario;
    }
}