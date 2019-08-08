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
                case 'consultar_remuneracao_cargo' : {
                    try {
                        $id_cliente = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'un')));
                        $nr_ano = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_ano')));
                        $nr_mes = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_mes')));
                        $nr_par = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_par')));
                        $id_vin = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'id_vin')));
                        
                        // Gerar arquivo HTML e TXT
                        $nm_arquivo_htm = "Renumeracao_Cargo_" . $nr_ano . $nr_mes . "_" . md5($id_cliente) . ".html";
                        $nm_arquivo_txt = "Renumeracao_Cargo_" . $nr_ano . $nr_mes . "_" . md5($id_cliente) . ".txt";
                        $file_txt = '../downloads/' . $nm_arquivo_txt;
                        if (file_exists($file_txt)) {
                            unlink($file_txt);
                        }
                        $file_htm = '../downloads/' . $nm_arquivo_htm;
                        if (file_exists($file_htm)) {
                            unlink($file_htm);
                        }
                        $fp_txt = fopen('../downloads/' . $nm_arquivo_txt, "a");
                        $fp_htm = fopen('../downloads/' . $nm_arquivo_htm, "a");
                        
                        // Gerar cabeçalho de campos no HTML e no TXT
                        $es = fwrite($fp_txt, "CARGO/FUNÇÃO|SERVIDORES|VENCIMENTO BASE|REMUNERAÇÕES|DESCONTOS|LÍQUIDOS" . "\r\n");
                        $es = fwrite($fp_htm, 
                              "<table id='tb_remunecacao' cellspacing='0' width='100%'>"
                            . " <thead>"
                            . "     <tr>"
                            . "         <th class='titulo esquerda'>Cargo / Função</th>"
                            . "         <th class='titulo direita' style='text-align: right;'>Servidores</th>"
                            . "         <th class='titulo direita' style='text-align: right;'>Venc.Base (R$)</th>"
                            . "         <th class='titulo direita' style='text-align: right;'>Remunerações (R$)</th>"
                            . "         <th class='titulo direita' style='text-align: right;'>Descontos (R$)</th>"
                            . "         <th class='titulo direita' style='text-align: right;'>Líquidos (R$)</th>"
                            . "     </tr>"
                            . " </thead>"
                            . " <tbody>" . "\r\n");
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr>";
                        $tabela .= "            <th>Cargo / Função</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Servidores</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Venc.Base</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Remunerações</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Descontos</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Líquidos</th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        $sql = 
                             "Select "
                            ."    r.R_CARGO_FUNCAO  as cargo "
                            ."  , count(r.R_MATRIC) as servidores "
                            ."  , sum(r.R_VENCTO_BASE)   as vencimento_base "
                            ."  , sum(r.R_TOT_VENCTOS)   as total_vencimento "
                            ."  , sum(r.R_TOT_DESCONTOS) as total_descontos "
                            ."  , sum(r.R_SAL_LIQUIDO)   as total_liquido "
                            ."from SP_FOLHA_TRANSPARENCIA({$id_cliente}, '{$nr_ano}', '{$nr_mes}', '{$nr_par}', {$id_vin}) r "
                            ."group by "
                            ."    r.R_CARGO_FUNCAO "
                            ."order by "
                            ."    r.R_CARGO_FUNCAO ";

                        $total_servidores = 0;
                        $total_bases      = 0.0;
                        $total_vencimento = 0.0;
                        $total_descontos  = 0.0;
                        $total_salarios   = 0.0;
                        
                        $par = 0;    
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $class = (($par%2) === 0?" class='dif'":"");
                            
                            $servidores    = number_format($obj->servidores, 0, ',' , '.');
                            $vencimento    = number_format($obj->vencimento_base,  2, ',' , '.');
                            $total_venc    = number_format($obj->total_vencimento, 2, ',' , '.');
                            $total_desc    = number_format($obj->total_descontos,  2, ',' , '.');
                            $total_liquido = number_format($obj->total_liquido,    2, ',' , '.');

                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr>";
                            $tabela .= "        <td>{$obj->cargo}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$servidores}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$vencimento}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$total_venc}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$total_desc}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$total_liquido}</td>";
                            $tabela .= "    </tr>";
                            
                            $ln  = $obj->cargo . "|";
                            $ln .= $servidores . "|";
                            $ln .= $vencimento . "|";
                            $ln .= $total_venc . "|";
                            $ln .= $total_desc . "|";
                            $ln .= $total_liquido;

                            // Gerar linha de registro nos arquivos HTML e TXT
                            $es = fwrite($fp_txt, $ln . "\r\n");
                            $es = fwrite($fp_htm, 
                                  "     <tr{$class}>"
                                . "         <td>{$obj->cargo}</td>"
                                . "         <td style='text-align: right;'>{$servidores}</td>"
                                . "         <td style='text-align: right;'>{$vencimento}</td>"
                                . "         <td style='text-align: right;'>{$total_venc}</td>"
                                . "         <td style='text-align: right;'>{$total_desc}</td>"
                                . "         <td style='text-align: right;'>{$total_liquido}</td>"
                                . "     </tr>" . "\r\n");
                            
                            $par += 1;
                            
                            $total_servidores += $servidores;
                            $total_bases      += $obj->vencimento_base;
                            $total_vencimento += $obj->total_vencimento;
                            $total_descontos  += $obj->total_descontos;   
                            $total_salarios   += $obj->total_liquido;
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                        
                        // Gerar linha total dos registros no arquivo HTML
                        $es = fwrite($fp_htm, 
                              " <tbody>"
                            . " <tfoot>"
                            . "     <tr>"
                            . "         <th class='rodape'>Registros : {$par}</th>"
                            . "         <th class='rodape'>{$total_servidores}</th>"
                            . "         <th class='rodape'>" . number_format($total_bases, 2, ',' , '.') . "</th>"
                            . "         <th class='rodape'>" . number_format($total_vencimento, 2, ',' , '.') . "</th>"
                            . "         <th class='rodape'>" . number_format($total_descontos, 2, ',' , '.') . "</th>"
                            . "         <th class='rodape'>" . number_format($total_salarios, 2, ',' , '.') . "</th>"
                            . "     </tr>"
                            . " </tfoot>"
                            . "</table>" . "\r\n");
                        
                        fclose($fp_txt);
                        fclose($fp_htm);
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
            }
        } else {
            echo "Erro ao tentar identificar ação requistada!";
        }
    }
