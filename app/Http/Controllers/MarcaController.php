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

        return response()->json($marcas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $name = $request->input('nome');
        $existName = Marca::where('nome', $name)->first();

        if($existName !== null) {
            return response(
                ['message' => 'Existing name'],
                409
            );
        }

        // return Marca::create($request->all());
        $marca = $this->marca->create($request->all());
        return $marca;
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response(['message' => 'marca not found'], 404);
        }

        return $marca;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $marca = $this->marca->find($id);

        if ($marca === null)
            return response(['message' => 'brand not found'], 404);

        $marca->update($request->all());

        return $marca;
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
