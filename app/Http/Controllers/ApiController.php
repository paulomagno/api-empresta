<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

use App\Services\ServiceInstituicoes;
use App\Services\ServiceConvenios;
use App\Services\ServiceSimulacaoCredito;

class ApiController extends Controller
{
    private $instituicoes = NULL;
    private $convenios = NULL;
    private $simulacoes = NULL;

    // Método Construtor
    public function __construct(ServiceInstituicoes $instituicoes,
                                ServiceConvenios $convenios,
                                ServiceSimulacaoCredito $simulacoes) {

        $this->instituicoes = $instituicoes;
        $this->convenios    = $convenios;
        $this->simulacoes   = $simulacoes;

    } // FIM construtor

    /**
    * Retorna todas as instituções financeiras
    * @param none
    * return json
    */
    public function getInstituicoes() {

        // Verifica se o arquivo existe no storage local do laravel (storage\app)
        if ( Storage::disk('local')->exists(env('FILE_JSON_INSTITUICOES')) ) {

            return response($this->instituicoes->getInstituicoes(), 200)
                     ->header('Content-Type',' application/json; charset=utf-8');
        }
    } // FIM getInstituicoes

   /**
    * Retorna todos os convênios financeiros
    * @param none
    * return json
    */
    public function getConvenios() {

        // Verifica se o arquivo existe no storage local do laravel (storage\app)
        if ( Storage::disk('local')->exists(env('FILE_JSON_CONVENIOS')) ) {

            return response($this->convenios->getConvenios(), 200)
                     ->header('Content-Type',' application/json; charset=utf-8');
        }
    } // FIM getConvenios

    /**
    * Retorna as operações de crédito disponíveis de acordo com as informações enviadas
    * @param
    * return json
    */
    public function simulacaoCredito(Request $request) {

        // Regras de Validação dos dados
        $mensagem = ['error' => ''];
        $simulacoes = array();

        $request->valor_emprestimo = str_replace('.',',',$request->valor_emprestimo);
        $request->valor_emprestimo = str_replace(',','.',$request->valor_emprestimo);

        $regras = [
            'valor_emprestimo' => 'required|numeric|gt:0',
            'parcela' => 'integer|nullable|gt:0',
            'instituicoes' => 'array',
            'convenios'    => 'array'
        ];

        $validador = Validator::make($request->all(),$regras);

        if($validador->fails()) {

            $mensagem['error'] = $validador->messages();
            return $mensagem;

        }

        // Retorna um array de instituicoes
        $instituicoes = $this->instituicoes->getArrayInstituicoes();

        // Valida se a instituição financeira existe na relação cadastrada
        if(is_array($request->instituicoes) && sizeof($request->instituicoes) > 0 ) {
            foreach ($request->instituicoes as $indice => $instituicao) {

                if(!in_array(strtoupper($request->instituicoes[$indice]),$instituicoes)) {

                    $mensagem['error'] = 'Sao permitidas apenas as instituicoes '.implode(',',$instituicoes);
                    return $mensagem;
                    break;

                }
            }
        }

        // Retorna um array de convenios
        $convenios = $this->convenios->getArrayConvenios();

        // Valida se o convênio financeiro existe na relação cadastrada
        if(is_array($request->convenios) &&  sizeof($request->convenios) > 0 ) {

            foreach ($request->convenios as $indice => $convenio) {

                if(!in_array(strtoupper($request->convenios[$indice]),$convenios)) {

                    $mensagem['error'] = 'Sao permitidos apenas os convenios '.implode(',',$convenios);
                    return $mensagem;
                    break;

                }
            }

        }

        // Valida se a parcela está na relação de parcelas disponíveis
        $parcelas = $this->simulacoes->getArrayParcelas();

        if(!empty($request->parcela) && !in_array($request->parcela,$parcelas)) {
            $mensagem['error'] = 'Sao permitidos apenas parcelas : '.implode(',',$parcelas);
            return $mensagem;
        }

        // Verifica se o arquivo existe no storage local do laravel (storage\app)
        if ( Storage::disk('local')->exists(env('FILE_JSON_TAXAS')) ) {

            $simulacoesFiltro = $this->simulacoes->filtraSimulacaoCredito(
                $request->valor_emprestimo,
                $request->instituicoes,
                $request->convenios,
                $request->parcela
            );

            if(sizeof($simulacoesFiltro) > 0) {

                // Processa as simulações filtradas e as agrupa por instituição
                foreach ($simulacoesFiltro as $indice => $dados) {

                   $instituicao  = $simulacoesFiltro[$indice]['instituicao'];
                   $valorParcela = $request->valor_emprestimo * $simulacoesFiltro[$indice]['coeficiente'];

                   $simulacoes[$instituicao][] = array('taxa'          => $simulacoesFiltro[$indice]['taxaJuros'],
                                                       'parcelas'      => $simulacoesFiltro[$indice]['parcelas'],
                                                       'valor_parcela' => number_format($valorParcela,2,',','.'),
                                                       'convenio'      => $simulacoesFiltro[$indice]['convenio']
                                                 );
                }

                return response($simulacoes, 200)
                       ->header('Content-Type',' application/json; charset=utf-8');
            }
            else {
                $mensagem['error'] = 'Nao foi encontrada nenhuma informacao com os criterios de pesquisa';
                return $mensagem;
            }
        }
    } // FIM simulacaoCredito
}
