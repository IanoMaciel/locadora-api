<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use Illuminate\Http\Request;
use App\Repositories\ModeloRepository;

class ModeloController extends Controller
{
    protected $modelo;

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);
        if ($request->has('atributos_marca')) {
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if ($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $modeloRepository->selectAtributos($request->atributos);
        }

        return response()->json($modeloRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
        ]);

        // $modelo->marca_id = $request->marca_id;
        // $modelo->nome =$request->nome;
        // $modelo->imagem = $imagem_urn;
        // $modelo->numero_portas = $request->numero_portas;
        // $modelo->lugares = $request->lugares;
        // $modelo->air_bag = $request->air_bag;
        // $modelo->abs = $request->abs;
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);

        if ($modelo === null) {
            return response()->json(['message' => 'The requested resource does not exist'], 404);
        }

        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['message' => 'The requested resource does not exist'], 404);
        }

        if ($request->method() === 'PATCH') {
            $dynamicRules = array();

            // percorrendo as regras definidas no Model
            foreach ($modelo->rules() as $input => $rule) {
                // coleta apenas as regras aplicáveis aos parâmetros parciais das regras
                if (array_key_exists($input, $request->all())) {
                    $dynamicRules[$input] = $rule;
                }

            }
            $request->validate($dynamicRules);
        } else {
            $request->validate($modelo->rules());
        }

        // remove um arquivo antigo, caso um novo arquivo tenha sido enviado na request
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        // preencher o objeto $marca com os dados da request
        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;

        $modelo->save();

        //        $modelo->update([
        //            'marca_id' => $request->marca_id,
        //            'nome' => $request->nome,
        //            'imagem' => $imagem_urn,
        //            'numero_portas' => $request->numero_portas,
        //            'lugares' => $request->lugares,
        //            'air_bag' => $request->air_bag,
        //            'abs' => $request->abs,
        //        ]);

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['message' => 'The specified resource does not exist'], 404);
        }

        // remove um arquivo antigo, caso um novo arquivo tenha sido enviado na request.
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json(['message' => 'Model has been deleted successfully'], 200);
    }
}
