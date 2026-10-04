<?php

namespace App\Services;

use App\Models\Mensaje;
use App\Repositories\MensajeRepository;
use Illuminate\Http\Request;

/**
 * SERVICIO - MensajeService
 *
 * CAPA: servicio (capa de logica de negocio).
 *
 * RESPONSABILIDAD UNICA
 *   Validar y registrar los mensajes del formulario de contacto.
 *
 * QUE NO HACE
 *   - No decide si el mensaje se responde o se marca como leido
 *     (no hay panel de administracion en esta version).
 *   - No genera vistas ni respuestas HTTP.
 *
 * COLABORADORES
 *   MensajeRepository -> persistencia.
 */
class MensajeService
{
    public function __construct(
        private readonly MensajeRepository $mensajes,
    ) {
    }

    /**
     * Reglas de validacion del formulario de contacto.
     *
     * @return array<string, string>
     */
    public function reglas(): array
    {
        return [
            'nombre'  => 'required|string|max:120',
            'correo'  => 'required|email|max:150',
            'asunto'  => 'required|string|max:120',
            'mensaje' => 'required|string|max:2000',
        ];
    }

    /**
     * Valida la peticion y guarda el mensaje.
     */
    public function enviar(Request $request): Mensaje
    {
        $datos = $request->validate($this->reglas());

        return $this->mensajes->crear($datos);
    }
}