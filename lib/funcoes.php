<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

/*
            function valida_cnpj($cnpj) {
            // Deixa o CNPJ com apenas números
            $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

            // Garante que o CNPJ é uma string
            $cnpj = (string) $cnpj;

            // O valor original
            $cnpj_original = $cnpj;

            // Captura os primeiros 12 números do CNPJ
            $primeiros_numeros_cnpj = substr($cnpj, 0, 12);

            if (!function_exists('multiplica_cnpj')) {

                function multiplica_cnpj($cnpj, $posicao = 5) {
                    // Variável para o cálculo
                    $calculo = 0;

                    // Laço para percorrer os item do cnpj
                    for ($i = 0; $i < strlen($cnpj); $i++) {
                        // Cálculo mais posição do CNPJ * a posição
                        $calculo = $calculo + ( $cnpj[$i] * $posicao );

                        // Decrementa a posição a cada volta do laço
                        $posicao--;

                        // Se a posição for menor que 2, ela se torna 9
                        if ($posicao < 2) {
                            $posicao = 9;
                        }
                    }
                    // Retorna o cálculo
                    return $calculo;
                }

            }

            // Faz o primeiro cálculo
            $primeiro_calculo = multiplica_cnpj($primeiros_numeros_cnpj);

            // Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
            // Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
            $primeiro_digito = ( $primeiro_calculo % 11 ) < 2 ? 0 : 11 - ( $primeiro_calculo % 11 );

            // Concatena o primeiro dígito nos 12 primeiros números do CNPJ
            // Agora temos 13 números aqui
            $primeiros_numeros_cnpj .= $primeiro_digito;

            // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
            $segundo_calculo = multiplica_cnpj($primeiros_numeros_cnpj, 6);
            $segundo_digito = ( $segundo_calculo % 11 ) < 2 ? 0 : 11 - ( $segundo_calculo % 11 );

            // Concatena o segundo dígito ao CNPJ
            $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

            // Verifica se o CNPJ gerado é idêntico ao enviado
            if ($cnpj === $cnpj_original) {
                return true;
            }
        }

        function valida_cpf( $cpf = false ) {
            // Exemplo de CPF: 025.462.884-23

            if ( ! function_exists('calc_digitos_posicoes') ) {
                function calc_digitos_posicoes( $digitos, $posicoes = 10, $soma_digitos = 0 ) {
                    // Faz a soma dos dígitos com a posição
                    // Ex. para 10 posições: 
                    //   0    2    5    4    6    2    8    8   4
                    // x10   x9   x8   x7   x6   x5   x4   x3  x2
                    //   0 + 18 + 40 + 28 + 36 + 10 + 32 + 24 + 8 = 196
                    for ( $i = 0; $i < strlen( $digitos ); $i++  ) {
                        $soma_digitos = $soma_digitos + ( $digitos[$i] * $posicoes );
                        $posicoes--;
                    }

                    // Captura o resto da divisão entre $soma_digitos dividido por 11
                    // Ex.: 196 % 11 = 9
                    $soma_digitos = $soma_digitos % 11;

                    // Verifica se $soma_digitos é menor que 2
                    if ( $soma_digitos < 2 ) {
                        // $soma_digitos agora será zero
                        $soma_digitos = 0;
                    } else {
                        // Se for maior que 2, o resultado é 11 menos $soma_digitos
                        // Ex.: 11 - 9 = 2
                        // Nosso dígito procurado é 2
                        $soma_digitos = 11 - $soma_digitos;
                    }

                    // Concatena mais um dígito aos primeiro nove dígitos
                    // Ex.: 025462884 + 2 = 0254628842
                    $cpf = $digitos . $soma_digitos;

                    // Retorna
                    return $cpf;
                }
            }

            // Verifica se o CPF foi enviado
            if ( ! $cpf ) {
                return false;
            }

            // Remove tudo que não é número do CPF
            // Ex.: 025.462.884-23 = 02546288423
            $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

            // Verifica se o CPF tem 11 caracteres
            // Ex.: 02546288423 = 11 números
            if ( strlen( $cpf ) != 11 ) {
                return false;
            }   

            // Captura os 9 primeiros dígitos do CPF
            // Ex.: 02546288423 = 025462884
            $digitos = substr($cpf, 0, 9);

            // Faz o cálculo dos 9 primeiros dígitos do CPF para obter o primeiro dígito
            $novo_cpf = calc_digitos_posicoes( $digitos );

            // Faz o cálculo dos 10 dígitos do CPF para obter o último dígito
            $novo_cpf = calc_digitos_posicoes( $novo_cpf, 11 );

            // Verifica se o novo CPF gerado é idêntico ao CPF enviado
            if ( $novo_cpf === $cpf ) {
                // CPF válido
                return true;
            } else {
                // CPF inválido
                return false;
            }
        }






        // Verifica o CPF
        if ( valida_cpf( '47039216507' ) ) {
                echo "CPF é válido. <br>";
        } else {
                echo "CPF Inválido.  <br>";
        }



        // Valida um CNPJ
        if ( valida_cnpj('12345678000195') ) {
                echo "CNPJ correto.  <br>";
        } else {
                echo "CNPJ inválido.  <br>";
        }
  
        fuction cpfOuCnpj($cpfoucnpj){
            //Caso seja CNPJ
            if(strlen($cpfoucnpj) == 14) {
                valida_cpf($cpfoucnpj);
            }

            //Caso seja CPF
            if(strlen($cpfoucnpj) == 11) {
                valida_cnpj($cpfoucnpj);
            }
        }
  */

