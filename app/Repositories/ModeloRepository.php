<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class ModeloRepository {
    protected $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }
    public function selectAtributosRegistrosRelacionados($atributos) {
        // guarda o estado que a query esta sendo construída
        $this->model = $this->model->with($atributos);
    }

    public function filtro($filtros) {
        $filtros = explode(';', $filtros);
        foreach ($filtros as $key => $condicao) {
            $c = explode(':', $condicao);
            $this->model = $this->model->where($c[0], $c[1], $c[2]); // a query está sendo construída
        }
    }

    public function selectAtributos($atributos) {
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getResultado() {
        return $this->model->paginate(10);
    }
}
