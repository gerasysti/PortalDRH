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
    require_once '../lib/classes/dao.php';
    require_once '../lib/funcoes.php';
    require_once './index_dao.php';
    
    session_start();
    $hash    = (!isset($_SESSION['acesso'])?md5("Erro"):(!isset($_SESSION['acesso']['id'])?md5("Erro"):$_SESSION['acesso']['id']));
    $cliente = (!isset($_SESSION['acesso']['id_cliente'])?-1:intval($_SESSION['acesso']['id_cliente']));
    $user_id = (!isset($_SESSION['acesso']['id_usuario'])?-1:intval($_SESSION['acesso']['id_usuario']));
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ac'])) {
            switch ($_POST['ac']) {
                case 'consultar_usuario' : {
                    try {
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        $us = trim(filter_input(INPUT_POST, 'us'));
                        $to = trim(filter_input(INPUT_POST, 'to'));
                        $ps = trim(filter_input(INPUT_POST, 'ps'));
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th>ID</th>";
                        $tabela .= "            <th>Nome</th>";
                        $tabela .= "            <th>E-mail</th>";
                        $tabela .= "            <th>Cliente</th>";
                        $tabela .= "            <th>CNPJ</th>";
                        //$tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>Último acesso</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: left;'>Último acesso</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        $sql = 
                             "Select "
                            ."    coalesce(u.id_cliente, 0) as id_cliente "
                            ."  , u.id "
                            ."  , u.nome "
                            ."  , u.e_mail "
                            ."  , u.ultimo_acdesso "
                            ."  , u.exe_ano "
                            ."  , u.administrar_portal "
                            ."  , u.lancar_eventos     "
                            ."  , u.lancar_ch_professores "
                            ."  , coalesce(u.situacao, 0) as situacao "
                            ."  , case when coalesce(trim(u.senha), '') = '' then 0 else 1 end senha "
                            ."  , coalesce(c.nome, 'Administração do Sistema') as cliente_nome "
                            ."  , coalesce(c.cnpj, '00000000000000') as cliente_cnpj "
                            ."from ADM_USUARIO u "
                            ."  left join ADM_CLIENTE c on (c.id = u.id_cliente) ";

                        if ($cliente !== 0) $sql .= "where u.id_cliente = {$cliente} " ;
                        
                        $sql .= "order by u.nome"; 
                        
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $id     = str_pad($obj->id, 3, "0", STR_PAD_LEFT);
                            $nome   = (!empty($obj->nome)?$obj->nome:"&nbsp;");
                            $e_mail = (!empty($obj->e_mail)?$obj->e_mail:"&nbsp;");
                            
                            $ultimo_acdesso = (!empty($obj->ultimo_acdesso)?date('d/m/Y H:i:s', strtotime($obj->ultimo_acdesso)):"&nbsp;");
                            $id_cliente     = $obj->id_cliente;
                            $cliente_nome   = (!empty($obj->cliente_nome)?$obj->cliente_nome:"&nbsp;");
                            $cliente_cnpj   = (!empty($obj->cliente_cnpj)?$obj->cliente_cnpj:"&nbsp;");
                            $situacao = $obj->situacao;
                            
                            $input = 
                                  "<input type='hidden' id='id_{$id}' value='{$id}'>"
                                . "<input type='hidden' id='id_cliente_{$id}' value='{$id_cliente}'>"
                                . "<input type='hidden' id='nome_{$id}' value='{$nome}'>"
                                . "<input type='hidden' id='email_{$id}' value='{$e_mail}'>"
                                . "<input type='hidden' id='ultimo_acesso_{$id}' value='{$ultimo_acdesso}'>"
                                . "<input type='hidden' id='administrar_portal_{$id}' value='{$obj->administrar_portal}'>"
                                . "<input type='hidden' id='lancar_eventos_{$id}' value='{$obj->lancar_eventos}'>"
                                . "<input type='hidden' id='lancar_ch_professores_{$id}' value='{$obj->lancar_ch_professores}'>"
                                . "<input type='hidden' id='situacao_{$id}' value='{$situacao}'>";
                            
                            
                            $icon_ed = "<button id='editar_usuario_{$id}'  class='btn btn-round btn-primary' title='Editar Registro'  onclick='editarUsuario(this.id)'><i class='glyph-icon icon-edit'></i></button>";
                            $icon_ex = "<button id='excluir_usuario_{$id}' class='btn btn-round btn-primary' title='Excluir Registro' onclick='excluirUsuario(this.id)'><i class='glyph-icon icon-trash'></i></button>";
                            //$icon_ed = "<a id='editar_usuario_{$id}' href='javascript:void(0);' title='Editar Registro' onclick='editarUsuario(this.id)'><i class='glyph-icon icon-edit'></i></a>";
                            $icon_pw = "&nbsp;";
                            
                            if (intval($obj->senha) === 0) {
                                $icon_pw = "<i class='glyph-icon icon-key'></i>";
                            } else {
                                $icon_pw = "<i class='glyph-icon icon-check-square-o'></i>";
                            }
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr class='custom-font-size-10' id='linha_{$id}'>";
                            $tabela .= "        <td>{$id}</td>";
                            $tabela .= "        <td>{$nome}</td>";
                            $tabela .= "        <td>{$e_mail}</td>";
                            $tabela .= "        <td>{$cliente_nome}</td>";
                            $tabela .= "        <td>" . formatarTexto('##.###.###/####-##', $cliente_cnpj) . "</td>";
                            $tabela .= "        <td style='text-align: left;'>{$ultimo_acdesso}</td>";
                            $tabela .= "        <td style='text-align: center;'>{$icon_pw}</td>";
                            $tabela .= "        <td style='text-align: center;'>{$icon_ed}&nbsp;{$icon_ex}{$input}</td>";
                            $tabela .= "    </tr>";
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'carregar_unidade_gestora_permissao' : {
                    try {
                        $id_cliente = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente')));
                        $id_usuario = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_usuario')));
                        
                        $tabela  = 
                          "<table id='datatable-responsive-ugt' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>"
                        . "    <thead>"
                        . "        <tr class='custom-font-size-12'>"
                        . "            <th>#</th>"
                        . "            <th>Unidade Gestora</th>"
                        //. "            <th data-orderable='false' style='text-align: center;'><i class='glyph-icon icon-check-square-o'></i></th>"
                        . "            <th data-orderable='false' style='text-align: center;'>Acesso</th>"
                        . "        </tr>"
                        . "    </thead>"
                        . "    <tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql = 
                              "Select           "
                            . "    u.id         "
                            . "  , u.descricao  "
                            . "  , coalesce(p.acesso, 0) as acesso "
                            . "  , coalesce(p.lancar_eventos, 0) as lancar_eventos "
                            . "  , coalesce(p.lancar_ch_professores, 0) as lancar_ch_professores "
                            . "from REMUN_UNID_GESTORA u "
                            . "  left join ADM_USUARIO_UNID_GESTORA p on (p.id_cliente = u.id_cliente and p.id_unid_gestora = u.id and p.id_usuario = {$id_usuario}) "
                            . "where (u.id_cliente = {$id_cliente}) "
                            . "order by        "
                            . "    u.descricao ";
                                
                        $qtde = 0;
                        $res  = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = "ugt_" . $id_cliente . "_" . $obj->id;
                            $id = str_pad($obj->id, 3, "0", STR_PAD_LEFT);
                            
                            //$acesso = "<input class='custom-checkbox' type='checkbox' name='acesso_{$referencia}' id='acesso_{$referencia}' value='1'>";
                            $acesso = ((int)$obj->acesso === 1?"<i class='glyph-icon icon-check-square-o' id='img_acesso_{$referencia}'></i>":"<i class='glyph-icon icon-square-o' id='img_acesso_{$referencia}'></i>");
                            $input  = 
                                  "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
                                . "<input type='hidden' id='id_unid_gestora_{$referencia}' value='{$id}'>"
                                . "<input type='hidden' id='acesso_{$referencia}' value='{$obj->acesso}'>";
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= 
                                  "    <tr class='custom-font-size-10' id='linha_{$referencia}'>"
                                . "        <td>{$id}</td>"
                                . "        <td>{$obj->descricao}</td>"
                                . "        <td style='text-align: center;'><a href='javascript:preventDefault();' id='marcar_acesso_{$referencia}' onclick='des_marcar_acesso(this.id)'>{$acesso}</a>{$input}</td>"
                                . "    </tr>";
                                
                            $qtde += 1;
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table><input type='hidden' id='qtde_ugt' value='{$qtde}'>";
                        
                        unset($res);
                        unset($pdo);
                        
                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'carregar_unidade_orcament_permissao' : {
                    try {
                        $id_cliente = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente')));
                        $id_usuario = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_usuario')));
                        
                        $tabela  = 
                          "<table id='datatable-responsive-uoc' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>"
                        . "    <thead>"
                        . "        <tr class='custom-font-size-12'>"
                        . "            <th>#</th>"
                        . "            <th>Unidade Orçamentária</th>"
                        . "            <th data-orderable='false' style='text-align: center;'>Acesso</th>"
                        . "        </tr>"
                        . "    </thead>"
                        . "    <tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql = 
                              "Select           "
                            . "    u.id         "
                            . "  , u.descricao  "
                            . "  , coalesce(p.acesso, 0) as acesso "
                            . "  , coalesce(p.lancar_eventos, 0) as lancar_eventos "
                            . "from REMUN_UNID_ORCAMENT u "
                            . "  left join ADM_USUARIO_UNID_ORCAMENT p on (p.id_cliente = u.id_cliente and p.id_unid_orcament = u.id and p.id_usuario = {$id_usuario}) "
                            . "where (u.id_cliente = {$id_cliente}) "
                            . "order by        "
                            . "    u.descricao ";
                                
                        $qtde = 0;
                        $res  = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = "uoc_" . $id_cliente . "_" . $obj->id;
                            $id = str_pad($obj->id, 3, "0", STR_PAD_LEFT);
                            
                            //$acesso = "<input class='custom-checkbox' type='checkbox' name='acesso_{$referencia}' id='acesso_{$referencia}' value='1'>";
                            $acesso = ((int)$obj->acesso === 1?"<i class='glyph-icon icon-check-square-o' id='img_acesso_{$referencia}'></i>":"<i class='glyph-icon icon-square-o' id='img_acesso_{$referencia}'></i>");
                            $input  = 
                                  "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
                                . "<input type='hidden' id='id_unid_orcament_{$referencia}' value='{$id}'>"
                                . "<input type='hidden' id='acesso_{$referencia}' value='{$obj->acesso}'>";
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= 
                                  "    <tr class='custom-font-size-10' id='linha_{$referencia}'>"
                                . "        <td>{$id}</td>"
                                . "        <td>{$obj->descricao}</td>"
                                . "        <td style='text-align: center;'><a href='javascript:preventDefault();' id='marcar_acesso_{$referencia}' onclick='des_marcar_acesso(this.id)'>{$acesso}</a>{$input}</td>"
                                . "    </tr>";
                                
                            $qtde += 1;
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table><input type='hidden' id='qtde_ugt' value='{$qtde}'>";
                        
                        unset($res);
                        unset($pdo);
                        
                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'carregar_unidade_lotacao_permissao' : {
                    try {
                        $id_cliente = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente')));
                        $id_usuario = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_usuario')));
                        
                        $tabela  = 
                          "<table id='datatable-responsive-ulo' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>"
                        . "    <thead>"
                        . "        <tr class='custom-font-size-12'>"
                        . "            <th>#</th>"
                        . "            <th>Unidade de Lotação</th>"
                        //. "            <th data-orderable='false' style='text-align: center;'><i class='glyph-icon icon-check-square-o'></i></th>"
                        . "            <th data-orderable='false' style='text-align: center;'>Acesso</th>"
                        . "        </tr>"
                        . "    </thead>"
                        . "    <tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql = 
                              "Select           "
                            . "    u.id_lotacao "
                            . "  , u.descricao  "
                            . "  , coalesce(p.acesso, 0) as acesso "
                            . "  , coalesce(p.lancar_eventos, 0) as lancar_eventos "
                            . "  , coalesce(p.lancar_ch_professores, 0) as lancar_ch_professores "
                            . "from REMUN_UNID_LOTACAO u "
                            . "  left join ADM_USUARIO_UNID_LOTACAO p on (p.id_cliente = u.id_cliente and p.id_unid_lotacao = u.id_lotacao and p.id_usuario = {$id_usuario}) "
                            . "where (u.id_cliente = {$id_cliente}) "
                            . "order by        "
                            . "    u.descricao ";
                                
                        $qtde = 0;
                        $res  = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = "ulo_" . $id_cliente . "_" . $obj->id_lotacao;
                            $id = str_pad($obj->id_lotacao, 3, "0", STR_PAD_LEFT);
                            
                            //$acesso = "<input class='custom-checkbox' type='checkbox' name='acesso_{$referencia}' id='acesso_{$referencia}' value='1'>";
                            $acesso = ((int)$obj->acesso === 1?"<i class='glyph-icon icon-check-square-o' id='img_acesso_{$referencia}'></i>":"<i class='glyph-icon icon-square-o' id='img_acesso_{$referencia}'></i>");
                            $input  = 
                                  "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
                                . "<input type='hidden' id='id_unid_lotacao_{$referencia}' value='{$id}'>"
                                . "<input type='hidden' id='acesso_{$referencia}' value='{$obj->acesso}'>";
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= 
                                  "    <tr class='custom-font-size-10' id='linha_{$referencia}'>"
                                . "        <td>{$id}</td>"
                                . "        <td>{$obj->descricao}</td>"
                                . "        <td style='text-align: center;'><a href='javascript:preventDefault();' id='marcar_acesso_{$referencia}' onclick='des_marcar_acesso(this.id)'>{$acesso}</a>{$input}</td>"
                                . "    </tr>";
                                
                            $qtde += 1;
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table><input type='hidden' id='qtde_ulo' value='{$qtde}'>";
                        
                        unset($res);
                        unset($pdo);
                        
                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'carregar_usuario' : {
                    try {
                        
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'gravar_permissao_ugt' : {
                    try {
                        $id_cliente = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente')));
                        $id_unidade = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unidade')));
                        $id_usuario = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_usuario')));
                        $acesso     = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'acesso')));
                        $lancar     = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lancar')));
                        $lancar_chp = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lancar_chp')));

                        if ($id_cliente === 0) {
                            echo "<strong>Cliente não definido!</strong>";
                            exit();
                        } else
                        if ($id_usuario === 0) {
                            echo "<strong>Usuário não definido!</strong>";
                            exit();
                        } else
                        if ($id_unidade === 0) {
                            echo "<strong>Unidade não definida!</strong>";
                            exit();
                        } 
                        
                        if ($acesso === 0) {
                            $lancar = 0;
                        }
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = 
                              "Select "
                            . "     g.acesso         "
                            . "   , g.lancar_eventos "
                            . "   , g.lancar_ch_professores "
                            . "from ADM_USUARIO_UNID_GESTORA g      "
                            . "where (g.id_cliente = {$id_cliente}) "
                            . "  and (g.id_usuario = {$id_usuario}) "
                            . "  and (g.id_unid_gestora = {$id_unidade})";

                        $res  = $pdo->query($sql);
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) === false) {
                            $sql = 
                                  "Insert Into ADM_USUARIO_UNID_GESTORA ("
                                . "    id_cliente      "
                                . "  , id_usuario      "
                                . "  , id_unid_gestora "
                                . "  , acesso          "
                                . "  , lancar_eventos  "
                                . "  , lancar_ch_professores "
                                . ") values ("
                                . "    :id_cliente      "
                                . "  , :id_usuario      "
                                . "  , :id_unidade      "
                                . "  , :acesso          "
                                . "  , :lancar_eventos  "
                                . "  , :lancar_ch_professores "
                                . ")";
                        } else {
                            $sql = 
                                  "Update ADM_USUARIO_UNID_GESTORA Set  "
                                . "    acesso         = :acesso         "
                                . "  , lancar_eventos = :lancar_eventos "
                                . "  , lancar_ch_professores = :lancar_ch_professores "
                                . "where (id_cliente = :id_cliente) "
                                . "  and (id_usuario = :id_usuario) "
                                . "  and (id_unid_gestora = :id_unidade) ";
                        }
                            
                        $stm = $pdo->prepare($sql);
                        $stm->execute(array(
                            ':id_cliente'     => $id_cliente
                          , ':id_usuario'     => $id_usuario
                          , ':id_unidade'     => $id_unidade
                          , ':acesso'         => $acesso
                          , ':lancar_eventos' => $lancar
                          , ':lancar_ch_professores' => $lancar_chp
                        ));
                        $pdo->commit();

                        unset($res);
                        unset($stm);
                        unset($pdo);
                        
                        echo "OK";
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'gravar_permissao_uoc' : {
                    try {
                        $id_cliente = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente')));
                        $id_unidade = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unidade')));
                        $id_usuario = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_usuario')));
                        $acesso     = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'acesso')));
                        $lancar     = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lancar')));

                        if ($id_cliente === 0) {
                            echo "<strong>Cliente não definido!</strong>";
                            exit();
                        } else
                        if ($id_usuario === 0) {
                            echo "<strong>Usuário não definido!</strong>";
                            exit();
                        } else
                        if ($id_unidade === 0) {
                            echo "<strong>Unidade não definida!</strong>";
                            exit();
                        } 
                        
                        if ($acesso === 0) {
                            $lancar = 0;
                        }
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = 
                              "Select "
                            . "     g.acesso         "
                            . "   , g.lancar_eventos "
                            . "from ADM_USUARIO_UNID_ORCAMENT g      "
                            . "where (g.id_cliente = {$id_cliente}) "
                            . "  and (g.id_usuario = {$id_usuario}) "
                            . "  and (g.id_unid_orcament = {$id_unidade})";

                        $res  = $pdo->query($sql);
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) === false) {
                            $sql = 
                                  "Insert Into ADM_USUARIO_UNID_ORCAMENT ("
                                . "    id_cliente      "
                                . "  , id_usuario      "
                                . "  , id_unid_orcament"
                                . "  , acesso          "
                                . "  , lancar_eventos  "
                                . ") values ("
                                . "    :id_cliente      "
                                . "  , :id_usuario      "
                                . "  , :id_unidade      "
                                . "  , :acesso          "
                                . "  , :lancar_eventos  "
                                . ")";
                        } else {
                            $sql = 
                                  "Update ADM_USUARIO_UNID_ORCAMENT Set  "
                                . "    acesso         = :acesso          "
                                . "  , lancar_eventos = :lancar_eventos  "
                                . "where (id_cliente = :id_cliente) "
                                . "  and (id_usuario = :id_usuario) "
                                . "  and (id_unid_gestora = :id_unidade) ";
                        }
                            
                        $stm = $pdo->prepare($sql);
                        $stm->execute(array(
                            ':id_cliente'     => $id_cliente
                          , ':id_usuario'     => $id_usuario
                          , ':id_unidade'     => $id_unidade
                          , ':acesso'         => $acesso
                          , ':lancar_eventos' => $lancar
                        ));
                        $pdo->commit();

                        unset($res);
                        unset($stm);
                        unset($pdo);
                        
                        echo "OK";
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'gravar_permissao_ulo' : {
                    try {
                        $id_cliente = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente')));
                        $id_unidade = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unidade')));
                        $id_usuario = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_usuario')));
                        $acesso     = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'acesso')));
                        $lancar     = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lancar')));
                        $lancar_chp = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lancar_chp')));

                        if ($id_cliente === 0) {
                            echo "<strong>Cliente não definido!</strong>";
                            exit();
                        } else
                        if ($id_usuario === 0) {
                            echo "<strong>Usuário não definido!</strong>";
                            exit();
                        } else
                        if ($id_unidade === 0) {
                            echo "<strong>Unidade não definida!</strong>";
                            exit();
                        } 
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = 
                              "Select "
                            . "     g.acesso         "
                            . "   , g.lancar_eventos "
                            . "   , g.lancar_ch_professores "
                            . "from ADM_USUARIO_UNID_LOTACAO g      "
                            . "where (g.id_cliente = {$id_cliente}) "
                            . "  and (g.id_usuario = {$id_usuario}) "
                            . "  and (g.id_unid_lotacao = {$id_unidade})";

                        $res  = $pdo->query($sql);
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) === false) {
                            $sql = 
                                  "Insert Into ADM_USUARIO_UNID_LOTACAO ("
                                . "    id_cliente      "
                                . "  , id_usuario      "
                                . "  , id_unid_lotacao "
                                . "  , acesso          "
                                . "  , lancar_eventos  "
                                . "  , lancar_ch_professores "
                                . ") values ("
                                . "    :id_cliente      "
                                . "  , :id_usuario      "
                                . "  , :id_unidade      "
                                . "  , :acesso          "
                                . "  , :lancar_eventos  "
                                . "  , :lancar_ch_professores "
                                . ")";
                        } else {
                            $sql = 
                                  "Update ADM_USUARIO_UNID_LOTACAO Set  "
                                . "    acesso         = :acesso         "
                                . "  , lancar_eventos = :lancar_eventos "
                                . "  , lancar_ch_professores = :lancar_ch_professores "
                                . "where (id_cliente = :id_cliente) "
                                . "  and (id_usuario = :id_usuario) "
                                . "  and (id_unid_lotacao = :id_unidade) ";
                        }
                            
                        $stm = $pdo->prepare($sql);
                        $stm->execute(array(
                            ':id_cliente'     => $id_cliente
                          , ':id_usuario'     => $id_usuario
                          , ':id_unidade'     => $id_unidade
                          , ':acesso'         => $acesso
                          , ':lancar_eventos' => $lancar
                          , ':lancar_ch_professores' => $lancar_chp
                        ));
                        $pdo->commit();

                        unset($res);
                        unset($stm);
                        unset($pdo);
                        
                        echo "OK";
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'gravar_usuario' : {
                    try {
                        $op = trim(filter_input(INPUT_POST, 'op'));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        $id_cliente = trim(filter_input(INPUT_POST, 'id_cliente'));
                        $nome  = trim(filter_input(INPUT_POST, 'nome'));
                        $email = trim(filter_input(INPUT_POST, 'email'));
                        $senha = trim(filter_input(INPUT_POST, 'senha'));
                        $senha_confirme = trim(filter_input(INPUT_POST, 'senha_confirme'));
                        $situacao = trim(filter_input(INPUT_POST, 'situacao'));
                        $administrar_portal = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'administrar_portal')) );
                        $lancar_eventos = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lancar_eventos')) );
                        $lancar_ch_professores = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lancar_ch_professores')) );
                        
                        $file = '../downloads/user_' . $hs . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else 
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            echo "E-mail inválido!";
                        } else {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            if ($op === "inserir_usuario") {
                                $dao = Dao::getInstancia();
                                $id = $dao->getCountID("ADM_USUARIO", "ID");
                                $stm = $pdo->prepare(
                                      "Insert Into ADM_USUARIO ("
                                    . "    id         "
                                    . "  , id_cliente "
                                    . "  , nome       "
                                    . "  , e_mail     "
                                    . "  , senha      "
                                    . "  , exe_ano    "
                                    . "  , situacao   "
                                    . "  , administrar_portal "
                                    . "  , lancar_eventos     "
                                    . "  , lancar_ch_professores "
                                    . ") values ("
                                    . "    :id         "
                                    . "  , :id_cliente "
                                    . "  , :nome       "
                                    . "  , :email      "
                                    . "  , :senha      "
                                    . "  , extract(year from current_date) "
                                    . "  , :situacao   "
                                    . "  , :administrar_portal "
                                    . "  , :lancar_eventos     "
                                    . "  , :lancar_ch_professores "
                                    . ")");
                                $stm->execute(array(
                                    ':id'         => $id,
                                    ':id_cliente' => $id_cliente,
                                    ':nome'       => $nome,
                                    ':email'      => $email,
                                    ':senha'      => hashSenhaUser($senha),
                                    ':situacao'   => $situacao,
                                    ':administrar_portal' => $administrar_portal,
                                    ':lancar_eventos'     => $lancar_eventos,
                                    ':lancar_ch_professores' => $lancar_ch_professores
                                ));
                            } else
                            if ($op === "editar_usuario") {
                                $stm = $pdo->prepare(
                                      "Update ADM_USUARIO u Set "
                                    . "    u.nome       = :nome "
                                    . "  , u.e_mail     = :email "
                                    . "  , u.situacao   = :situacao "
                                    . "  , u.administrar_portal = :administrar_portal "
                                    . "  , u.lancar_eventos     = :lancar_eventos "
                                    . "  , u.lancar_ch_professores = :lancar_ch_professores "
                                    . "  , u.id_cliente = :id_cliente "
                                    . "where u.id = :id   ");
                                $stm->execute(array(
                                    ':nome'       => $nome,
                                    ':email'      => $email,
                                    ':situacao'   => $situacao,
                                    ':administrar_portal' => $administrar_portal,
                                    ':lancar_eventos'     => $lancar_eventos,
                                    ':lancar_ch_professores' => $lancar_ch_professores,
                                    ':id_cliente' => $id_cliente,
                                    ':id'         => $id
                                ));
                                
                                if (($senha !== "") && ($senha === $senha_confirme)) {
                                    $stm = $pdo->prepare(
                                          "Update ADM_USUARIO u Set "
                                        . "    u.senha = :senha "
                                        . "where u.id = :id   ");
                                    $stm->execute(array(
                                        ':senha' => hashSenhaUser($senha),
                                        ':id'    => $id
                                    ));
                                }
                            }
                            
                            $pdo->commit();
                            
                            $registros = array('form' => array());

                            $registros['form'][0]['id']    = str_pad(intval($id), 3, "0", STR_PAD_LEFT);
                            $registros['form'][0]['nome']  = $nome;
                            $registros['form'][0]['email'] = $email;

                            $json = json_encode($registros);
                            file_put_contents($file, $json);
                            
                            echo "OK";
                        }
                        
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'excluir_usuario' : {
                    try {
                        $id = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id')));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else 
                        if (intval($user_id) === intval($id)) {
                            echo "Auto exclusão não permitida!";
                        } else {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $stm = $pdo->prepare(
                                  "Delete from ADM_USUARIO "
                                . "where id = :id   ");
                            $stm->execute(array(
                                ':id'    => $id
                            ));
                            
                            $pdo->commit();
                            
                            echo "OK";
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
            }
        } else {
            echo "Erro ao tentar identificar ação requistada!";
        }
    }
