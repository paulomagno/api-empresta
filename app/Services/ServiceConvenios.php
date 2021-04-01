<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ServiceConvenios
{
    private $convenios = NULL;
    private $arrayConvenios = array();

    /**
    * Retorna um Objeto json com os convenios cadastrados a partir de um arquivo json
    * @param none
    * return Object json
    */
    public function getConvenios() {

        $this->convenios = Storage::get(env('FILE_JSON_CONVENIOS'));
        return $this->convenios;
    } // FIM getConvenios

    /**
    * Retorna um array com os convenios
    * @param none
    * return array
    */
    public function getArrayConvenios() {

        $this->convenios = json_decode($this->getConvenios(),true);

        foreach ($this->convenios as $indice => $convenio) {

            $this->arrayConvenios[] = $this->convenios[$indice]['chave'];

        }

        return  $this->arrayConvenios;
    } // FIM getArrayConvenios



}


