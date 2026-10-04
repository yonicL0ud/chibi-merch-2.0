<?php

namespace App\Repositories;

use App\Models\Mensaje;
use Illuminate\Database\Eloquent\Collection;

/**
 * REPOSITORIO - MensajeRepository
 *
 * CAPA: acceso a datos (tercera capa de la arquitectura).
 *
 * RESPONSABILIDAD UNICA
 *   Guardar y listar los mensajes enviados desde el formulario de contacto.
 *
 * QUE NO HACE
 *   - No valida los campos del formulario.
 *   - No filtra ni responde los mensajes.
 */
class MensajeRepository
{
    /**
     * Persiste un mensaje ya validado.
     *
     * @param  array<string, string> $datos
     */
    public function crear(array $datos): Mensaje
    {
        return Mensaje::create($datos);
    }

    /**
     * Devuelve todos los mensajes, del mas reciente al mas antiguo.
     *
     * @return Collection<int, Mensaje>
     */
    public function todos(): Collection
    {
        return Mensaje::orderByDesc('created_at')->get();
    }
}