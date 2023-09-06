<?php

namespace App\Security\Validation;

use app\Security\Utils\Regex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as ValidateContract;

class DevValidation
{

    /* Numeral Severity */
    public function numeral_low($complement = null)
    {
        return 'regex: ' . Regex::$severity['numeral']['low'] . $complement . '$/i';
    }

    public function numeral_medium($complement = null)
    {
        return 'regex: ' .  Regex::$severity['numeral']['medium'] . $complement . '$/i';
    }

    public function numeral_high($complement = null)
    {
        return 'regex: ' .  Regex::$severity['numeral']['high'] . $complement . '$/i';
    }

    /* Text Severity */
    public function text_low($complement = null)
    {
        return 'regex: ' . Regex::$severity['text']['low'] . $complement . '$/i';
    }

    public function text_medium($complement = null)
    {
        return 'regex: ' .  Regex::$severity['text']['medium'] . $complement . '$/i';
    }

    public function text_high($complement = null)
    {
        return 'regex: ' .  Regex::$severity['text']['high'] . $complement . '$/i';
    }

    /* Hybrid Severity */
    public function hybrid_low($complement = null)
    {
        return 'regex: ' . Regex::$severity['hybrid']['low'] . $complement . '$/i';
    }

    public function hybrid_medium($complement = null)
    {
        return 'regex: ' .  Regex::$severity['hybrid']['medium'] . $complement . '$/i';
    }

    public function hybrid_high($complement = null)
    {
        return 'regex: ' .  Regex::$severity['hybrid']['high'] . $complement . '$/i';
    }

    /* Uniques Severity */
    public function uniques_def_portuguese($complement = null)
    {
        return 'regex: ' . Regex::$severity['uniques']['def_portuguese'] . $complement . '$/im';
    }

    public function uniques_text_editor($complement = null)
    {
        return 'regex: ' .  Regex::$severity['uniques']['text_editor'] . $complement . '$/im';
    }


    /**
     * Singular variables for use in specific situations. (EXACT MATCH: Use in final validation)
     *
     * @var string
     */
    public function booleanFormat()
    {
        return 'regex:' .  Regex::PATTERN_BOOLEAN;
    }
    public function multiDateFormat()
    {
        return 'regex:' . Regex::PATTTERN_MULTI_DATE;
    }
    public function methodFormat()
    {
        return 'regex:' . Regex::PATTERN_METHOD;
    }
    public function emailFormat()
    {
        return 'regex:' . Regex::PATTERN_EMAIL;
    }
    public function digitoUnicoFormat()
    {
        return 'regex:' . Regex::PATTERN_CEP;
    }
    public function cepFormat()
    {
        return 'regex:' . Regex::PATTERN_CEP;
    }
    public function anoFormat()
    {
        return 'regex:' . Regex::PATTERN_ANO;
    }
    public function telefoneFormat()
    {
        return 'regex:' . Regex::PATTERN_TELEFONE;
    }
    public function contaFormat()
    {
        return 'regex:' . Regex::PATTERN_CONTA;
    }
    public function containsCpfCnpj()
    {
        return 'regex:' . Regex::PATTERN_CONTAINS_CPFCNPJ;
    }
    public function cpfCnpjFormat()
    {
        return 'regex:' . Regex::PATTERN_CPFCNPJ;
    }
    public function cpfFormat()
    {
        return 'regex:' .  Regex::PATTERN_CPF;
    }
    public function cnpjFormat()
    {
        return 'regex:' .  Regex::PATTERN_CNPJ;
    }
    public function dataBrFormat()
    {
        return 'regex:' .  Regex::PATTERN_DATA_BR;
    }
    public function dataEnFormat()
    {
        return 'regex:' .  Regex::PATTERN_DATA_EN;
    }
    public function horaFormat()
    {
        return 'regex:' .  Regex::PATTERN_HORA;
    }
    public function dataHoraBrFormat()
    {
        return 'regex:' .  Regex::PATTERN_DATA_HORA_BR;
    }
    public function dataHoraMinBrFormat()
    {
        return 'regex:' .  Regex::PATTERN_DATA_HORA_MIN_BR;
    }
    public function dataHoraEnFormat()
    {
        return 'regex:' .  Regex::PATTERN_DATA_HORA_EN;
    }
    public function dataHoraMinEnFormat()
    {
        return 'regex:' .  Regex::PATTERN_DATA_HORA_MIN_EN;
    }
    public function numeroIntFormat()
    {
        return 'regex:' .  Regex::PATTERN_NUMERO_INT;
    }
    public function numeroIntBrFormat()
    {
        return 'regex:' .  Regex::PATTERN_NUMERO_INT_BR;
    }
    public function containsNumeroIntFormat()
    {
        return 'regex:' .  Regex::PATTERN_CONTAINS_NUMERO_INT;
    }
    public function numeroFloatEnFormat()
    {
        return 'regex:' .  Regex::PATTERN_NUMERO_FLOAT_EN;
    }
    public function numeroFloatBrFormat()
    {
        return 'regex:' .  Regex::PATTERN_NUMERO_FLOAT_BR;
    }
    public function trueFormat()
    {
        return 'regex:' .  Regex::PATTERN_TRUE;
    }
    public function falseFormat()
    {
        return 'regex:' .  Regex::PATTERN_FALSE;
    }
    public function base64Format()
    {
        return 'regex:' .  Regex::PATTERN_BASE64;
    }
    public function sexoFormat()
    {
        return 'regex:' .  Regex::PATTERN_SEXO;
    }
    public function senhaFormat()
    {
        return 'regex:' .  Regex::PATTERN_SENHA;
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
