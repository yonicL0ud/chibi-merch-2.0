<?php

namespace App\Http\Controllers;

use App\Services\MensajeService;
use Illuminate\Http\Request;

/**
 * CONTROLADOR - ContactoController
 *
 * CAPA: presentacion (primera capa de la arquitectura).
 *
 * RESPONSABILIDAD UNICA
 *   Mostrar el formulario de contacto y recibir el envio.
 *
 * QUE NO HACE
 *   - No define las reglas de validacion: estan en MensajeService.
 *   - No guarda nada en la base de datos: eso lo hace MensajeRepository.
 *
 * COLABORADORES
 *   MensajeService -> validacion y registro del mensaje.
 */
class ContactoController extends Controller
{
    public function __construct(
        private readonly MensajeService $mensajes,
    ) {
    }

    /**
     * Muestra el formulario.
     * GET /contacto
     */
    public function index()
    {
        return view('contacto.index');
    }

    /**
     * Recibe y registra el mensaje.
     * POST /contacto
     */
    public function store(Request $request)
    {
        // Si la validacion falla, Laravel redirige de vuelta al formulario
        // con los errores en $errors, sin llegar a la linea siguiente.
        $this->mensajes->enviar($request);

        return redirect()->route('contacto.index')
            ->with('exito', 'Gracias por escribirnos. Te responderemos pronto.');
    }
}