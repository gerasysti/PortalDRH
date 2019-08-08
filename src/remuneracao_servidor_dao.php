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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ac'])) {
            switch ($_POST['ac']) {
                case 'consultar_remuneracao_servidor' : {
                    try {
                        $id_cliente = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'un')));
                        $proces = trim(filter_input(INPUT_POST, 'pr'));
                        $nr_ano = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_ano')));
                        $nr_mes = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_mes')));
                        $nr_par = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_par')));
                        $id_vin = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'id_vin')));
                        
                        // Gerar arquivo TXT
                        $nm_arquivo = "Renumeracao_Servidor_" . $nr_ano . $nr_mes . "_" . md5($id_cliente) . ".txt";
                        $file = '../downloads/' . $nm_arquivo;
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        $fp = fopen('../downloads/' . $nm_arquivo, "a");
                        
                        if ( $proces === 'tabela' ) {
                            $tabela  = "<a id='ancora_datatable-responsive'></a>";
                            $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                            $tabela .= "    <thead>";
                            $tabela .= "        <tr>";
                            $tabela .= "            <th style='text-align: right;'>Matrícula</th>";
                            $tabela .= "            <th>Servidor</th>";
                            $tabela .= "            <th>Cargo/Função</th>";
                            $tabela .= "            <th>Vínculo</th>";
                            $tabela .= "            <th>Admissão</th>";
                            $tabela .= "            <th style='text-align: right;'>Dias</th>";
                            $tabela .= "            <th style='text-align: right;'>Venc.Base</th>";
                            $tabela .= "            <th style='text-align: right;'>Remuneração</th>";
                            $tabela .= "            <th style='text-align: right;'>Descontos</th>";
                            $tabela .= "            <th style='text-align: right;'>Líquido</th>";
                            $tabela .= "            <th>Situação</th>";
                            $tabela .= "        </tr>";
                            $tabela .= "    </thead>";
                            $tabela .= "    <tbody>";
                        } else 
                        if ( $proces === 'arquivo_txt' ) {
                            $es = fwrite($fp, "MATRICULA|SERVIDOR|CARGO/FUNÇÃO|VÍNCULO|ADMISSÃO|DIAS|VENCIMENTO BASE|REMUNERAÇÃO|DESCONTOS|LÍQUIDO|SITUAÇÃO" . "\r\n");
                        }
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        $sql = 
                             "Select "
                            ."    r.R_MATRIC_FTDO   as matricula "
                            ."  , r.R_NOME_SERVIDOR as servidor "
                            ."  , r.R_CARGO_FUNCAO  as cargo "
                            ."  , r.R_DESCR_VINCULO as vinculo "
                            ."  , r.R_DT_AMISSAO    as admissao "
                            ."  , r.R_QTD_DIAS_TRAB as dias_trabalhados "
                            ."  , r.R_VENCTO_BASE   as vencimento_base "
                            ."  , r.R_TOT_VENCTOS   as total_vencimento "
                            ."  , r.R_TOT_DESCONTOS as total_descontos "
                            ."  , r.R_SAL_LIQUIDO   as total_liquido "
                            ."  , r.R_DESCR_EST_FUNCIONAL  as situacao "
                            ."from SP_FOLHA_TRANSPARENCIA({$id_cliente}, '{$nr_ano}', '{$nr_mes}', '{$nr_par}', {$id_vin}) r "
                            ."order by "
                            ."    r.R_NOME_SERVIDOR ";

                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            //$status = ($obj->situacao === "ATIVO"?"<i class='glyph-icon icon-check-square-o'></i>":"<i class='glyph-icon icon-square-o'></i>");
                            $status = ucwords(strtolower($obj->situacao));
                            
                            $admissao      = date('d/m/Y', strtotime($obj->admissao));
                            $vencimento    = number_format($obj->vencimento_base,  2, ',' , '.');
                            $total_venc    = number_format($obj->total_vencimento, 2, ',' , '.');
                            $total_desc    = number_format($obj->total_descontos,  2, ',' , '.');
                            $total_liquido = number_format($obj->total_liquido,    2, ',' , '.');
                            
                            if ( $proces === 'tabela' ) {
                                $tabela .= "    <tr>";
                                $tabela .= "        <td style='text-align: right;'>{$obj->matricula}</tb>";
                                $tabela .= "        <td>{$obj->servidor}</tb>";
                                $tabela .= "        <td>{$obj->cargo}</tb>";
                                $tabela .= "        <td>{$obj->vinculo}</tb>";
                                $tabela .= "        <td style='text-align: center;'>{$admissao}</tb>";
                                $tabela .= "        <td style='text-align: right;'>{$obj->dias_trabalhados}</tb>";
                                $tabela .= "        <td style='text-align: right;'>{$vencimento}</tb>";
                                $tabela .= "        <td style='text-align: right;'>{$total_venc}</tb>";
                                $tabela .= "        <td style='text-align: right;'>{$total_desc}</tb>";
                                $tabela .= "        <td style='text-align: right;'>{$total_liquido}</tb>";
                                $tabela .= "        <td style='text-align: left;'>{$status}</tb>";
                                $tabela .= "    </tr>";
                            } else
                            if ( $proces === 'arquivo_txt' ) {
                                $ln  = $obj->matricula . "|";
                                $ln .= $obj->servidor  . "|";
                                $ln .= $obj->cargo     . "|";
                                $ln .= $obj->vinculo   . "|";
                                $ln .= $admissao       . "|";
                                $ln .= $obj->dias_trabalhados . "|";
                                $ln .= $vencimento    . "|";
                                $ln .= $total_venc    . "|";
                                $ln .= $total_desc    . "|";
                                $ln .= $total_liquido . "|";
                                $ln .= $obj->situacao;

                                $es = fwrite($fp, $ln . "\r\n");
                            }    
                        }
                        
                        fclose($fp);

                        if ( $proces === 'tabela' ) {
                            $tabela .= "    </tbody>";
                            $tabela .= "</table>";
                            
                            echo $tabela;
                        } else
                        if ( $proces === 'arquivo_txt' ) {
                            // $baixar = "<a href = 'http://servicos.hab.org.br/personalManagement/baixar.php?arquivo=./setransbel/{$row['NM_ARQUIVO']}'>";
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
