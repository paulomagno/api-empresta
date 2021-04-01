<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class ServiceSimulacaoCredito
{
    private $taxas = NULL;
    private $simulacoes = array();


    /**
    * Retorna as taxas financeiras cadastradas a partir de um arquivo json
    * @param none
    * return json
    */
    public function __construct() {

        $this->taxas = Storage::get(env('FILE_JSON_TAXAS'));
        return $this->taxas;
    } // FIM getInstituicoes

    /**
    * Retorna um array com os numeros de parcelas disponiveis
    * @param none
    * return array
    */
    public function getArrayParcelas() {

        $parcelas = json_decode($this->taxas,true);
        $arrayParcelas = array();

        foreach ($parcelas as $indice => $taxa) {

            $arrayParcelas[] = $parcelas[$indice]['parcelas'];
        }

        return array_unique($arrayParcelas);
    } // FIM numeroParcelas

    /**
    * Retorna as simulações de crédito disponíveis de acordo com os parâmetros informados
    * @param float valorEmprestimo
    * @param array instituicoes
    * @param array convenios
    * @param int   numeroParcelas
    * return array
    */
    public function filtraSimulacaoCredito($valorEmprestimo =  0,
                                           $instituicoes = array(),
                                           $convenios = array(),
                                           $numeroParcelas) {

        $this->simulacoes = collect(json_decode($this->taxas,true));

        // Filtro por Instituição financeira
        if(is_array($instituicoes) && sizeof($instituicoes) > 0 ) {

            $instituicoes = collect($instituicoes)->map(function ($instituicao) {
                return strtoupper($instituicao);
            });

            $this->simulacoes = $this->simulacoes->whereIn('instituicao',$instituicoes);
        }

        // Filtro por Convênio financeiro
        if(is_array($convenios) && sizeof($convenios) > 0 ) {

            $convenios = collect($convenios)->map(function ($convenio) {
                return strtoupper($convenio);
            });

            $this->simulacoes = $this->simulacoes->whereIn('convenio',$convenios);

        }

        // Filtro por numero de parcelas
        if(!empty($numeroParcelas) && $numeroParcelas > 0 ) {
            $this->simulacoes = $this->simulacoes->where('parcelas',$numeroParcelas);
        }

        return $this->simulacoes;
    } // FIM filtraSimulacaoCredito

} // FIM Classe
