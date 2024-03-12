<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Repositories\MarcaRepository;
use App\Models\Marca;

class MarcaController extends Controller
{
    protected $marca;

    /**
     * Undocumented function
     *
     * @param Marca $marca
     */
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);
        if ($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);
        }

        return response()->json($marcaRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'marca not found'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'brand not found'], 404);
        }

        if ($request->method() === 'PATCH') {
            $dynamicRules = array();

            // percorrendo as regras definidas no Model
            foreach ($marca->rules() as $input => $rule) {
                // coleta apenas as regras aplicáveis aos parâmetros parciais das regras
                if (array_key_exists($input, $request->all())) {
                    $dynamicRules[$input] = $rule;
                }

            }
            $request->validate($dynamicRules, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }

        // remove um arquivo antigo, caso um novo arquivo tenha sido enviado na request
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');


        // preencher o objeto $marca com os dados da request
        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;

        $marca->save();

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        if(!$marca) {
            return response()->json(['message' => 'brand not found'], 404);
        }

        // remove um arquivo antigo, caso um novo arquivo tenha sido enviado na request.
        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json(['message' => 'Product deleted'], 200);
    }
}
