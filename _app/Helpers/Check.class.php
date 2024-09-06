<?php

/**
 * Check.class [ HELPER ]
 * Classe responável por manipular e validar dados do sistema!
 *
 * @copyright (c) 2014, Marcos Orlando - ZEN AGÊNCIA WEB
 */
class Check {

    private static $Data;
    private static $Format;

    /**
     * <b>Verifica E-mail:</b> Executa validação de formato de e-mail. Se for um email válido retorna true, ou retorna false.
     * @param STRING $Email = Uma conta de e-mail
     * @return BOOL = True para um email válido, ou false
     */
    public static function Email($Email) {
        self::$Data = (string) $Email;
        self::$Format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';

        if (preg_match(self::$Format, self::$Data)):
            return true;
        else:
            return false;
        endif;
    }

    /**
     * <b>Tranforma URL:</b> Tranforma uma string no formato de URL amigável e retorna a string convertida!
     * @param STRING $Name = Uma string qualquer
     * @return STRING = $Data = Uma URL amigável válida
     */
    public static function Name($Name) {
        self::$Format = array();
        self::$Format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        self::$Format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

        self::$Data = strtr(utf8_decode($Name), utf8_decode(self::$Format['a']), self::$Format['b']);
        self::$Data = strip_tags(trim(self::$Data));
        self::$Data = str_replace(' ', '-', self::$Data);
        self::$Data = str_replace(array('-----', '----', '---', '--'), '-', self::$Data);

        return strtolower(utf8_encode(self::$Data));
    }

