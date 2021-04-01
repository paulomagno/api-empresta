<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ServiceInstituicoes
{
    private $instituicoes = NULL;
    private $arrayInstituicoes = array();

    /**
    * Retorna um objeto json com as instituções cadastradas a partir de um arquivo json
    * @param none
    * return Object json
    */
    public function getInstituicoes() {

        $this->instituicoes = Storage::get(env('FILE_JSON_INSTITUICOES'));
        return $this->instituicoes;
    } // FIM getInstituicoes


    /**
    * Retorna um array com as instituições cadastradas
    * @param none
    * return array
    */
    public function getArrayInstituicoes() {

        $this->instituicoes = json_decode($this->getInstituicoes(),true);

        foreach ($this->instituicoes as $indice => $instituicao) {

            $this->arrayInstituicoes[] = $this->instituicoes[$indice]['chave'];

        }

        return  $this->arrayInstituicoes;
    } // FIM getArrayInstituicoes

} // FIM Classe
