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
    
    function get_nome_mes($value) {
        $intValue = empty($value) ? 0 : intVal( $value );
        $meses = ['No-Value','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','Dec. Terc. 1º Parcela','Dec. Terc. Parcela Final','Abono Fundeb'];
        return $meses[$intValue]; 
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ac'])) {
            switch ($_POST['ac']) {
                case 'consultar_contra_cheque' : {
                    try {
                        $id_cliente  = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'un')));
                        $id_servidor = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'sv')));
                        $cp_servidor = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cp')));
                        $nr_ano = (int)preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_ano')));
                        $nr_mes = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_mes')));
                        $nr_par = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_par')));
                        
//                        // Gerar arquivo HTML e TXT
//                        $nm_arquivo_htm = "Contra_Cheque_" . $id_servidor. $nr_ano . $nr_mes . "_" . md5($id_cliente) . ".html";
//                        $nm_arquivo_txt = "Contra_Cheque_" . $id_servidor. $nr_ano . $nr_mes . "_" . md5($id_cliente) . ".txt";
//                        $file_txt = '../downloads/' . $nm_arquivo_txt;
//                        if (file_exists($file_txt)) {
//                            unlink($file_txt);
//                        }
//                        $file_htm = '../downloads/' . $nm_arquivo_htm;
//                        if (file_exists($file_htm)) {
//                            unlink($file_htm);
//                        }
//                        $fp_txt = fopen('../downloads/' . $nm_arquivo_txt, "a");
//                        $fp_htm = fopen('../downloads/' . $nm_arquivo_htm, "a");
//                        
//                        // Gerar cabeçalho de campos no HTML e no TXT
//                        $es = fwrite($fp_txt, "COMPETÊNCIA|CENTRO DE CUSTO|LOCAL DE TRABALHO|BASE|TOTAL VENCIMENTOS|DESCONTOS|SALÁRIOS" . "\r\n");
//                        $es = fwrite($fp_htm, 
//                              "<table id='tb_contracheque' cellspacing='0' width='100%'>"
//                            . " <thead>"
//                            . "     <tr>"
//                            . "         <th class='titulo esquerda'>Competência</th>"
//                            . "         <th class='titulo esquerda'>Centro de Custo</th>"
//                            . "         <th class='titulo esquerda'>Local de Trabalho</th>"
//                            . "         <th class='titulo direita'>Base (R$)</th>"
//                            . "         <th class='titulo direita'>Vencimentos (R$)</th>"
//                            . "         <th class='titulo direita'>Descontos (R$)</th>"
//                            . "         <th class='titulo direita'>Salários (R$)</th>"
//                            . "     </tr>"
//                            . " </thead>"
//                            . " <tbody>" . "\r\n");
//                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr>";
                        $tabela .= "            <th>Competência</th>";
                        $tabela .= "            <th>Centro de Custo</th>";
                        $tabela .= "            <th>Local de Trabalho</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>Base</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>Vencimentos</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>Descontos</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>Salários</th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $id_servidor_automativo = ($cp_servidor === 0?$id_servidor:$cp_servidor);
                        
                        $ln  = "";
                        $am  = $nr_ano . $nr_mes;
                        $sql = 
                             "Select "
                            ."    c.* "
                            ."  , f.descricao as ds_cargo_atual  "
                            ."  , u.descricao as nm_unid_gestora "
                            ."from REMUN_BASE_CALC_MES c "
                            ."  left join REMUN_CARGO_FUNCAO f on (f.id_cliente = c.id_cliente and f.id_cargo = c.id_cargo_atual) "
                            ."  left join REMUN_UNID_GESTORA u on (u.id_cliente = c.id_cliente and u.id = c.id_unid_gestora) "
                            ."where c.id_cliente  = {$id_cliente}" 
                            ."  and c.id_servidor = {$id_servidor_automativo}"
                            ."  and c.parcela     = '{$nr_par}'"
                            . ($nr_mes === "0"?"  and substring(c.ano_mes from 1 for 4) = '{$nr_ano}'":"  and c.ano_mes = '{$am}'")
                            ."order by "
                            ."    c.ano_mes DESC "; 
                        
//                        $pagamento = 0;
//                        
//                        $total_pagtos = 0;
//                        $total_vencimento_base = 0.0;
//                        $total_vencimento = 0.0;
//                        $total_descontos  = 0.0;
//                        $total_salarios   = 0.0;
//                        
                        $par = 0;    
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $class = (($par%2) === 0?" class='dif'":"");
                            