    /**
     * <b>Checa CPF:</b> Informe um CPF para checar sua validade via algoritmo!
     * @param STRING $CPF = CPF com ou sem pontuação
     * @return BOLEAM = True se for um CPF válido
     */
    public static function CPF($Cpf) {
        self::$Data = preg_replace('/[^0-9]/', '', $Cpf);

        if (strlen(self::$Data) != 11):
            return false;
        endif;

        $digitoA = 0;
        $digitoB = 0;

        for ($i = 0, $x = 10; $i <= 8; $i++, $x--) {
            $digitoA += self::$Data[$i] * $x;
        }

        for ($i = 0, $x = 11; $i <= 9; $i++, $x--) {
            if (str_repeat($i, 11) == self::$Data) {
                return false;
            }
            $digitoB += self::$Data[$i] * $x;
        }

        $somaA = (($digitoA % 11) < 2 ) ? 0 : 11 - ($digitoA % 11);
        $somaB = (($digitoB % 11) < 2 ) ? 0 : 11 - ($digitoB % 11);

        if ($somaA != self::$Data[9] || $somaB != self::$Data[10]) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * <b>Checa CNPJ:</b> Informe um CNPJ para checar sua validade via algoritmo!
     * @param STRING $CNPJ = CNPJ com ou sem pontuação
     * @return BOLEAM = True se for um CNJP válido
     */
    public static function CNPJ($Cnpj) {
        self::$Data = (string) $Cnpj;
        self::$Data = preg_replace('/[^0-9]/', '', self::$Data);

        if (strlen(self::$Data) != 14):
            return false;
        endif;

        $A = 0;
        $B = 0;

        for ($i = 0, $c = 5; $i <= 11; $i++, $c--):
            $c = ($c == 1 ? 9 : $c);
            $A += self::$Data[$i] * $c;
        endfor;

        for ($i = 0, $c = 6; $i <= 12; $i++, $c--):
            if (str_repeat($i, 14) == self::$Data):
                return false;
            endif;
            $c = ($c == 1 ? 9 : $c);
            $B += self::$Data[$i] * $c;
        endfor;

        $somaA = (($A % 11) < 2) ? 0 : 11 - ($A % 11);
        $somaB = (($B % 11) < 2) ? 0 : 11 - ($B % 11);

        if (strlen(self::$Data) != 14):
            return false;
        elseif ($somaA != self::$Data[12] || $somaB != self::$Data[13]):
            return false;
        else:
            return true;
        endif;
    }

    /**
     * <b>Tranforma Data:</b> Transforma uma data no formato DD/MM/YY em uma data no formato YYYY-MM-DD!
     * @param STRING $Name = Data em (d/m/Y)
     * @return STRING = $Data = Data no formato YYYY-MM-DD!
     */
    public static function Nascimento($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('/', self::$Format[0]);

        if (checkdate(self::$Data[1], self::$Data[0], self::$Data[2])):
            self::$Data = self::$Data[2] . '-' . self::$Data[1] . '-' . self::$Data[0];
            return self::$Data;
        else:
            return false;
        endif;
    }

    /**
     * <b>Tranforma TimeStamp:</b> Transforma uma data no formato DD/MM/YY em uma data no formato TIMESTAMP!
     * @param STRING $Name = Data em (d/m/Y) ou (d/m/Y H:i:s)
     * @return STRING = $Data = Data no formato timestamp!
     */
    public static function Data($Data) {
        self::$Format = explode(' ', $Data);
        self::$Data = explode('/', self::$Format[0]);

        if (!checkdate(self::$Data[1], self::$Data[0], self::$Data[2])):
            return false;
        else:
            if (empty(self::$Format[1])):
                self::$Format[1] = date('H:i:s');
            endif;

            self::$Data = self::$Data[2] . '-' . self::$Data[1] . '-' . self::$Data[0] . ' ' . self::$Format[1];
            return self::$Data;
        endif;
    }

    /**
     * <b>Limita os Palavras:</b> Limita a quantidade de palavras a serem exibidas em uma string!
     * @param STRING $String = Uma string qualquer
     * @param INT $name Description INT = $Limite = String limitada pelo $Limite
     */
    public static function Words($String, $Limite, $Pointer = null) {
        self::$Data = strip_tags(trim($String));
        self::$Format = (int) $Limite;

        $ArrWords = explode(' ', self::$Data);
        $NumWords = count($ArrWords);
        $NewWords = implode(' ', array_slice($ArrWords, 0, self::$Format));

        $Pointer = (empty($Pointer) ? '...' : ' ' . $Pointer );
        $Result = ( self::$Format < $NumWords ? $NewWords . $Pointer : self::$Data );
        return $Result;
    }

    /**
     * <b>Limita os Caracteres:</b> Limita a quantidade de letras a serem exibidas em uma string!
     * @param STRING $String = Uma string qualquer
     * @param INT $name Description INT = $Limite = String limitada pelo $Limite
     */
    public static function Chars($String, $Limite) {
        self::$Data = strip_tags($String);
        self::$Format = $Limite;
        if (strlen(self::$Data) <= self::$Format) {
            return self::$Data;
        } else {
            $subStr = strrpos(substr(self::$Data, 0, self::$Format), ' ');
            return substr(self::$Data, 0, $subStr) . '...';
        }
    }

    /**
     * <b>Checa Nº($phone_number) e aplica urlencode($message):</b> Informe um WHATSAPP NUMBER E UMA MENSAGEM para
     * receber o número somente e a mensagem otimizada.
     * @param $phone_number
     * @param $message
     * @return string
     */
    public static function WhatsMessage($phone_number, $message)
    {
        $phone_number = '55' . preg_replace(
                '/[\D]/',
                '',
                $phone_number
            );

        $phone_number = trim(str_replace([' ', '(',')','-'], '', $phone_number));

        self::$Data = "https://wa.me/{$phone_number}?text=" . urlencode($message);

        return self::$Data;
    }

	public static function clearNumber($phone_number)
	{
		$phone_number = '55' . preg_replace(
				'/[\D]/',
				'',
				$phone_number
			);

		self::$Data = trim(str_replace([' ', '(',')','-'], '', $phone_number));

		return self::$Data;
	}

	public static function getCapilalize($str)
	{
		$str = ucwords(mb_strtolower($str));
		self::$Data = str_replace([
			' De ',
			' Da ',
			' Do ',
			' Das ',
			' Dos ',
			' Em ',
			' As ',
			' Os '
		], [
			' de ',
			' da ',
			' do ',
			' das ',
			' dos ',
			' em ',
			' as ',
			' os '
		], $str);
		return self::$Data;
	}

	/**
	 * @param mixed $Format
	 */
	public static function Salutation()
	{
		$hour = date('H');
		self::$Format = (($hour > 0) && ($hour <= 12)) ? 'Bom dia!' : ((($hour > 12) && ($hour <= 18)) ? 'Boa tarde! ' : 'Boa noite!');
		return self::$Format;
	}


    /**
     * <b>NewPass:</b> Gera uma nova senha aleatória para um usuário!
     * @param INT $tamanho = Quantidade de caracteres na senha
     * @param BOOL $maiusculas Usar letras maiusculas (ABCDEFGHIJKLMNOPQRSTUVWXYZ)
     * @param BOOL $numeros Usar numeros (1234567890)
     * @param BOOL $simbolos Usar simbolos (!@#$%*-')
     */
    public static function NewPass($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false) {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';

        $caracteres .= $lmin;
        if ($maiusculas):
            $caracteres .= $lmai;
        endif;
        if ($numeros):
            $caracteres .= $num;
        endif;
        if ($simbolos):
            $caracteres .= $simb;
        endif;

        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand - 1];
        }
        return $retorno;
    }

	/**
	 * @param $legend string - legenda do campo
	 * @param $name string - nome do campo input
	 * @param $status boolean - 0 / 1
	 * @param $on string - data-on
	 * @param $off string - data-off
	 *
	 * @return string
	 */
	public static function switchOnOff($name, $status, $legend = null, $on = 'ON', $off = 'OFF'): string
	{
		return "<p class='p_switch'><b>{$legend}</b> <span class='switch'> <input name='{$name}' type='checkbox' id='{$name}' value='1'" .
			($status ==
			1 ? 'checked' : '') . "> <label for='{$name}' data-on='{$on}' data-off='{$off}'></label> </span></p>";

	}

	public static function getSectionContent($html, $seletor) {
		// Cria um novo objeto DOMDocument
		$dom = new DOMDocument();

		// Carrega o HTML na variável DOMDocument
		// Supressão de erros usando '@' para evitar warnings com caracteres não UTF-8
		@$dom->loadHTML($html);

		// Encontra o elemento pela classe 'section-page'
		$xpath = new DOMXPath($dom);
		$sections = $xpath->query("//[contains(@class, '{$seletor}')]");

		// Verifica se a section foi encontrada
		if ($sections->length > 0) {
			$sectionContent = "";

			// Concatena o conteúdo das sections encontradas
			foreach ($sections as $section) {
				$sectionContent .= $dom->saveHTML($section);
			}

			return $sectionContent;
		} else {
			return "Section with class '{$seletor}' not found.";
		}
	}
}
