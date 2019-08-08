<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    ini_set('display_errors', true);
    error_reporting(E_ALL);

    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';
    require_once '../lib/mail/enviar_email.php';

//    $protocolo  = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === false) ? 'http' : 'https';
//    $host       = $_SERVER['HTTP_HOST'];
//    $script     = $_SERVER['SCRIPT_NAME'];
//    $parametros = $_SERVER['QUERY_STRING'];
//    $metodo     = $_SERVER['REQUEST_METHOD'];
//    $UrlAtual   = $protocolo . '://' . $host . $script . '?' . $parametros;
//
//    echo "<br>Protocolo: ".$protocolo;
//    echo "<br>Host: ".$host;
//    echo "<br>Script: ".$script;
//    echo "<br>Parametros: ".$parametros;
//    echo "<br>Metodo: ".$metodo;
//    echo "<br>Url: ".$UrlAtual."<br><br><br><br>";
//
  
    $usar_locaweb = false;
    
    function hashSenhaUser($senha){
        return password_hash($senha, PASSWORD_BCRYPT, array('cost'=>12));
    }
    
    function logar($cliente, $login, $senha) {
        $ret = false;
        $cnf = Configuracao::getInstancia();
        $pdo = $cnf->db('', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = 
              "Select "
            . "    s.id_cliente  "
            . "  , s.id_servidor "
            . "  , s.matricula "
            . "  , s.nome "
            . "  , s.sexo "
            . "  , s.cpf "
            . "  , s.dt_nascimento "
            . "  , s.dt_admissao   "
            . "  , coalesce(nullif(trim(s.e_mail), ''), '...') as email "
            . "  , coalesce(s.senha, '...')  as senha "
            . "  , s.nivel_acesso "
            . "  , current_timestamp as ultimo_acesso "
            . "  , coalesce(x.senha_master, '...') as senha_master "
            . "from REMUN_SERVIDOR s "
//            . "  left join ( "
//            . "    Select "
//            . "        u.id_cliente "
//            . "      , u.e_mail "
//            . "      , coalesce(u.senha, '...')  as senha_master "
//            . "    from ADM_USUARIO u "
//            . "    where u.senha = '" . hashSenhaUser($senha) . "' "
//            . "  ) x on (x.id_cliente = s.id_cliente) "
            . "  left join (        "
            . "    Select first 1   "
            . "        u.id_cliente "
            . "      , u.e_mail     "
            . "      , coalesce(u.senha, '...')  as senha_master "
            . "    from ADM_USUARIO u "
            . "    where u.id in (    "
            . "        Select         "
            . "          min(ux.id)   "
            . "        from ADM_USUARIO ux "
            . "        where ux.id_cliente  = {$cliente} "
            . "      ) "
            . "  ) x on (x.id_cliente = s.id_cliente) "
            . "where s.id_cliente  = {$cliente} "
            . "  and s.id_servidor = {$login}   ";
        
        $res = $pdo->query($sql);
        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
            $ret = ( password_verify($senha, trim($obj->senha)) || password_verify($senha, trim($obj->senha_master)) );
            if ( $ret ) {
                $stm = $pdo->prepare(
                      'Update REMUN_SERVIDOR s Set '
                    . '  s.ultimo_acesso   = :ultimo_acesso '
                    . 'where s.id_cliente  = :id_cliente    '
                    . '  and s.id_servidor = :id_servidor   ');
                $stm->execute(array(
                    ':ultimo_acesso' => $obj->ultimo_acesso,
                    ':id_cliente'    => $cliente,
                    ':id_servidor'   => $login
                ));

                $pdo->commit();
                
                session_start();
                $_SESSION['unidade']['id'] = $cliente;
                $_SESSION['unidade']['un'] = md5($cliente);
                $_SESSION['unidade']['us'] = $login;
                $_SESSION['unidade']['pw'] = $obj->senha;
            }
        }
        
        return $ret;
    }
    
    function recuperarSenha($cliente, $servidor, $cpf, $dt_nascimento, $email) {
        $ret = false;
        $cnf = Configuracao::getInstancia();
        $pdo = $cnf->db('', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = 
              "Select "
            . "    s.id_cliente  "
            . "  , s.id_servidor "
            . "  , s.matricula "
            . "  , s.nome "
            . "  , s.sexo "
            . "  , s.cpf "
            . "  , s.dt_nascimento "
            . "  , s.dt_admissao   "
            . "  , coalesce(nullif(trim(s.e_mail), ''), '...') as email "
            . "  , coalesce(s.senha,  '...') as senha "
            . "  , s.nivel_acesso "
            //. "  , trim(coalesce(u.titulo_portal, u.nome)) as unidade "
            . "  , trim(coalesce(nullif(trim(u.titulo_portal), ''), trim(u.nome))) as unidade "
            . "  , coalesce(u.enviar_senha_email, 1) as enviar_senha_email "
            . "  , current_timestamp as ultimo_acesso "
            . "from REMUN_SERVIDOR s "
            . "  inner join ADM_CLIENTE u on (u.id = s.id_cliente) "
            . "where s.id_cliente  = {$cliente} "
            . "  and s.id_servidor = {$servidor} "
            . "  and s.cpf = '{$cpf}' ";
        
        $res = $pdo->query($sql);
        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
            $ret = (date('Y-m-d', strtotime($obj->dt_nascimento)) === $dt_nascimento) &&
                   ($obj->email === $email);
            
            if ( $ret ) {
                $retorno = array('servidor' => array());
                
                //$senhaEnvio = gerarSenha(8);
                $senhaEnvio = gerarSenhaNumero(7);
                $senhaBase  = hashSenha($senhaEnvio); // Hash da senha gerada para guardar na base de dados
                
                $stm = $pdo->prepare(
                      'Update REMUN_SERVIDOR s Set '
                    . '  s.senha = :senha '
                    . 'where s.id_cliente  = :id_cliente  '
                    . '  and s.id_servidor = :id_servidor ');
                $stm->execute(array(
                    ':senha'       => $senhaBase,
                    ':id_cliente'  => $cliente,
                    ':id_servidor' => $servidor
                ));

                $pdo->commit();
                
                $retorno['servidor'][0]['unidade']    = $obj->unidade;
                $retorno['servidor'][0]['matricula']  = $obj->matricula;
                $retorno['servidor'][0]['nome']       = $obj->nome;
                $retorno['servidor'][0]['cpf']        = $obj->cpf;
                $retorno['servidor'][0]['nascimento'] = date('d/m/Y', strtotime($obj->dt_nascimento));
                $retorno['servidor'][0]['email'] = $obj->email;
                $retorno['servidor'][0]['login'] = $obj->id_servidor;
                $retorno['servidor'][0]['senha'] = $senhaEnvio; //$obj->senha;
                $retorno['servidor'][0]['enviar_senha_email'] = $obj->enviar_senha_email;
                
                $ret = $retorno;
            }
        }
        
        return $ret;
    }
    
    function gravarSenha($cliente, $servidor, $cpf, $dt_nascimento, $email, $senha) {
        $ret = false;
        $cnf = Configuracao::getInstancia();
        $pdo = $cnf->db('', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = 
              "Select "
            . "    s.id_cliente  "
            . "  , s.id_servidor "
            . "  , s.matricula "
            . "  , s.nome "
            . "  , s.sexo "
            . "  , s.cpf "
            . "  , s.dt_nascimento "
            . "  , s.dt_admissao   "
            . "  , coalesce(s.e_mail, '...') as email "
            . "  , coalesce(s.senha,  '...') as senha "
            . "  , s.nivel_acesso "
            . "  , trim(coalesce(u.titulo_portal, u.nome)) as unidade "
            . "  , coalesce(u.enviar_senha_email, 1) as enviar_senha_email "
            . "  , current_timestamp as ultimo_acesso "
            . "from REMUN_SERVIDOR s "
            . "  inner join ADM_CLIENTE u on (u.id = s.id_cliente) "
            . "where s.id_cliente  = {$cliente} "
            . "  and s.id_servidor = {$servidor} "
            . "  and s.cpf = '{$cpf}' ";
        
        $res = $pdo->query($sql);
        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
            $ret = (date('Y-m-d', strtotime($obj->dt_nascimento)) === $dt_nascimento) &&
                   ($obj->email === $email);
            
            if ( $ret ) {
                $retorno = array('servidor' => array());
                
                $senhaEnvio = trim($senha);
                $senhaBase  = hashSenha($senhaEnvio); // Hash da senha gerada para guardar na base de dados
                
                $stm = $pdo->prepare(
                      'Update REMUN_SERVIDOR s Set '
                    . '  s.senha = :senha '
                    . 'where s.id_cliente  = :id_cliente  '
                    . '  and s.id_servidor = :id_servidor ');
                $stm->execute(array(
                    ':senha'       => $senhaBase,
                    ':id_cliente'  => $cliente,
                    ':id_servidor' => $servidor
                ));

                $pdo->commit();
                
                $retorno['servidor'][0]['unidade']    = $obj->unidade;
                $retorno['servidor'][0]['matricula']  = $obj->matricula;
                $retorno['servidor'][0]['nome']       = $obj->nome;
                $retorno['servidor'][0]['cpf']        = $obj->cpf;
                $retorno['servidor'][0]['nascimento'] = date('d/m/Y', strtotime($obj->dt_nascimento));
                $retorno['servidor'][0]['email'] = $obj->email;
                $retorno['servidor'][0]['login'] = $obj->id_servidor;
                $retorno['servidor'][0]['senha'] = $senhaEnvio; //$obj->senha;
                $retorno['servidor'][0]['enviar_senha_email'] = $obj->enviar_senha_email;
                
                $ret = $retorno;
            }
        }
        
        return $ret;
    }
    
    function gerarSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false){
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num  = '1234567890';

        $retorno     = '';
        $caracteres  = '';
        $caracteres .= $lmin;

        if ($maiusculas) $caracteres .= $lmai;
        if ($numeros)    $caracteres .= $num;

        $len = strlen($caracteres);

        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand-1];
        }
        
        return $retorno;
    }

    function gerarSenhaNumero($tamanho = 7){
        $num = '1234567890';

        $retorno    = '';
        $caracteres = '';
        
        for ($x = 1; $x <= $tamanho; $x++) {
            $caracteres .= $num;
        }

        $len = strlen($caracteres);

        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand - 1];
        }
        
        return $retorno;
    }

    function hashSenha($senha){
        return password_hash($senha, PASSWORD_BCRYPT, array('cost'=>12));
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ac'])) {
            switch ($_POST['ac']) {
                case 'login_servidor' : {
                    $parametros   = strip_tags(trim(filter_input(INPUT_POST, 'hash')));
                    $id_cliente   = strip_tags(trim(filter_input(INPUT_POST, 'id_cliente')));   //  15002 - CÂMARA MUNICIPAL DE SÃO SEBASTIÃO DA BOA VISTA
                    $nr_matricula = strip_tags(trim(filter_input(INPUT_POST, 'nr_matricula'))); //  935
                    $ds_senha     = strip_tags(trim(filter_input(INPUT_POST, 'ds_senha')));     //  DMb6Qc
                    
                    $parametros = ($parametros === ""?"?ac=":$parametros . "&ac=");
                    if ( ($id_cliente === "0") || ($nr_matricula === "") || ($ds_senha === "") ) {
                        header("location: ../login.php{$parametros}login_empty");
                    }
                    elseif (logar($id_cliente, $nr_matricula, $ds_senha)) {
                        header('location: ../servidor.php?un=' . $_SESSION['unidade']['un']);
                    } else {
                        header("location: ../login.php{$parametros}error_login");
                    }
                } break;    
                
                case 'testar_senha_atual' : {
                    $chave = explode('_', strip_tags(trim(filter_input(INPUT_POST, 'p0'))));
                    $senha = strip_tags(trim(filter_input(INPUT_POST, 'p1')));
                    
                    if ( ($chave[0] === "0") || ($chave[1] === "") || ($senha === "") ) {
                        echo "Informar senha atual";
                    }
                    elseif (!logar($chave[0], $chave[1], $senha)) {
                        echo "Senha inválida";
                    } else {
                        echo "OK";
                    }
                } break;    
            
                case 'login_lembrar' : {
                    $parametros    = strip_tags(trim(filter_input(INPUT_POST, 'r_hash')));
                    $id_cliente    = strip_tags(trim(filter_input(INPUT_POST, 'r_id_cliente')));    //  15002 - CÂMARA MUNICIPAL DE SÃO SEBASTIÃO DA BOA VISTA
                    $nr_matricula  = strip_tags(trim(filter_input(INPUT_POST, 'r_nr_matricula')));  //  935
                    $nr_cpf        = strip_tags(preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'r_nr_cpf')))); //  84461543234
                    $dt_nascimento = strip_tags(trim(filter_input(INPUT_POST, 'r_dt_nascimento'))); //  08/08/1986
                    $ds_email      = strip_tags(trim(filter_input(INPUT_POST, 'r_ds_email')));      //  ?
                    
                    $parametros = ($parametros === ""?"?ac=":$parametros . "&ac=");
                    
                    if ( ((int)$id_cliente === 0) || ($nr_matricula === "") || ($nr_cpf === "") || ($dt_nascimento === "") || ($ds_email === "") ) {
                        header("location: ../login.php{$parametros}remember_empty");
                    } 
                    elseif (($retorno = recuperarSenha($id_cliente, $nr_matricula, $nr_cpf, $dt_nascimento, $ds_email)) !== false ) {
                        if ( $retorno['servidor'][0]['enviar_senha_email'] === "1" ) {
                            $dados =  "<!DOCTYPE html>"
                                    . "<html>"
                                    . " <head>"
                                    . "     <meta charset='UTF-8'>"
                                    . "     <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>"
                                    . "     <title>DRH Transparencia - Informaçõe de Acesso</title>"
                                    . " </head>"
                                    . " <body>"
                                    . "     <style type='text/css'>"
                                    . "         html, body {"
                                    . "             font-family: sans-serif;"
                                    . "             height: 100%;"
                                    . "             background: #fff;"
                                    . "         }"
                                    . "         strong {"
                                    . "             font-weight: bold;"
                                    . "         }"
                                    . "     </style>"
                                    . "     <table>"
                                    . "         <tr>"
                                    . "             <td>Unidade &nbsp;&nbsp;&nbsp;</td>"
                                    . "             <td>{$retorno['servidor'][0]['unidade']}</td>"
                                    . "         </tr>"
                                    . "         <tr>"
                                    . "             <td>Servidor</td>"
                                    . "             <td>{$retorno['servidor'][0]['nome']}</td>"
                                    . "         </tr>"
                                    . "         <tr>"
                                    . "             <td>CPF</td>"
                                    . "             <td>" . formatarTexto("###.###.###-##", $retorno['servidor'][0]['cpf']) . "</td>"
                                    . "         </tr>"
                                    . "         <tr>"
                                    . "             <td>Login</td>"
                                    . "             <td><strong>{$retorno['servidor'][0]['login']}</strong></td>"
                                    . "         </tr>"
                                    . "         <tr>"
                                    . "             <td>Senha</td>"
                                    . "             <td><strong>{$retorno['servidor'][0]['senha']}</strong></td>"
                                    . "         </tr>"
                                    . "     </table>"
                                    . " </body>"
                                    . "</html>";

                            if ( enviarEmail($dados, "Envio de Senha", $retorno['servidor'][0]['nome'], $retorno['servidor'][0]['email'], $usar_locaweb) === true ) {
                                session_start();
                                $_SESSION['acao'] = "reenviar_senha";
                                $_SESSION['servidor']['unid']  = md5($id_cliente);
                                $_SESSION['servidor']['nome']  = $retorno['servidor'][0]['nome'];
                                $_SESSION['servidor']['email'] = $retorno['servidor'][0]['email'];
                                $_SESSION['servidor']['login'] = "...";
                                $_SESSION['servidor']['senha'] = "...";
                                header('location: ../confirmacao.php');
                            } else {
                                // Falha ao enviar e-mail

                            }
                        } else {
                            session_start();
                            $_SESSION['acao'] = "exibir_senha";
                            $_SESSION['servidor']['unid']  = md5($id_cliente);
                            $_SESSION['servidor']['nome']  = $retorno['servidor'][0]['nome'];
                            $_SESSION['servidor']['email'] = $retorno['servidor'][0]['email'];
                            $_SESSION['servidor']['login'] = $retorno['servidor'][0]['login'];
                            $_SESSION['servidor']['senha'] = $retorno['servidor'][0]['senha'];
                            header('location: ../confirmacao.php');
                        }
                    } else {
                        header("location: ../login.php{$parametros}incorrect_data");
                    }
                } break;    
                
                case 'login_altetar_senha' : {
                    $parametros    = strip_tags(trim(filter_input(INPUT_POST, 'hash')));
                    $id_unidade    = strip_tags(trim(filter_input(INPUT_POST, 'id_unidade')));   
                    $ds_unidade    = strip_tags(trim(filter_input(INPUT_POST, 'ds_unidade')));
                    $id_servidor   = strip_tags(trim(filter_input(INPUT_POST, 'id_servidor')));   
                    $nm_servidor   = strip_tags(trim(filter_input(INPUT_POST, 'nm_servidor')));   
                    $nr_cpf        = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_cpf')));   
                    $dt_nascimento = strip_tags(trim(filter_input(INPUT_POST, 'dt_nascimento')));
                    $ds_email      = strip_tags(trim(filter_input(INPUT_POST, 'ds_email')));   
                    $ds_senha      = strip_tags(trim(filter_input(INPUT_POST, 'ds_senha_confirma')));
                    
//                    if (($retorno = gravarSenha($id_unidade, $id_servidor, $nr_cpf, $dt_nascimento, $ds_email, $ds_senha)) !== false ) {
//                        $dados =  "<!DOCTYPE html>"
//                                . "<html>"
//                                . " <head>"
//                                . "     <meta charset='UTF-8'>"
//                                . "     <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>"
//                                . "     <title>DRH Transparencia - Informaçõe de Acesso</title>"
//                                . " </head>"
//                                . " <body>"
//                                . "     <style type='text/css'>"
//                                . "         html, body {"
//                                . "             font-family: sans-serif;"
//                                . "             height: 100%;"
//                                . "             background: #fff;"
//                                . "         }"
//                                . "         strong {"
//                                . "             font-weight: bold;"
//                                . "         }"
//                                . "     </style>"
//                                . "     <table>"
//                                . "         <tr>"
//                                . "             <td>Unidade &nbsp;&nbsp;&nbsp;</td>"
//                                . "             <td>{$retorno['servidor'][0]['unidade']}</td>"
//                                . "         </tr>"
//                                . "         <tr>"
//                                . "             <td>Servidor</td>"
//                                . "             <td>{$retorno['servidor'][0]['nome']}</td>"
//                                . "         </tr>"
//                                . "         <tr>"
//                                . "             <td>CPF</td>"
//                                . "             <td>" . formatarTexto("###.###.###-##", $retorno['servidor'][0]['cpf']) . "</td>"
//                                . "         </tr>"
//                                . "         <tr>"
//                                . "             <td>Login</td>"
//                                . "             <td><strong>{$retorno['servidor'][0]['login']}</strong></td>"
//                                . "         </tr>"
//                                . "         <tr>"
//                                . "             <td>Nova Senha</td>"
//                                . "             <td><strong>{$retorno['servidor'][0]['senha']}</strong></td>"
//                                . "         </tr>"
//                                . "     </table>"
//                                . " </body>"
//                                . "</html>";
//                        
//                        if ( enviarEmail($dados, "Envio de Senha", $retorno['servidor'][0]['nome'], $retorno['servidor'][0]['email'], false) === true ) {
//                            session_start();
//                            $_SESSION['acao'] = "senha_alterara";
//                            $_SESSION['servidor']['unid']  = md5($id_unidade);
//                            $_SESSION['servidor']['nome']  = $nm_servidor;
//                            $_SESSION['servidor']['email'] = $ds_email;
//                            header('location: ../confirmacao.php');
//                        }
//                    }
                    
                    $cnf = Configuracao::getInstancia();
                    $pdo = $cnf->db('', '');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $stm = $pdo->prepare(
                          'Update REMUN_SERVIDOR s Set '
                        . '  s.senha = :senha '
                        . 'where s.id_cliente  = :id_cliente  '
                        . '  and s.id_servidor = :id_servidor ');
                    $stm->execute(array(
                        ':senha'       => hashSenha($ds_senha),
                        ':id_cliente'  => $id_unidade,
                        ':id_servidor' => $id_servidor
                    ));

                    $pdo->commit();
                    
                    $dados =  "<!DOCTYPE html>"
                            . "<html>"
                            . " <head>"
                            . "     <meta charset='UTF-8'>"
                            . "     <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>"
                            . "     <title>DRH Transparencia - Informaçõe de Acesso</title>"
                            . " </head>"
                            . " <body>"
                            . "     <style type='text/css'>"
                            . "         html, body {"
                            . "             font-family: sans-serif;"
                            . "             height: 100%;"
                            . "             background: #fff;"
                            . "         }"
                            . "         strong {"
                            . "             font-weight: bold;"
                            . "         }"
                            . "     </style>"
                            . "     <table>"
                            . "         <tr>"
                            . "             <td>Unidade &nbsp;&nbsp;&nbsp;</td>"
                            . "             <td>{$ds_unidade}</td>"
                            . "         </tr>"
                            . "         <tr>"
                            . "             <td>Servidor</td>"
                            . "             <td>{$nm_servidor}</td>"
                            . "         </tr>"
                            . "         <tr>"
                            . "             <td>CPF</td>"
                            . "             <td>" . formatarTexto("###.###.###-##", $nr_cpf) . "</td>"
                            . "         </tr>"
                            . "         <tr>"
                            . "             <td>Login</td>"
                            . "             <td><strong>{$id_servidor}</strong></td>"
                            . "         </tr>"
                            . "         <tr>"
                            . "             <td>Nova Senha</td>"
                            . "             <td><strong>{$ds_senha}</strong></td>"
                            . "         </tr>"
                            . "     </table>"
                            . " </body>"
                            . "</html>";
                            
                    session_start();
                    $_SESSION['acao'] = "senha_alterara";
                    $_SESSION['servidor']['unid']  = md5($id_unidade);
                    $_SESSION['servidor']['nome']  = $nm_servidor;
                    $_SESSION['servidor']['email'] = $ds_email;
                    $_SESSION['servidor']['login'] = "...";
                    $_SESSION['servidor']['senha'] = "...";
                    
                    if ( enviarEmail($dados, "Envio de Nova Senha", $nm_servidor, $ds_email, $usar_locaweb) === true ) {
                        header('location: ../confirmacao.php');
                    } else {
                        // Falha ao enviar e-mail

                    }
                } break;
                
                case 'email_teste' : {
                    $parametros    = strip_tags(trim(filter_input(INPUT_POST, 'r_hash')));
                    $id_cliente    = strip_tags(trim(filter_input(INPUT_POST, 'r_id_cliente')));    //  15002 - CÂMARA MUNICIPAL DE SÃO SEBASTIÃO DA BOA VISTA
                    $nr_matricula  = strip_tags(trim(filter_input(INPUT_POST, 'r_nr_matricula')));  //  935
                    $nr_cpf        = strip_tags(preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'r_nr_cpf')))); //  844.615.432-34
                    $dt_nascimento = strip_tags(trim(filter_input(INPUT_POST, 'r_dt_nascimento'))); //  08/08/1986
                    $ds_email      = strip_tags(trim(filter_input(INPUT_POST, 'r_ds_email')));      //  marcel8886@hotmail.com
                    
                    $parametros = ($parametros === ""?"?ac=":$parametros . "&ac=");
                    
                    $ds_unidade  = "TESTE";
                    $nm_servidor = "Teste";
                    $id_servidor = $nr_matricula;
                    $ds_senha    = "TesteTeste";
                    
                    $dados =  "<!DOCTYPE html>"
                            . "<html>"
                            . " <head>"
                            . "     <meta charset='UTF-8'>"
                            . "     <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>"
                            . "     <title>DRH Transparencia - Informaçõe de Acesso</title>"
                            . " </head>"
                            . " <body>"
                            . "     <style type='text/css'>"
                            . "         html, body {"
                            . "             font-family: sans-serif;"
                            . "             height: 100%;"
                            . "             background: #fff;"
                            . "         }"
                            . "         strong {"
                            . "             font-weight: bold;"
                            . "         }"
                            . "     </style>"
                            . "     <table>"
                            . "         <tr>"
                            . "             <td>Unidade &nbsp;&nbsp;&nbsp;</td>"
                            . "             <td>{$ds_unidade}</td>"
                            . "         </tr>"
                            . "         <tr>"
                            . "             <td>Servidor</td>"
                            . "             <td>{$nm_servidor}</td>"
                            . "         </tr>"
                            . "         <tr>"
                            . "             <td>CPF</td>"
                            . "             <td>" . formatarTexto("###.###.###-##", $nr_cpf) . "</td>"
                            . "         </tr>"
                            . "         <tr>"
                            . "             <td>Login</td>"
                            . "             <td><strong>{$id_servidor}</strong></td>"
                            . "         </tr>"
                            . "         <tr>"
                            . "             <td>Nova Senha</td>"
                            . "             <td><strong>{$ds_senha}</strong></td>"
                            . "         </tr>"
                            . "     </table>"
                            . " </body>"
                            . "</html>";
                    
                    if (($retorno = recuperarSenha($id_cliente, $nr_matricula, $nr_cpf, $dt_nascimento, $ds_email)) !== false ) {
                        if ( enviarEmailTeste($dados, "Teste de Envio", $retorno['servidor'][0]['nome'], $retorno['servidor'][0]['email'], false) === true ) {
                            echo "OK";
                        } else {
                            echo "Erro";
                        }
                    }        
                } break;
            }
        } else {
            ;
        }
    }
