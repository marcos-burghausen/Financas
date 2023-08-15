<?php

namespace app\Security\Validation;

class Regexp
{

	static $severity = [
		'text' => [
			'low'    => '/^[a-zà-ýÀ-Ý.\-_\/ ]',
			'medium' => '/^[a-zà-ýÀ-Ý ]',
			'high'   => '/^[a-zà-ýÀ-Ý]',
		],
		'numeral' => [
			'low'    => '/^[0-9\/\.\- ]',
			'medium' => '/^[0-9 ]',
			'high'   => '/^[0-9]',
		],
		'hybrid' => [
			'low'    => '/^[0-9a-zà-ýÀ-Ý\/\.\-º, ]',
			'medium' => '/^[0-9a-zà-ýÀ-Ý ]',
			'high'   => '/^[0-9a-zà-ýÀ-Ý]',
		],
		'uniques' => [
			'def_portuguese' => '/^[\da-zà-ý\=\/_\.\-\!\?\*\"\'\$\@\`\´\,\%\(\) ]',
			'text_editor' => '/^.',
		],

	];
	const PATTERN_CONTA = '/^[0-9]{5}-[0-9]$|^[0-9]{6}$/';
	const PATTERN_CONTAINS_CPFCNPJ = '/[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}|[0-9]{11}|[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}|[0-9]{14}/';
	const PATTERN_CPFCNPJ = '/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$|^[0-9]{11}$|^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}$|^[0-9]{14}$/';
	const PATTERN_CPF = '/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$|^[0-9]{11}$/';
	const PATTERN_CNPJ = '/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}$|^[0-9]{14}$/';
	const PATTERN_CARTEIRA = '/^[0-9]{3}$/';
	const PATTERN_ISA = '/^[0-9]{1,2}$/';
	const PATTERN_SCORE = '/^(1000)$|^[0-9]{1,3}$/';
	const PATTERN_RATING = '/^AA$|^[A-H]$/';
	const PATTERN_DATA_BR = '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/';
	const PATTERN_DATA_EN = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
	const PATTERN_HORA = '/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';
	const PATTERN_DATA_HORA_BR = '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4} ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';
	const PATTERN_DATA_HORA_MIN_BR = '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4} ([0-1][0-9]|2[0-3]):[0-5][0-9]$/';
	const PATTERN_DATA_HORA_EN = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/';
	const PATTERN_DATA_HORA_MIN_EN = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} ([0-1][0-9]|2[0-3]):[0-5][0-9]$/';
	const PATTERN_NUMERO_INT = '/^-?[0-9]+$/';
	const PATTERN_NUMERO_INT_BR = '/^-?[0-9]{1,3}(\.[0-9]{3})*$/';
	const PATTERN_CONTAINS_NUMERO_INT = '-?[0-9]+';
	const PATTERN_NUMERO_FLOAT_EN = '/^-?([0-9]+\.?[0-9]*|\.[0-9]+)$/';
	const PATTERN_NUMERO_FLOAT_BR = '/^-?(([0-9]{1,3}(\.[0-9]{3})*|[0-9]+)(,[0-9]+)?|(,[0-9]+))$/';
	const PATTERN_BOOLEAN = '/^(s|sim|1|true|verdadeiro|n|n(a|ã|Ã)o|0|fals[eo])$/i';
	const PATTERN_TRUE = '/^(s|sim|1|true|verdadeiro)$/i';
	const PATTERN_FALSE = '/^(n|n(a|ã|Ã)o|0|fals[eo])$/i';
	const PATTERN_TIPO_PESSOA = '/^(1|f|pf|(pessoa )?f(i|í)sica|2|3|4|j|pj|(pessoa )?jur(i|í)dica)$/i';
	const PATTERN_TIPO_PESSOA_PF = '/^(1|f|pf|(pessoa )?f(i|í)sica)$/i';
	const PATTERN_TIPO_PESSOA_PJ = '/^(2|3|4|j|pj|(pessoa )?jur(i|í)dica)$/i';

	const PATTERN_SEGURO_PROD_AUTO = 'AUTO|DUAS RODAS|MOTOS';
	const PATTERN_SEGURO_PROD_VIDA = 'VIDA|ACIDENTES PESSOAIS|ALTA RENDA|PROFISSIONAL|UNIQUE|SORTE FÁCIL';
	const PATTERN_SEGURO_PROD_RESIDENCIAL = 'RESID|CASA';
	//	const PATTERN_SEGURO_PROD_PATRIMONIAL = '^((?!VIDA|PREVID).)*EMPRESA((?!VIDA|PREVID).)*|CONDOMÍNIO'; //não roda no operador $regexMatch do aggregate
	const PATTERN_SEGURO_PROD_PATRIMONIAL = '^(?!.*VIDA.*)(?!.*PREVID.*).*EMPRESA(?!.*VIDA)(?!.*PREVID)|CONDOMÍNIO';
	const PATTERN_SEGURO_PROD_RURAL = 'RURAL|GRANIZO|MAQUINAS E EQUIPAMENTOS';
	const PATTERN_SEGURO_PROD_AGRICOLA = 'AGRICOLA';

	const PATTERN_CONSORCIO_SEGMENTO_AUTOMOVEIS = '^AUTOM.VEIS$';
	const PATTERN_CONSORCIO_SEGMENTO_IMOVEIS = '^IM.VEIS$';
	const PATTERN_CONSORCIO_SEGMENTO_MOTOCICLETAS = '^MOTOCICLETAS$';
	const PATTERN_CONSORCIO_SEGMENTO_SERVICOS = '^SERVI.OS$';
	const PATTERN_CONSORCIO_SEGMENTO_MOVEIS_PLANEJADOS = '^M.VEIS PLANEJADOS$';
	const PATTERN_CONSORCIO_SEGMENTO_CAMINHAO_TRATOR_EQUIP = '^CAMINH.O, TRATOR E EQUIP.$';

	const PATTERN_PRINCIPALIDADE_MUITO_BAIXA    = '^MUITO BAIXA$';
	const PATTERN_PRINCIPALIDADE_BAIXA          = '^BAIXA$';
	const PATTERN_PRINCIPALIDADE_MEDIA          = '^M.DIA$';
	const PATTERN_PRINCIPALIDADE_ALTA           = '^ALTA$';
	const PATTERN_PRINCIPALIDADE_MUITO_ALTA     = '^MUITO ALTA$';

	const PATTTERN_MULTI_DATE = '/^(\d{2}\D){2}\d{4}\D{1,3}(\d{2}\D){2}\d{4}$/';
	const PATTERN_METHOD = '/^(create|update)$/i';
	const PATTERN_EMAIL = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
	const PATTERN_UNICO_DIGITO = '/^\d$/';
	const PATTERN_CEP = '/^\d{5}\D?\d{3}$/';
	const PATTERN_ANO = '/^\d{0,4}$/';
	const PATTERN_TELEFONE = '/^\D?\d{2}\D?\s?\d{4,5}\D?\d{4}$/';
	const PATTERN_BASE64 = '/^data:(application\/.{1,70}|.{1,70}\/(png|txt));base64,[a-z0-9+\/]+={0,2}$/i';
	const PATTERN_SEXO =	'/^(?:A|F|M)$/i';

	/* Padrão de senha
		(?=.*\d)              - deve conter ao menos um dígito
		(?=.*[a-z])           - deve conter ao menos uma letra minúscula
		(?=.*[A-Z])           - deve conter ao menos uma letra maiúscula
		(?=.*[!@#$%^&*()_+-={}[]|:;<>,.?/])         - deve conter ao menos um caractere especial
		[0-9a-zA-Z!@#$%^&*()_+-={}[]|:;<>,.?/]{8,}  - deve conter ao menos 8 dos caracteres mencionados
	*/
	const PATTERN_SENHA = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\\¨~?´`!@#$%^&*()_+={}:;.<>,|\[\]\-\/])[0-9a-zA-Z\\¨~?´`!@#$%^&*()_+={}:;.<>,|\[\]\-\/]{8,}$/';

	public static function patternConta()
	{
		return self::PATTERN_CONTA;
	}
	public static function patternContainsCpfCnpj()
	{
		return self::PATTERN_CONTAINS_CPFCNPJ;
	}
	public static function patternCpfCnpj()
	{
		return self::PATTERN_CPFCNPJ;
	}
	public static function patternCpf()
	{
		return self::PATTERN_CPF;
	}
	public static function patternCnpj()
	{
		return self::PATTERN_CNPJ;
	}
	public static function patternCarteira()
	{
		return self::PATTERN_CARTEIRA;
	}
	public static function patternIsa()
	{
		return self::PATTERN_ISA;
	}
	public static function patternScore()
	{
		return self::PATTERN_SCORE;
	}
	public static function patternRating()
	{
		return self::PATTERN_RATING;
	}
	public static function patternDataEn()
	{
		return self::PATTERN_DATA_EN;
	}
	public static function patternDataBr()
	{
		return self::PATTERN_DATA_BR;
	}
	public static function patternHora()
	{
		return self::PATTERN_HORA;
	}
	public static function patternDataHoraBr()
	{
		return self::PATTERN_DATA_HORA_BR;
	}
	public static function patternDataHoraMinBr()
	{
		return self::PATTERN_DATA_HORA_MIN_BR;
	}
	public static function patternDataHoraEn()
	{
		return self::PATTERN_DATA_HORA_EN;
	}
	public static function patternDataHoraMinEn()
	{
		return self::PATTERN_DATA_HORA_MIN_EN;
	}
	public static function patternNumeroInt()
	{
		return self::PATTERN_NUMERO_INT;
	}
	public static function patternNumeroIntBr()
	{
		return self::PATTERN_NUMERO_INT_BR;
	}
	public static function patternContainsNumeroInt()
	{
		return self::PATTERN_CONTAINS_NUMERO_INT;
	}
	public static function patternNumeroFloatEn()
	{
		return self::PATTERN_NUMERO_FLOAT_EN;
	}
	public static function patternNumeroFloatBr()
	{
		return self::PATTERN_NUMERO_FLOAT_BR;
	}
	public static function patternBoolean()
	{
		return self::PATTERN_BOOLEAN;
	}
	public static function patternTrue()
	{
		return self::PATTERN_TRUE;
	}
	public static function patternFalse()
	{
		return self::PATTERN_FALSE;
	}
	public static function patternTipoPessoa()
	{
		return self::PATTERN_TIPO_PESSOA;
	}
	public static function patternTipoPessoaPf()
	{
		return self::PATTERN_TIPO_PESSOA_PF;
	}
	public static function patternTipoPessoaPj()
	{
		return self::PATTERN_TIPO_PESSOA_PJ;
	}
}
