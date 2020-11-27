<?php

namespace Core\Foundation\Interfaces;

use Core\Routing\Request;

/**
 * Interface Controller
 * Implementación de los métodos CRUD
 *
 * @package App\Controllers
 */
interface iController
{
    /**
     * Mostrar un registro especifico
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function show(Request $request, $id);

    /**
     * Guarda un registro por método POST
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request);

    /**
     * Vista para editar un registro
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function edit(Request $request, $id);

    /**
     * Actualiza un registro por método UPDATE
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id);

    /**
     * Elimina un registro especifico por método DELETE
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function delete(Request $request, $id);
}