<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    protected $cliente;

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clienteRepository = new ClienteRepository($this->cliente);

        if ($request->has('filtro')) {
            $clienteRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $clienteRepository->selectAtributos($request->atributos);
        }

        return response()->json($clienteRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules());
        $cliente = $this->cliente->create($request->all());
        return response()->json($cliente, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $cliente = $this->cliente->find($id);

        if ($cliente === null) {
            return response()->json(['message' => 'Cliente não existe'], 404);
        }

        return response()->json($cliente, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cliente = $this->cliente->find($id);
        if ($cliente === null) {
            return response()->json(['message' => 'Cliente não existe'],404);
        }

        $cliente->update($request->all());

        return response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);
        if ($cliente === null) {
            return response()->json(['message' => 'Cliente não existe'], 404);
        }

        $cliente->delete();
        return response()->json(['message' => 'Registro removido com sucesso!'], 200);
    }
}
