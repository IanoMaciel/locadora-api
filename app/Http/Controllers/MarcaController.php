<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller {

    public function __construct (Marca $marca) {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $marcas = $this->marca->paginate(10);

        if ($marcas->isEmpty())
            return response()->json(['message' => 'No records found'], 404);

        return response()->json($marcas, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate($this->marca->rules(), $this->marca->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
        ]);

        $marca->nome = $request->nome;
        $marca->imagem = $imagem_urn;
        $marca->save();


        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $marca = $this->marca->find($id);

        if ($marca === null)
            return response()->json(['message' => 'marca not found'], 404);

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     */
    public function update(Request $request, $id) {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'brand not found'], 404);
        }

        if ($request->method() === 'PATCH') {
            $dynamicRules = array();
            foreach ($marca->rules() as $input => $rule) {
                if (array_key_exists($input, $request->all())) {
                    $dynamicRules[$input] = $rule;
                }
            }
            $request->validate($dynamicRules, $marca->feedback());
        } else  {
            $request->validate($marca->rules(), $marca->feedback());
        }
        $marca->update($request->all());
        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $marca = $this->marca->find($id);

        if(!$marca) return response()->json(['message' => 'brand not found'], 404);

        $marca->delete();
        return response()->json(['message' => 'Product deleted'], 200);
    }
}
