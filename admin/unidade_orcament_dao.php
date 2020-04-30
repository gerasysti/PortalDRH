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
                case 'listar_unidades_orcamentarias' : {
                    try {
                        $op = trim(filter_input(INPUT_POST, 'op'));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = $cliente; //trim(filter_input(INPUT_POST, 'id'));
                        $us = $user_id; //(int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'us')) );
                        $ug = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'ug')) );
                        
                        $file = '../downloads/uoc_' . $hs . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            // Carregar Unidades Orçamentárias
                            $sql = 
                                 "Select   "
                               . "    o.id "
                               . "  , o.descricao "
                               . "from REMUN_UNID_ORCAMENT o "
                               . "  inner join ( "
                               . "    Select "
                               . "        x.id_cliente "
                               . "      , x.id_unid_gestora "
                               . "    from ADM_USUARIO_UNID_GESTORA x "
                               . "    where x.id_cliente = {$id} "
                               . "      and x.id_usuario = {$us} "
                               . "      and x.acesso     = 1 "
                               . "  ) a on (a.id_cliente = o.id_cliente and a.id_unid_gestora = o.id_unid_gestora) "
                               . "  inner join ADM_USUARIO_UNID_ORCAMENT g on (g.id_cliente = o.id_cliente and g.id_unid_orcament = o.id and g.id_usuario = {$us} and g.acesso = 1) "
                               . "where (o.id_cliente = {$id}) "
                               . ($ug !== 0?"  and (o.id_unid_gestora = {$ug}) ":"")
                               . "order by "
                               . "    o.id_unid_gestora "
                               . "  , o.descricao ";
                            
                            $registros = array('lista' => array());
                            
                            $i = 0;   
                            $qry = $pdo->query($sql);
                            while (($obj = $qry->fetch(PDO::FETCH_OBJ)) !== false) {
                                $registros['lista'][$i]['id'] = $obj->id;
                                $registros['lista'][$i]['descricao']  = $obj->descricao;
                                
                                $i += 1;
                            }

                            // Fechar conexão PDO
                            unset($qry);
                            unset($pdo);
                            
                            $json = json_encode($registros);
                            file_put_contents($file, $json);
                            
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