function debug($array, $die = null) {
    echo '<pre>';
    print_r($array);
    echo '</pre></hr />';
    if( $die !== null) {
        die;
    }
}

function guid() {
    if (function_exists('com_create_guid')){
        return com_create_guid();
    } else {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
        return $uuid;
    }
}

function IniciaisNome($nome){
    $str = strtoupper($nome); 
    
    $str = str_replace(" DE ",  " ", $str); 
    $str = str_replace(" DA ",  " ", $str); 
    $str = str_replace(" DI ",  " ", $str); 
    $str = str_replace(" DO ",  " ", $str); 
    $str = str_replace(" DU ",  " ", $str); 
    $str = str_replace(" E ",   " ", $str); 
    $str = str_replace(" DOS ", " ", $str); 
    $str = str_replace(" DAS ", " ", $str); 

    $arr = explode(" ", $str);
    $ini = "";
    
    for ($i = 0; $i < count($arr); $i++) {
        $ini .= substr($arr[$i], 0, 1);
    }
    
    return $ini; 
}

function validarCPF($cpf = null) {
 
    // Verifica se um número foi informado
    if(empty($cpf)) {
        return false;
    }
 
    // Elimina possivel mascara
    //$cpf = ereg_replace('[^0-9]', '', $cpf);
    $cpf = preg_replace("/[^0-9]/", "", trim($cpf) );
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
     
    // Verifica se o numero de digitos informados é igual a 11 
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se nenhuma das sequências invalidas abaixo 
    // foi digitada. Caso afirmativo, retorna falso
    else if ($cpf == '00000000000' || 
        $cpf == '11111111111' || 
        $cpf == '22222222222' || 
        $cpf == '33333333333' || 
        $cpf == '44444444444' || 
        $cpf == '55555555555' || 
        $cpf == '66666666666' || 
        $cpf == '77777777777' || 
        $cpf == '88888888888' || 
        $cpf == '99999999999') {
        return false;
     // Calcula os digitos verificadores para verificar se o
     // CPF é válido
     } else {   
         
        for ($t = 9; $t < 11; $t++) {
             
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
 
        return true;
    }
}

function formatarTexto($mascara, $string) {
    $string = str_replace(" ", "", $string);
    for ($i = 0; $i < strlen($string); $i++) {
        $mascara[strpos($mascara, "#")] = $string[$i];
    }
    return $mascara;
}

function validarData($data, $formato = 'DD/MM/AAAA') {
    switch($formato) {
        case 'DD-MM-AAAA':
        case 'DD/MM/AAAA':
            $d = (int)substr($data, 0, 2);
            $m = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAA/MM/DD':
        case 'AAAA-MM-DD':
            $a = (int)substr($data, 0, 4); 
            $m = (int)substr($data, 5, 2);
            $d = (int)substr($data, 8, 2);
            break;

        case 'AAAA/DD/MM':
        case 'AAAA-DD-MM':
            $a = (int)substr($data, 0, 4); 
            $d = (int)substr($data, 5, 2);
            $m = (int)substr($data, 8, 2);
            break;

        case 'MM-DD-AAAA':
        case 'MM/DD/AAAA':
            $m = (int)substr($data, 0, 2);
            $d = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAAMMDD':
            $a = (int)substr($data, 0, 4);
            $m = (int)substr($data, 4, 2);
            $d = (int)substr($data, 6, 2);
            break;

        case 'AAAADDMM':
            $a = (int)substr($data, 0, 4);
            $d = (int)substr($data, 4, 2);
            $m = (int)substr($data, 6, 2);
            break;

        default:
            throw new Exception( "Formato de data inválido");
    }
    
    return checkdate($m, $d, $a);
}

function getDescricaoMes($data, $formato = 'DD/MM/AAAA') {
    switch($formato) {
        case 'DD-MM-AAAA':
        case 'DD/MM/AAAA':
            $d = (int)substr($data, 0, 2);
            $m = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAA/MM/DD':
        case 'AAAA-MM-DD':
            $a = (int)substr($data, 0, 4); 
            $m = (int)substr($data, 5, 2);
            $d = (int)substr($data, 8, 2);
            break;

        case 'AAAA/DD/MM':
        case 'AAAA-DD-MM':
            $a = (int)substr($data, 0, 4); 
            $d = (int)substr($data, 5, 2);
            $m = (int)substr($data, 8, 2);
            break;

        case 'MM-DD-AAAA':
        case 'MM/DD/AAAA':
            $m = (int)substr($data, 0, 2);
            $d = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAAMMDD':
            $a = substr($data, 0, 4);
            $m = substr($data, 4, 2);
            $d = substr($data, 6, 2);
            break;

        case 'AAAADDMM':
            $a = substr($data, 0, 4);
            $d = substr($data, 4, 2);
            $m = substr($data, 6, 2);
            break;

        default:
            throw new Exception( "Formato de data inválido");
    }
    
    $descricao = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    return $descricao[(int)$m - 1];
}

function getDataExtenso($data_in) {
    $dt  = explode("/", $data_in);
    $mes = array("", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    return $dt[0] . " de " . $mes[intval($dt[1])] . " de " . $dt[2];
}

function getDescricaoMes_v2($value) {
    $intValue = empty($value) ? 0 : intVal( $value );
    $meses = ['No-Value','JANEIRO','FEVEREIRO','MARÇO','ABRIL','MAIO','JUNHO','JULHO','AGOSTO','SETEMBBRO','OUTUBRO','NOVEMBRO','DEZEMBRO','DEC. TERC. 1º PARCELA','DEC. TERC. PARCELA FINAL','ABONO FUNDEB'];
    return $meses[$intValue]; 
}

function getAbreviacaoMes($data, $formato = 'DD/MM/AAAA') {
    switch($formato) {
        case 'DD-MM-AAAA':
        case 'DD/MM/AAAA':
            $d = (int)substr($data, 0, 2);
            $m = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAA/MM/DD':
        case 'AAAA-MM-DD':
            $a = (int)substr($data, 0, 4); 
            $m = (int)substr($data, 5, 2);
            $d = (int)substr($data, 8, 2);
            break;

        case 'AAAA/DD/MM':
        case 'AAAA-DD-MM':
            $a = (int)substr($data, 0, 4); 
            $d = (int)substr($data, 5, 2);
            $m = (int)substr($data, 8, 2);
            break;

        case 'MM-DD-AAAA':
        case 'MM/DD/AAAA':
            $m = (int)substr($data, 0, 2);
            $d = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAAMMDD':
            $a = substr($data, 0, 4);
            $m = substr($data, 4, 2);
            $d = substr($data, 6, 2);
            break;

        case 'AAAADDMM':
            $a = substr($data, 0, 4);
            $d = substr($data, 4, 2);
            $m = substr($data, 6, 2);
            break;

        default:
            throw new Exception( "Formato de data inválido");
    }
    
    $descricao = array("Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez");
    return strtoupper($descricao[(int)$m - 1]);
}

function excluirArquivosJson($pasta, $token_user) {
    $filtro = (trim($token_user) === ""?md5(date("r") . "admin"):$token_user) . ".json";

    if(is_dir($pasta)) {
        $diretorio = dir($pasta);

        while($arquivo = $diretorio->read()) {
            if (($arquivo !== '.') && ($arquivo !== '..') && (strpos($arquivo, $filtro) !== false)) {
                unlink($pasta . $arquivo);
            }
        }

        $diretorio->close();
    }
}    

function leftStr($str, $length) {
     return substr($str, 0, $length);
}

function rightStr($str, $length) {
     return substr($str, -$length);
}

function getTagSelected($constante, $value) {
    if ( $constante === $value  ) {
      return "selected='selected'";
    } else {
        return "";
    }
}

function getGuidEmpty() {
    return "{00000000-0000-0000-0000-000000000000}";
}

function encript($value) {
    $chave   = "d033e22ae348aeb5660fc2140aec35850c4da997"; // admin (md5)
    $data    = base64_encode($value);
    $tam     = strlen($data);
    $posic   = rand(0, $tam);
    $retorno = "";
    if ( $posic === $tam ) {
        $retorno = leftStr($data, $posic) . $chave;
    } else {
        $retorno = leftStr($data, $posic) . $chave . rightStr($data, $tam - $posic);
    }
    return base64_encode($retorno);
}

function decript($value) {
    $chave   = "d033e22ae348aeb5660fc2140aec35850c4da997"; // admin (md5)
    $data    = base64_decode($value);
    $retorno = str_replace($chave, "", $data);
    return base64_decode($retorno);
}

function estaEncript($value) {
    $str = decript($value);
    return ($str !== "");
}

function getKeysDados($dados, $extensao = null) {
    //var_dump($dados);die;
    //var_dump(array_keys($dados));die;
    //var_dump('#'. implode( "#,#", array_keys($dados) ) .'#');die;
    $keys = '#'. implode( "#,#", array_keys($dados) ) .'#';
    $keys = explode(',', $keys);
    //var_dump($keys);die;
    return $keys;
}

function converteDados($dados, $template) {
    //debug($dados, true);die;
    $keys = getKeysDados($dados);
    //debug($keys, true);die;
    $values = array_values($dados);
    //debug($values, true);die;
    //debug(str_replace($keys, $values, $template));die;
    return str_replace($keys, $values, $template);
}

function removerAcentos($string) {
    $what = array( 'ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','Ã','À','Á','Ê','É','Í','Õ','Ó','Ú','ñ','Ñ','ç','Ç');
    $by   = array( 'a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','A','E','E','I','O','O','U','n','n','c','C');
    return str_replace($what, $by, $string);
}

function removerCaracteresEspecais($string) {
    $what = array( 'ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','Ã','À','Á','Ê','É','Í','Õ','Ó','Ú','ñ','Ñ','ç','Ç','-','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º' );
    $by   = array( 'a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','A','E','E','I','O','O','U','n','n','c','C','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_' );
    return str_replace($what, $by, $string);
}