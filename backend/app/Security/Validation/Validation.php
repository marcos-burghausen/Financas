<?php

namespace App\Security\Validation;

use app\Security\Validation\Regexp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidateContract;

class Validation
{

    /* Numeral Severity */
    public function numeral_low($complement = null)
    {
        return 'regex: ' . Regexp::$severity['numeral']['low'] . $complement . '$/i';
    }

    public function numeral_medium($complement = null)
    {
        return 'regex: ' .  Regexp::$severity['numeral']['medium'] . $complement . '$/i';
    }

    public function numeral_high($complement = null)
    {
        return 'regex: ' .  Regexp::$severity['numeral']['high'] . $complement . '$/i';
    }

    /* Text Severity */
    public function text_low($complement = null)
    {
        return 'regex: ' . Regexp::$severity['text']['low'] . $complement . '$/i';
    }

    public function text_medium($complement = null)
    {
        return 'regex: ' .  Regexp::$severity['text']['medium'] . $complement . '$/i';
    }

    public function text_high($complement = null)
    {
        return 'regex: ' .  Regexp::$severity['text']['high'] . $complement . '$/i';
    }

    /* Hybrid Severity */
    public function hybrid_low($complement = null)
    {
        return 'regex: ' . Regexp::$severity['hybrid']['low'] . $complement . '$/i';
    }

    public function hybrid_medium($complement = null)
    {
        return 'regex: ' .  Regexp::$severity['hybrid']['medium'] . $complement . '$/i';
    }

    public function hybrid_high($complement = null)
    {
        return 'regex: ' .  Regexp::$severity['hybrid']['high'] . $complement . '$/i';
    }

    /* Uniques Severity */
    public function uniques_def_portuguese($complement = null)
    {
        return 'regex: ' . Regexp::$severity['uniques']['def_portuguese'] . $complement . '$/im';
    }

    public function uniques_text_editor($complement = null)
    {
        return 'regex: ' .  Regexp::$severity['uniques']['text_editor'] . $complement . '$/im';
    }


