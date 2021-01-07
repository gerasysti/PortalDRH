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
                case 'consultar_unidade' : {
                    try {
                        $id = strip_tags( trim(filter_input(INPUT_POST, 'id')) );
                        $us = strip_tags( trim(filter_input(INPUT_POST, 'us')) );
                        $to = strip_tags( trim(filter_input(INPUT_POST, 'to')) );
                        $ps = strip_tags( trim(filter_input(INPUT_POST, 'ps')) );
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th>ID</th>";
                        $tabela .= "            <th>Descrição</th>";
                        $tabela .= "            <th>Tipo</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'>Ativa</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;' class='numeric'></th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        $sql = 
                              "Select           "
                            . "    u.id_cliente "
                            . "  , u.id_lotacao "
                            . "  , u.descricao  "
                            . "  , u.tipo       "
                            . "  , Case u.tipo  "
                            . "      when  1 then 'SEDE DO ÓRGÃO' "
                            . "      when  2 then 'SECRETARIA MUNICIPAL' "
                            . "      when  3 then 'HOSPITAL / POSTO DE SAÚDE' "
                            . "      when  4 then 'ESCOLA MUNICIPAL' "
                            . "      when 99 then 'OUTROS' "
                            . "      else 'IND'     "
                            . "    end as ds_tipo   "
                            . "  , u.ativa          "
                            . "from REMUN_UNID_LOTACAO u    "
                            . "where (u.id_cliente = {$to}) "
                            . "  and (upper(u.descricao) like upper('%{$ps}%')) "
                            . "order by              "
                            . "    u.descricao       ";
                        
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = $obj->id_lotacao; //$obj->id_cliente . "_" . $obj->id;
                            $id_cliente = $obj->id_cliente;
                            $id_lotacao = str_pad($obj->id_lotacao, 4, "0", STR_PAD_LEFT);
                            $descricao  = (!empty($obj->descricao)?$obj->descricao:"&nbsp;");
                            $tipo       = (!empty($obj->tipo)?$obj->tipo:"0");
                            $ds_tipo    = (!empty($obj->ds_tipo)?$obj->ds_tipo:"...");
                            $ativa      = (!empty($obj->ativa)?trim($obj->ativa):"S");
                            
                            $style = "padding-left: 1px; padding-right: 1px; padding-top: 1px; padding-bottom: 1px; ";
                            $input = 
                                  "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
                                . "<input type='hidden' id='id_lotacao_{$referencia}' value='{$id_lotacao}'>"
                                . "<input type='hidden' id='descricao_{$referencia}'  value='{$descricao}'>"
                                . "<input type='hidden' id='tipo_{$referencia}'       value='{$tipo}'>"
                                . "<input type='hidden' id='ativa_{$referencia}'      value='{$ativa}'>";
                            
                            $icon_ed = "<button id='editar_unidade_{$referencia}'  class='btn btn-round btn-primary' title='Visualizar Registro'  onclick='editarUnidadeLotacao(this.id)'  style='{$style}'><i class='glyph-icon icon-edit'></i></button>";
                            $icon_ex = ""; //"<button id='excluir_unidade_{$referencia}' class='btn btn-round btn-primary' title='Excluir Registro' onclick='excluirUnidadeLotacao(this.id)' style='{$style}'><i class='glyph-icon icon-trash'></i></button>";
                            $icon_st = ""; 
                            
                            if ($ativa === 'S') {
                                $icon_st = "<i class='glyph-icon icon-check-square-o'></i>";
                            } else {
                                $icon_st = "<i class='glyph-icon icon-square-o'></i>";
                            }
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr class='custom-font-size-10' id='linha_{$referencia}'>";
                            $tabela .= "        <td>{$id_lotacao}</td>";
                            $tabela .= "        <td>{$descricao}</td>";
                            $tabela .= "        <td>{$ds_tipo}</td>";
                            $tabela .= "        <td style='text-align: center;'>{$icon_st}</td>";
                            $tabela .= "        <td style='text-align: center;' style='{$style}'>{$icon_ed}&nbsp;{$icon_ex}{$input}</td>";
                            $tabela .= "    </tr>";
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'carregar_unidade' : {
                    try {
                        
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'gravar_unidade' : {
                    try {
//                        $op = trim(filter_input(INPUT_POST, 'op'));
//                        $hs = trim(filter_input(INPUT_POST, 'hs'));
//                        $id = trim(filter_input(INPUT_POST, 'id'));
//                        $id_cliente = trim(filter_input(INPUT_POST, 'id_cliente'));
//                        $nome  = trim(filter_input(INPUT_POST, 'nome'));
//                        $email = trim(filter_input(INPUT_POST, 'email'));
//                        $senha = trim(filter_input(INPUT_POST, 'senha'));
//                        $senha_confirme = trim(filter_input(INPUT_POST, 'senha_confirme'));
//                        $situacao = trim(filter_input(INPUT_POST, 'situacao'));
//                        $administrar_portal = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'administrar_portal')) );
//                        $lancar_eventos = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lancar_eventos')) );
//                        
//                        $file = '../downloads/user_' . $hs . '.json';
//                        if (file_exists($file)) {
//                            unlink($file);
//                        }
//                        
//                        if ($hs !== $hash) {
//                            echo "Acesso Inválido";
//                        } else 
//                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//                            echo "E-mail inválido!";
//                        } else {
//                            $cnf = Configuracao::getInstancia();
//                            $pdo = $cnf->db('', '');
//                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//                            
//                            if ($op === "inserir_usuario") {
//                                $dao = Dao::getInstancia();
//                                $id = $dao->getCountID("ADM_USUARIO", "ID");
//                                $stm = $pdo->prepare(
//                                      "Insert Into ADM_USUARIO ("
//                                    . "    id         "
//                                    . "  , id_cliente "
//                                    . "  , nome       "
//                                    . "  , e_mail     "
//                                    . "  , senha      "
//                                    . "  , exe_ano    "
//                                    . "  , situacao   "
//                                    . "  , administrar_portal "
//                                    . "  , lancar_eventos     "
//                                    . ") values ("
//                                    . "    :id         "
//                                    . "  , :id_cliente "
//                                    . "  , :nome       "
//                                    . "  , :email      "
//                                    . "  , :senha      "
//                                    . "  , extract(year from current_date) "
//                                    . "  , :situacao   "
//                                    . "  , :administrar_portal "
//                                    . "  , :lancar_eventos     "
//                                    . ")");
//                                $stm->execute(array(
//                                    ':id'         => $id,
//                                    ':id_cliente' => $id_cliente,
//                                    ':nome'       => $nome,
//                                    ':email'      => $email,
//                                    ':senha'      => hashSenhaUser($senha),
//                                    ':situacao'   => $situacao,
//                                    ':administrar_portal' => $administrar_portal,
//                                    ':lancar_eventos'     => $lancar_eventos
//                                ));
//                            } else
//                            if ($op === "editar_usuario") {
//                                $stm = $pdo->prepare(
//                                      "Update ADM_USUARIO u Set "
//                                    . "    u.nome       = :nome "
//                                    . "  , u.e_mail     = :email "
//                                    . "  , u.situacao   = :situacao "
//                                    . "  , u.administrar_portal = :administrar_portal "
//                                    . "  , u.lancar_eventos     = :lancar_eventos "
//                                    . "  , u.id_cliente = :id_cliente "
//                                    . "where u.id = :id   ");
//                                $stm->execute(array(
//                                    ':nome'       => $nome,
//                                    ':email'      => $email,
//                                    ':situacao'   => $situacao,
//                                    ':administrar_portal' => $administrar_portal,
//                                    ':lancar_eventos'     => $lancar_eventos,
//                                    ':id_cliente' => $id_cliente,
//                                    ':id'         => $id
//                                ));
//                                
//                                if (($senha !== "") && ($senha === $senha_confirme)) {
//                                    $stm = $pdo->prepare(
//                                          "Update ADM_USUARIO u Set "
//                                        . "    u.senha = :senha "
//                                        . "where u.id = :id   ");
//                                    $stm->execute(array(
//                                        ':senha' => hashSenhaUser($senha),
//                                        ':id'    => $id
//                                    ));
//                                }
//                            }
//                            
//                            $pdo->commit();
//                            
//                            $registros = array('form' => array());
//
//                            $registros['form'][0]['id']    = str_pad(intval($id), 3, "0", STR_PAD_LEFT);
//                            $registros['form'][0]['nome']  = $nome;
//                            $registros['form'][0]['email'] = $email;
//
//                            $json = json_encode($registros);
//                            file_put_contents($file, $json);
//                            
//                            echo "OK";
//                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'excluir_unidade' : {
                    try {
                        $to = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'to'))) );
                        $id = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id'))) );
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        
                        echo "<strong>Usuário não habilitado para esta tarefa</strong>.<br>Entre em contato com a direção.";
                        exit;
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $stm = $pdo->prepare(
                                  "Delete from REMUN_UNID_LOTACAO  "
                                . "where (id_cliente = :id_cliente)"
                                . "  and (id_unidade = :id_unidade) ");
                            $stm->execute(array(
                                  ':id_cliente' => $to
                                , ':id_unidade' => $id
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
