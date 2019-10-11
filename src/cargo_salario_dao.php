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
                case 'consultar_cargo_salario' : {
                    try {
                        $id_cliente = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'un')));
                        $nr_ano = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_ano')));
                        $nr_mes = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_mes')));
                        
                        // Gerar arquivo HTML e TXT
                        $nm_arquivo_htm = "TCS_" . $nr_ano . $nr_mes . "_" . md5($id_cliente) . ".html";
                        $nm_arquivo_txt = "TCS_" . $nr_ano . $nr_mes . "_" . md5($id_cliente) . ".txt";
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
                        $es = fwrite($fp_txt, 
                                  "CARGO/FUNÇÕES"
                                . "|REF 00"
                                . "|REF 01"
                                . "|REF 02"
                                . "|REF 03"
                                . "|REF 04"
                                . "|REF 05"
                                . "|REF 06"
                                . "|REF 07"
                                . "|REF 08"
                                . "|REF 09"
                                . "|REF 10"
                                . "|REF 11"
                                . "|REF 12"
                                . "|REF 13"
                                . "|REF 14"
                                . "|REF 15" . "\r\n");
                        $es = fwrite($fp_htm, 
                              "<table id='tb_cargo_salario' cellspacing='0' width='100%' border='0'>"
                            . " <thead>"
                            . "     <tr>"
                            . "         <th rowspan='2'  class='titulo centro'>CARGO/FUNÇÃO</th>"
                            . "         <th colspan='16' class='titulo centro'>REFERÊNCIAS</th>"
                            . "     </tr>"
                            . "     <tr>"
                            . "         <th class='titulo centro'>00</th>"
                            . "         <th class='titulo centro'>01</th>"
                            . "         <th class='titulo centro'>02</th>"
                            . "         <th class='titulo centro'>03</th>"
                            . "         <th class='titulo centro'>04</th>"
                            . "         <th class='titulo centro'>05</th>"
                            . "         <th class='titulo centro'>06</th>"
                            . "         <th class='titulo centro'>07</th>"
                            . "         <th class='titulo centro'>08</th>"
                            . "         <th class='titulo centro'>09</th>"
                            . "         <th class='titulo centro'>10</th>"
                            . "         <th class='titulo centro'>11</th>"
                            . "         <th class='titulo centro'>12</th>"
                            . "         <th class='titulo centro'>13</th>"
                            . "         <th class='titulo centro'>14</th>"
                            . "         <th class='titulo centro'>15</th>"
                            . "     </tr>"
                            . " </thead>"
                            . " <tbody>" . "\r\n");
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th rowspan='2'>Cargo/Função</th>";
                        $tabela .= "            <th colspan='16' class='numeric' data-orderable='false' style='text-align: center;'>Referências </th>";
                        $tabela .= "        </tr>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        //$tabela .= "            <th>Vínculo</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>00</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>01</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>02</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>03</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>04</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>05</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>06</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>07</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>08</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>09</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>10</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>11</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>12</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>13</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>14</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>15</th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        $sql = 
                             "Select "
                            ."    c.id_cliente  "
                            ."  , c.id_cargo    "
                            ."  , c.descricao as ds_cargo "
                            ."  , c.tipo_tcm    "
                            ."  , c.qtd_vagas   "
                            ."  , c.vencto_base "
                            ."  , s.ano_mes     "
                            ."  , s.val_ref00   "
                            ."  , s.val_ref01, s.val_ref02, s.val_ref03, s.val_ref04, s.val_ref05 "
                            ."  , s.val_ref06, s.val_ref07, s.val_ref08, s.val_ref09, s.val_ref10 "
                            ."  , s.val_ref11, s.val_ref12, s.val_ref13, s.val_ref14, s.val_ref15 "
                            ."from REMUN_CARGO_FUNCAO c "
                            ."  inner join REMUN_CARGO_REF s on (s.id_cliente = c.id_cliente and s.id_cargo = c.id_cargo and s.ano_mes = '{$nr_ano}{$nr_mes}') "
                            ."where c.id_cliente = {$id_cliente} "
                            ."order by "
                            ."    c.descricao "; 

                        $par = 0;    
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $class = (($par%2) === 0?" class='dif'":"");
                            
                            $cd = 3;
                            $espaco = "-&nbsp;";
                            
                            $ref00 = (!empty($obj->val_ref00)?number_format($obj->val_ref00, $cd, ',' , '.'):"{$espaco}");
                            $ref01 = (!empty($obj->val_ref01)?number_format($obj->val_ref01, $cd, ',' , '.'):"{$espaco}");
                            $ref02 = (!empty($obj->val_ref02)?number_format($obj->val_ref02, $cd, ',' , '.'):"{$espaco}");
                            $ref03 = (!empty($obj->val_ref03)?number_format($obj->val_ref03, $cd, ',' , '.'):"{$espaco}");
                            $ref04 = (!empty($obj->val_ref04)?number_format($obj->val_ref04, $cd, ',' , '.'):"{$espaco}");
                            $ref05 = (!empty($obj->val_ref05)?number_format($obj->val_ref05, $cd, ',' , '.'):"{$espaco}");
                            $ref06 = (!empty($obj->val_ref06)?number_format($obj->val_ref06, $cd, ',' , '.'):"{$espaco}");
                            $ref07 = (!empty($obj->val_ref07)?number_format($obj->val_ref07, $cd, ',' , '.'):"{$espaco}");
                            $ref08 = (!empty($obj->val_ref08)?number_format($obj->val_ref08, $cd, ',' , '.'):"{$espaco}");
                            $ref09 = (!empty($obj->val_ref09)?number_format($obj->val_ref09, $cd, ',' , '.'):"{$espaco}");
                            $ref10 = (!empty($obj->val_ref10)?number_format($obj->val_ref10, $cd, ',' , '.'):"{$espaco}");
                            $ref11 = (!empty($obj->val_ref11)?number_format($obj->val_ref11, $cd, ',' , '.'):"{$espaco}");
                            $ref12 = (!empty($obj->val_ref12)?number_format($obj->val_ref12, $cd, ',' , '.'):"{$espaco}");
                            $ref13 = (!empty($obj->val_ref13)?number_format($obj->val_ref13, $cd, ',' , '.'):"{$espaco}");
                            $ref14 = (!empty($obj->val_ref14)?number_format($obj->val_ref14, $cd, ',' , '.'):"{$espaco}");
                            $ref15 = (!empty($obj->val_ref15)?number_format($obj->val_ref15, $cd, ',' , '.'):"{$espaco}");
                            
                            if (($ref00 === $espaco) && ($ref01 === $espaco)) {
                                $ref01 = (!empty($obj->vencto_base)?number_format($obj->vencto_base, $cd, ',' , '.'):"{$espaco}");
                            }
                            
                            $desc  = str_pad($obj->id_cargo, 2, "0", STR_PAD_LEFT) . " - " . $obj->ds_cargo;
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr class='custom-font-size-10'>";
                            $tabela .= "        <td>{$desc}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref00}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref01}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref02}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref03}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref04}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref05}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref06}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref07}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref08}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref09}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref10}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref11}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref12}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref13}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref14}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$ref15}</td>";
                            $tabela .= "    </tr>";
                            
                            $ln  = $desc  . "|";
                            $ln .= ($ref00 !== $espaco?$ref00:"") . "|";
                            $ln .= ($ref01 !== $espaco?$ref01:"") . "|";
                            $ln .= ($ref02 !== $espaco?$ref02:"") . "|";
                            $ln .= ($ref03 !== $espaco?$ref03:"") . "|";
                            $ln .= ($ref04 !== $espaco?$ref04:"") . "|";
                            $ln .= ($ref05 !== $espaco?$ref05:"") . "|";
                            $ln .= ($ref06 !== $espaco?$ref06:"") . "|";
                            $ln .= ($ref07 !== $espaco?$ref07:"") . "|";
                            $ln .= ($ref08 !== $espaco?$ref08:"") . "|";
                            $ln .= ($ref09 !== $espaco?$ref09:"") . "|";
                            $ln .= ($ref10 !== $espaco?$ref10:"") . "|";
                            $ln .= ($ref11 !== $espaco?$ref11:"") . "|";
                            $ln .= ($ref12 !== $espaco?$ref12:"") . "|";
                            $ln .= ($ref13 !== $espaco?$ref13:"") . "|";
                            $ln .= ($ref14 !== $espaco?$ref14:"") . "|";
                            $ln .= ($ref15 !== $espaco?$ref15:"");

                            $desc = removerAcentos($desc);
                            // Gerar linha de registro nos arquivos HTML e TXT
                            $es = fwrite($fp_txt, $ln . "\r\n");
                            $es = fwrite($fp_htm, 
                                  "     <tr{$class}>"
                                . "         <td>{$desc}</td>"
                                . "         <td style='text-align: right;'>{$ref00}</td>"
                                . "         <td style='text-align: right;'>{$ref01}</td>"
                                . "         <td style='text-align: right;'>{$ref02}</td>"
                                . "         <td style='text-align: right;'>{$ref03}</td>"
                                . "         <td style='text-align: right;'>{$ref04}</td>"
                                . "         <td style='text-align: right;'>{$ref05}</td>"
                                . "         <td style='text-align: right;'>{$ref06}</td>"
                                . "         <td style='text-align: right;'>{$ref07}</td>"
                                . "         <td style='text-align: right;'>{$ref08}</td>"
                                . "         <td style='text-align: right;'>{$ref09}</td>"
                                . "         <td style='text-align: right;'>{$ref10}</td>"
                                . "         <td style='text-align: right;'>{$ref11}</td>"
                                . "         <td style='text-align: right;'>{$ref12}</td>"
                                . "         <td style='text-align: right;'>{$ref13}</td>"
                                . "         <td style='text-align: right;'>{$ref14}</td>"
                                . "         <td style='text-align: right;'>{$ref15}</td>"
                                . "     </tr>" . "\r\n");
                            
                            $par += 1;
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                        
                        // Gerar linha total dos registros no arquivo HTML
//                        $es = fwrite($fp_htm, 
//                              " </tbody>"
//                            . " <tfoot>"
//                            . "     <tr>"
//                            . "         <th class='rodape'>Registros : {$par}</th>"
//                            . "         <th class='rodape'>{$total_servidores}</th>"
//                            . "         <th class='rodape'>" . number_format($total_bases, 2, ',' , '.') . "</th>"
//                            . "         <th class='rodape'>" . number_format($total_vencimento, 2, ',' , '.') . "</th>"
//                            . "         <th class='rodape'>" . number_format($total_descontos, 2, ',' , '.') . "</th>"
//                            . "         <th class='rodape'>" . number_format($total_salarios, 2, ',' , '.') . "</th>"
//                            . "     </tr>"
//                            . " </tfoot>"
//                            . "</table>" . "\r\n");
                        $es = fwrite($fp_htm, 
                              " </tbody>" . "\r\n"
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
