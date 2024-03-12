<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCarroRequest;
use App\Models\Carro;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    protected $carro;

    /**
     * Undocumented function
     *
     * @param Carro $carro
     */
    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }

    /**
     * Undocumented function
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro);

        if ($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,' . $request->atributos_modelos;
            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $carroRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')) {
            $carroRepository->filtro($request->filtro);
        }


        if ($request->has('atributos')) {
            $carroRepository->selectAtributos($request->atributos);
        }

        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$request->validate($this->carro->rules());

        dd($request->all());

        // $carro = $this->carro->create($request->all());
        // return response()->json($carro, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carro = $this->carro->find($id);
        if ($carro === null) {
            return response()->json(['message' => 'Carro não existe'], 404);
        }

        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Integer $id
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);

        if ($carro === null) {
            return response()->json(['message' => 'Carro não exite'], 404);
        }

        $carro->update($request->all());
        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carro = $this->carro->find($id);
        if ($carro === null) {
            return response()->json(['message' => 'Erro ao excluir. Carrão não existente'], 404);
        }
        $carro->delete();
        return response()->josn(['message' => 'Carro foi removido com sucesso'], 200);
    }
}