//                            $pagamento    += 1;
                            $vencimento_base = number_format($obj->vencto_base_cargo,  2, ',' , '.');
                            $total_venc    = number_format($obj->tot_venctos,   2, ',' , '.');
                            $total_desc    = number_format($obj->tot_descontos, 2, ',' , '.');
                            $total_liquido = number_format($obj->sal_liquido,   2, ',' , '.');

                            $id_link = trim(md5($id_cliente) . "_" . $id_cliente . "_" . $id_servidor_automativo . "_" . $nr_ano . "_" . str_replace($nr_ano, "", $obj->ano_mes) . "_" . $nr_par);
                            $ds_mes  = substr($obj->ano_mes, 4, 2) . ". " . get_nome_mes(substr($obj->ano_mes, 4, 2));
                            $onclick = "onclick='pdfContraChequeNovo(this.id)'";
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr>";
                            $tabela .= "        <td><a  id='{$id_link}' href='javascript:void(0);'  title='Baixar PDF do Contra-Cheque' {$onclick}>&nbsp;<i class='glyph-icon icon-file-pdf-o'></i>&nbsp;&nbsp;&nbsp;{$ds_mes}</a></tb>"; // Competência
                            $tabela .= "        <td>{$obj->descr_sub_unid_orcam}</tb>"; // Centro de Custo
                            $tabela .= "        <td>{$obj->descr_unid_lotacao}</tb>";   // Local de Trabalho
                            $tabela .= "        <td style='text-align: right;'>{$vencimento_base}</tb>";
                            $tabela .= "        <td style='text-align: right;'>{$total_venc}</tb>";
                            $tabela .= "        <td style='text-align: right;'>{$total_desc}</tb>";
                            $tabela .= "        <td style='text-align: right;'>{$total_liquido}</tb>";
                            $tabela .= "    </tr>";
                            
//                            // Gerar linha de registro nos arquivos TXT
//                            $ln  = $obj->ano_mes . "|";
//                            $ln .= $obj->descr_sub_unid_orcam . "|";
//                            $ln .= $obj->descr_unid_lotacao   . "|";
//                            $ln .= $vencimento_base . "|";
//                            $ln .= $total_venc      . "|";
//                            $ln .= $total_desc      . "|";
//                            $ln .= $total_liquido;
//
//                            // Gerar linha de registro nos arquivos HTML 
//                            $es = fwrite($fp_txt, $ln . "\r\n");
//                            $es = fwrite($fp_htm, 
//                                  "     <tr{$class}>"
//                                . "         <td>{$obj->ano_mes}</th>"              // Competência
//                                . "         <td>{$obj->descr_sub_unid_orcam}</th>" // Centro de Custo
//                                . "         <td>{$obj->descr_unid_lotacao}</th>"   // Local de Trabalho
//                                . "         <td style='text-align: right;'>{$vencimento_base}</th>"
//                                . "         <td style='text-align: right;'>{$total_venc}</th>"
//                                . "         <td style='text-align: right;'>{$total_desc}</th>"
//                                . "         <td style='text-align: right;'>{$total_liquido}</th>"
//                                . "     </tr>" . "\r\n");
//                            
//                            $par += 1;
//                            
//                            $total_pagtos          += $pagamento;
//                            $total_vencimento_base += $obj->vencto_base_cargo;
//                            $total_vencimento      += $obj->tot_venctos;
//                            $total_descontos       += $obj->tot_descontos;   
//                            $total_salarios        += $obj->sal_liquido;
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
//                        
//                        // Gerar linha total dos registros no arquivo HTML
//                        $es = fwrite($fp_htm, 
//                              " <tbody>"
//                            . " <tfoot>"
//                            . "     <tr>"
//                            . "         <th class='rodape'>Registros : {$par}</th>"
//                            . "         <th class='rodape'>{$total_pagtos}</th>"
//                            . "         <th class='rodape'>" . number_format($total_vencimento_base, 2, ',' , '.') . "</th>"
//                            . "         <th class='rodape'>" . number_format($total_vencimento, 2, ',' , '.') . "</th>"
//                            . "         <th class='rodape'>" . number_format($total_descontos, 2, ',' , '.') . "</th>"
//                            . "         <th class='rodape'>" . number_format($total_salarios, 2, ',' , '.') . "</th>"
//                            . "     </tr>"
//                            . " </tfoot>"
//                            . "</table>" . "\r\n");
//                        
//                        fclose($fp_txt);
//                        fclose($fp_htm);
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
            }
        } else {
            echo "Erro ao tentar identificar ação requistada!";
        }
    }