    /**
     * Singular variables for use in specific situations. (EXACT MATCH: Use in final validation)
     *
     * @var string
     */
    public function booleanFormat()
    {
        return 'regex:' .  Regexp::PATTERN_BOOLEAN;
    }
    public function multiDateFormat()
    {
        return 'regex:' . Regexp::PATTTERN_MULTI_DATE;
    }
    public function methodFormat()
    {
        return 'regex:' . Regexp::PATTERN_METHOD;
    }
    public function emailFormat()
    {
        return 'regex:' . Regexp::PATTERN_EMAIL;
    }
    public function digitoUnicoFormat()
    {
        return 'regex:' . Regexp::PATTERN_CEP;
    }
    public function cepFormat()
    {
        return 'regex:' . Regexp::PATTERN_CEP;
    }
    public function anoFormat()
    {
        return 'regex:' . Regexp::PATTERN_ANO;
    }
    public function telefoneFormat()
    {
        return 'regex:' . Regexp::PATTERN_TELEFONE;
    }
    public function contaFormat()
    {
        return 'regex:' . Regexp::PATTERN_CONTA;
    }
    public function containsCpfCnpj()
    {
        return 'regex:' . Regexp::PATTERN_CONTAINS_CPFCNPJ;
    }
    public function cpfCnpjFormat()
    {
        return 'regex:' . Regexp::PATTERN_CPFCNPJ;
    }
    public function cpfFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CPF;
    }
    public function cnpjFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CNPJ;
    }
    public function carteiraFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CARTEIRA;
    }
    public function isaFormat()
    {
        return 'regex:' .  Regexp::PATTERN_ISA;
    }
    public function scoreFormat()
    {
        return 'regex:' .  Regexp::PATTERN_SCORE;
    }
    public function ratingFormat()
    {
        return 'regex:' .  Regexp::PATTERN_RATING;
    }
    public function dataBrFormat()
    {
        return 'regex:' .  Regexp::PATTERN_DATA_BR;
    }
    public function dataEnFormat()
    {
        return 'regex:' .  Regexp::PATTERN_DATA_EN;
    }
    public function horaFormat()
    {
        return 'regex:' .  Regexp::PATTERN_HORA;
    }
    public function dataHoraBrFormat()
    {
        return 'regex:' .  Regexp::PATTERN_DATA_HORA_BR;
    }
    public function dataHoraMinBrFormat()
    {
        return 'regex:' .  Regexp::PATTERN_DATA_HORA_MIN_BR;
    }
    public function dataHoraEnFormat()
    {
        return 'regex:' .  Regexp::PATTERN_DATA_HORA_EN;
    }
    public function dataHoraMinEnFormat()
    {
        return 'regex:' .  Regexp::PATTERN_DATA_HORA_MIN_EN;
    }
    public function numeroIntFormat()
    {
        return 'regex:' .  Regexp::PATTERN_NUMERO_INT;
    }
    public function numeroIntBrFormat()
    {
        return 'regex:' .  Regexp::PATTERN_NUMERO_INT_BR;
    }
    public function containsNumeroIntFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CONTAINS_NUMERO_INT;
    }
    public function numeroFloatEnFormat()
    {
        return 'regex:' .  Regexp::PATTERN_NUMERO_FLOAT_EN;
    }
    public function numeroFloatBrFormat()
    {
        return 'regex:' .  Regexp::PATTERN_NUMERO_FLOAT_BR;
    }
    public function trueFormat()
    {
        return 'regex:' .  Regexp::PATTERN_TRUE;
    }
    public function falseFormat()
    {
        return 'regex:' .  Regexp::PATTERN_FALSE;
    }
    public function tipoPessoaFormat()
    {
        return 'regex:' .  Regexp::PATTERN_TIPO_PESSOA;
    }
    public function tipoPessoaPfFormat()
    {
        return 'regex:' .  Regexp::PATTERN_TIPO_PESSOA_PF;
    }
    public function tipoPessoaPjFormat()
    {
        return 'regex:' .  Regexp::PATTERN_TIPO_PESSOA_PJ;
    }
    public function seguroProdAutoFormat()
    {
        return 'regex:' .  Regexp::PATTERN_SEGURO_PROD_AUTO;
    }
    public function seguroProdVidaFormat()
    {
        return 'regex:' .  Regexp::PATTERN_SEGURO_PROD_VIDA;
    }
    public function seguroProdResidencialFormat()
    {
        return 'regex:' .  Regexp::PATTERN_SEGURO_PROD_RESIDENCIAL;
    }
    public function seguroProdPatrimonialFormat()
    {
        return 'regex:' .  Regexp::PATTERN_SEGURO_PROD_PATRIMONIAL;
    }
    public function seguroProdRuralFormat()
    {
        return 'regex:' .  Regexp::PATTERN_SEGURO_PROD_RURAL;
    }
    public function seguroProdAgricolaFormat()
    {
        return 'regex:' .  Regexp::PATTERN_SEGURO_PROD_AGRICOLA;
    }
    public function consorcioSegmentoAutomoveisFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CONSORCIO_SEGMENTO_AUTOMOVEIS;
    }
    public function consorcioSegmentoImoveisFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CONSORCIO_SEGMENTO_IMOVEIS;
    }
    public function consorcioSegmentoMotocicletasFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CONSORCIO_SEGMENTO_MOTOCICLETAS;
    }
    public function consorcioSegmentoServicosFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CONSORCIO_SEGMENTO_SERVICOS;
    }
    public function consorcioSegmentoMoveisPlanejadosFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CONSORCIO_SEGMENTO_MOVEIS_PLANEJADOS;
    }
    public function consorcioSegmentoCaminhaoTratorEquipFormat()
    {
        return 'regex:' .  Regexp::PATTERN_CONSORCIO_SEGMENTO_CAMINHAO_TRATOR_EQUIP;
    }
    public function base64Format()
    {
        return 'regex:' .  Regexp::PATTERN_BASE64;
    }
    public function sexoFormat()
    {
        return 'regex:' .  Regexp::PATTERN_SEXO;
    }
    public function senhaFormat()
    {
        return 'regex:' .  Regexp::PATTERN_SENHA;
    }

    /**
     * Api Security Validate 
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function apiSecurityValidate(Request $request, array $allowed = [], ?ValidateContract $valid = null)
    {

        if (!$valid) {
            $valid = Validator::make($request->all(), $allowed);
        }

        $validJson = $this->validationJsonStructure($request, $allowed);

        if ($valid->fails() == true) {
            $response = [
                'error' => $request->multipleErrors ? $valid->errors() : $valid->errors()->first(),
                'code' => 422
            ];

            throw new HttpResponseException(response()->json($response, 422));
        } else if ($validJson == false) {
            $response = [
                'error' => 'Erro ao validar os parâmetros informados',
                'code' => 422
            ];

            throw new HttpResponseException(response()->json($response, 422));
        }
    }

    /**
     * Web Security Validate 
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function webSecurityValidate(Request $request, array $allowed = [])
    {

        $valid = Validator::make($request->all(), $allowed);

        $validJson = $this->validationJsonStructure($request, $allowed);

        if ($valid->fails() == true) {
            throw new HttpResponseException(redirect()->away($request->url())->withErrors([$valid->errors()->first()]));
        } else if ($validJson == false) {
            throw new HttpResponseException(redirect()->away($request->url())->withErrors(['error' => 'Erro ao validar os parâmetros informados']));
        }
    }

    /**
     * Verifica a existência da model e do registro no bd 
     *
     * @param  $model
     * @param  $id
     */
    public function entryExistence($model, $id)
    {
        $modelPath = 'App\\Models\\' . $model;

        try {
            $primaryKey = app($modelPath)->getKeyName();
            $existence = $modelPath::where($primaryKey, $id)->exists();

            if (!$existence) {

                $response = [
                    'error' => preg_replace('/([aeioun])s$/i', '$1', $model) . ' informado(a) não foi encontrado(a).',
                    'code' => 422
                ];

                throw new HttpResponseException(response()->json($response, 422));
            }

            return true;
        } catch (\Exception $e) {

            $response = [
                'error' => 'Modelo instanciado não encontrado.',
                'code' => 422
            ];

            throw new HttpResponseException(response()->json($response, 422));
        }
    }

    /**
     * Valida o payload para verificar se não foram enviados campos não esperados
     */
    private function validationJsonStructure(Request $request, $allowed)
    {
        $keys = $request->keys();

        //adiciona os parâmetros padrões ['_token','_method'] ao array de parâmetros permitidos
        $allowed = array_merge(array_keys($allowed), config('pioneira-security.payload_validation.pattern_params'));

        foreach ($keys as $key) {
            if (array_search($key, $allowed) === false) {
                return false;
            }
        }
        return true;
    }
}
