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
                case 'consultar_fichafinanceira' : {
                    try {
                        $id_cliente  = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'un')));
                        $id_servidor = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'sv')));
                        $cp_servidor = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cp')));
                        $nr_ano = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_exercicio')));
                        
                        $id_servidor_automativo = ($cp_servidor === 0?$id_servidor:$cp_servidor);
                        
                        // Gerar arquivo HTML e TXT
                        $nm_arquivo_htm = "FFS_" . $id_servidor . $nr_ano . "_" . md5($id_cliente) . ".html";
                        $nm_arquivo_txt = "FFS_" . $id_servidor . $nr_ano . "_" . md5($id_cliente) . ".txt";
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
                        $es = fwrite($fp_txt, "CARGO/EVENTOS"
                                . "|JAN"
                                . "|FEV"
                                . "|MAR"
                                . "|ABR"
                                . "|MAI"
                                . "|JUN"
                                . "|JUL"
                                . "|AGO"
                                . "|SET"
                                . "|OUT"
                                . "|NOV"
                                . "|DEZ"
                                . "|1a. PARTE 13o SAL."
                                . "|2a. PARTE 13o SAL."
                                . "|ABONO"
                                . "|TOTAL" . "\r\n");
                        $es = fwrite($fp_htm, 
                              "<table id='tb_fichafinanceira' cellspacing='0' width='100%'>"
                            . " <thead>"
                            . "     <tr>"
                            . "         <th class='titulo esquerda'>Cargo/Eventos</th>"
                            . "         <th class='titulo direita'>Jan</th>"
                            . "         <th class='titulo direita'>Fev</th>"
                            . "         <th class='titulo direita'>Mar</th>"
                            . "         <th class='titulo direita'>Abr</th>"
                            . "         <th class='titulo direita'>Mai</th>"
                            . "         <th class='titulo direita'>Jun</th>"
                            . "         <th class='titulo direita'>Jul</th>"
                            . "         <th class='titulo direita'>Ago</th>"
                            . "         <th class='titulo direita'>Set</th>"
                            . "         <th class='titulo direita'>Out</th>"
                            . "         <th class='titulo direita'>Nov</th>"
                            . "         <th class='titulo direita'>Dez</th>"
                            . "         <th class='titulo direita'>13o.1</th>"
                            . "         <th class='titulo direita'>13o.2</th>"
                            . "         <th class='titulo direita'>Abono</th>"
                            . "         <th class='titulo direita'>Total</th>"
                            . "     </tr>"
                            . " </thead>"
                            . " <tbody>" . "\r\n");
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover table-responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr>";
                        $tabela .= "            <th class='numeric' style='text-align: left;'>Cargo/Eventos</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Jan</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Fev</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Mar</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Abr</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Mai</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Jun</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Jul</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Ago</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Set</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Out</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Nov</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Dez</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>13o. 1</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>13o. 2</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Abono</th>";
                        $tabela .= "            <th class='numeric' style='text-align: right;'>Total</th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        
                        // Listar Cargo/Função
                        $sql = 
                             "Select distinct  "
                            ."    c1.id_cargo  "
                            ."  , trim(c1.descricao) as descricao "
                            ."from REMUN_BASE_CALC_MES s "
                            ."  left join REMUN_CARGO_FUNCAO c1 on (c1.id_cliente = s.id_cliente and c1.id_cargo = s.id_cargo_origem) "
                            ."where s.ano_mes between '{$nr_ano}01' and '{$nr_ano}15' "
                            ."  and s.id_cliente  = {$id_cliente} "
                            ."  and s.id_servidor = {$id_servidor_automativo} "; 

                        $total_servidores = 0;
                        $total_bases      = 0.0;
                        $total_vencimento = 0.0;
                        $total_descontos  = 0.0;
                        $total_salarios   = 0.0;
                        
                        $par = 0;    
                        $crg = $pdo->query($sql);
                        while (($crgObj = $crg->fetch(PDO::FETCH_OBJ)) !== false) {
                            $class = (($par%2) === 0?" class='dif'":"");
                            
                            $id_cargo = (int)$crgObj->id_cargo;
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr>";
                            $tabela .= "        <td colspan='17' style='text-align: left;'><strong>{$crgObj->id_cargo} - {$crgObj->descricao}</strong></td>";
                            $tabela .= "    </tr>";
                            
                            // Gerar linha de registro nos arquivos HTML e TXT
                            $ln  = $crgObj->id_cargo . " - " . $crgObj->descricao  . "|";
                            
                            $es = fwrite($fp_txt, $ln . "\r\n");
                            $es = fwrite($fp_htm, 
                                  "     <tr>"
                                . "         <td colspan='17' style='text-align: left;'><strong>{$crgObj->id_cargo} - {$crgObj->descricao}</strong></td>"
                                . "     </tr>" . "\r\n");
                            
                            // Listar Tipos de Eventos por Cargo/Função   
                            $sql = 
                                  "Select distinct   "
                                . "    r.id_evento   "
                                . "  , trim(r.cod_evento)  as cod_evento  "
                                //. "  , trim(r.descricao)   as descricao   "
                                . "  , trim(a.descricao)   as descricao   "
                                . "  , trim(r.tipo_evento) as tipo_evento "
                                . "  , Case trim(r.tipo_evento) "
                                . "      when 'V' then 'VENCIMENTOS/VANTAGENS' "
                                . "      when 'D' then 'DESCONTOS' "
                                . "    end as tipo_evento_descricao "
                                . "from REMUN_EVENTO_CALC_MES r "
                                . "  inner join REMUN_BASE_CALC_MES s on (s.id_cliente = r.id_cliente and s.id_servidor = r.id_servidor and s.ano_mes = r.ano_mes and s.parcela = r.parcela) "
                                    
                                . "  inner join ( "
                                . "    Select "
                                . "        x.id_evento "
                                . "      , x.descricao "
                                . "    from REMUN_EVENTO_CALC_MES x "
                                . "      inner join ( "
                                . "        Select "
                                . "            e.id_evento "
                                . "          , max(e.ano_mes) as ano_mes "
                                . "        from REMUN_EVENTO_CALC_MES e "
                                . "        where e.id_cliente  = {$id_cliente}  "
                                . "          and e.id_servidor = {$id_servidor_automativo} "
                                . "          and e.ano_mes between '{$nr_ano}01' and '{$nr_ano}15' "
                                . "        group by "
                                . "            e.id_evento "
                                . "      ) d on (d.id_evento = x.id_evento and d.ano_mes = x.ano_mes) "
                                . "    where x.id_cliente  = {$id_cliente}  "
                                . "      and x.id_servidor = {$id_servidor_automativo} "
                                . "  ) a on (a.id_evento = r.id_evento) "
                                    
                                . "where r.id_cliente  = {$id_cliente}  "
                                . "  and r.id_servidor = {$id_servidor_automativo} "
                                . "  and r.ano_mes between '{$nr_ano}01' and '{$nr_ano}15' "
                                . "  and s.id_cargo_origem = {$id_cargo} "
                                . "order by "
                                . "    r.tipo_evento  desc"
                                . "  , r.cod_evento ";
                                //. "  , r.descricao ";
                            
                            $totais = [
                                  'V' => [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0]
                                , 'D' => [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0]
                                ];
                            
                            $tipo_evento = "";
                            $ven = $pdo->query($sql);
                            while (($venObj = $ven->fetch(PDO::FETCH_OBJ)) !== false) {
                                $class = (($par%2) === 0?" class='dif'":"");
                                
                                $id_evento = (int)$venObj->id_evento;
                                
                                if ($venObj->tipo_evento !== $tipo_evento) {
                                    $tipo_evento  = $venObj->tipo_evento;
                                    $tp_descricao = trim($venObj->tipo_evento_descricao);
                                    
                                    // Gerar linha de registro da Consulta em página
                                    $tabela .= "    <tr>";
                                    $tabela .= "        <td colspan='17' style='text-align: left;'><strong><cite>{$tp_descricao}</cite></strong></td>";
                                    $tabela .= "    </tr>";

                                    // Gerar linha de registro nos arquivos HTML e TXT
                                    $ln  = $venObj->tipo_evento . " - " . trim($venObj->tipo_evento_descricao)  . "|";

                                    $es = fwrite($fp_txt, $ln . "\r\n");
                                    $es = fwrite($fp_htm, 
                                          "     <tr>"
                                        . "         <td colspan='17' style='text-align: left;'><strong><cite>{$tp_descricao}</cite></strong></td>"
                                        . "     </tr>" . "\r\n");
                                    
                                }
                                
                                // Gerar linha de registro da Consulta em página
                                $tabela .= "    <tr>";
                                $tabela .= "        <td colspan='17' style='text-align: left;'>{$venObj->cod_evento} - {$venObj->descricao}</td>";
                                $tabela .= "    </tr>";

                                // Gerar linha de registro nos arquivos HTML e TXT
                                $ln  = $venObj->cod_evento . " - " . $venObj->descricao  . "|";

                                $es = fwrite($fp_txt, $ln . "\r\n");
                                $es = fwrite($fp_htm, 
                                      "     <tr>"
                                    . "         <td colspan='17' style='text-align: left;'>{$venObj->cod_evento} - {$venObj->descricao}</td>"
                                    . "     </tr>" . "\r\n");
                                  
                                // Listar os Valores por Tipo de Eventos
                                $campo = ["", "jan", "fev", "mar", "abr", "mai", "jun", "jul", "ago", "set", "out", "nov", "dez", "p113", "p213", "abo"];    
                                $sql_valores = "";
                                for ($par = 1; $par < 16; $par++) {
                                    $ref = str_pad($par, 2, "0", STR_PAD_LEFT);
                                    $sql_valores .=  "  , coalesce(sum(case when substring(r.ano_mes from 5 for 2) = '{$ref}' then r.ref_qtd else 0.0 end), 0.0) as {$campo[$par]}_quant ";
                                    $sql_valores .=  "  , coalesce(sum(case when substring(r.ano_mes from 5 for 2) = '{$ref}' then r.valor   else 0.0 end), 0.0) as {$campo[$par]}_valor ";
                                } 
                                
                                $sql = 
                                      "Select  "
                                    . "    r.cod_evento  "
                                    . "  , r.tipo_evento "
                                    . "  {$sql_valores} "
                                    . "from REMUN_EVENTO_CALC_MES r "
                                    . "  inner join REMUN_BASE_CALC_MES s on (s.id_cliente = r.id_cliente and s.id_servidor = r.id_servidor and s.ano_mes = r.ano_mes and s.parcela = r.parcela) "
                                    . "where r.id_cliente  = {$id_cliente}  "
                                    . "  and r.id_servidor = {$id_servidor_automativo} "
                                    . "  and r.ano_mes between '{$nr_ano}01' and '{$nr_ano}15' "
                                    . "  and s.id_cargo_origem = {$id_cargo} "
                                    . "  and r.id_evento       = {$id_evento} "
                                    . "group by "
                                    . "    r.cod_evento  "
                                    . "  , r.tipo_evento ";
                                    
                                $vlr = $pdo->query($sql);
                                while (($vlrArray = $vlr->fetch(PDO::FETCH_BOTH)) !== false) {
                                    $class = (($par%2) === 0?" class='dif'":"");
                                    
                                    // Gerar linha de registro da Consulta em página (Quantidade)
                                    $tabela .= "    <tr>";
                                    $tabela .= "         <td style='text-align: right;'>&nbsp;</td>";
                                    
                                    $ln  = "..";
                                    $htm = "<td style='text-align: right;'>&nbsp;</td>";

                                    $tt_quant = 0.0;
                                    for ($x = 1; $x < 16; $x++) {
                                        // Gerar linha de registro da Consulta em página (Quantidade)
                                        $tt_quant += floatval($vlrArray[$campo[$x] . '_quant']);
                                        $quant     = number_format($vlrArray[$campo[$x] . '_quant'], 2, ',' , '.');
                                        $tabela .= "         <td style='text-align: right;'>{$quant}</td>";
                                        
                                        $ln  .= "|" . $quant ;
                                        $htm .= "<td style='text-align: right;'>{$quant}</td>";
                                    }
                                    
                                    $ln  .= "|" . number_format($tt_quant, 2, ',' , '.') ;
                                    $htm .= "<td style='text-align: right;'>" . number_format($tt_quant, 2, ',' , '.') . "</td>";
                                    
                                    // Gerar linha de registro da Consulta em página (Quantidade)
                                    $tabela .= "         <td style='text-align: right;'><strong>" . number_format($tt_quant, 2, ',' , '.') . "</strong></td>";
                                    $tabela .= "    </tr>";
                                    
                                    // Gerar linha de registro nos arquivos HTML e TXT
                                    $es = fwrite($fp_txt, $ln . "\r\n");
                                    $es = fwrite($fp_htm, 
                                          "     <tr{$class}>"
                                        . $htm 
                                        . "     </tr>" . "\r\n");
                                    
                                    // Gerar linha de registro da Consulta em página (Valor)
                                    $tabela .= "    <tr>";
                                    $tabela .= "         <td style='text-align: right;'>&nbsp;</td>";
                                    
                                    $ln  = "..";
                                    $htm = "<td style='text-align: right;'>&nbsp;</td>";
                                    
                                    $tt_valor = 0.0;
                                    for ($x = 1; $x < 16; $x++) {
                                        $totais[trim($tipo_evento)][$x] += floatval($vlrArray[$campo[$x] . '_valor']);
                                                
                                        // Gerar linha de registro da Consulta em página (Valor)
                                        $tt_valor += floatval($vlrArray[$campo[$x] . '_valor']);
                                        $valor     = number_format($vlrArray[$campo[$x] . '_valor'], 2, ',' , '.');
                                        $tabela .= "         <td style='text-align: right;'>{$valor}</td>";
                                        
                                        $ln  .= "|" . $valor ;
                                        $htm .= "<td style='text-align: right;'>{$valor}</td>";
                                    }
                                    
                                    $ln  .= "|" . number_format($tt_valor, 2, ',' , '.') ;
                                    $htm .= "<td style='text-align: right;'>" . number_format($tt_valor, 2, ',' , '.') . "</td>";
                                    
                                    // Gerar linha de registro da Consulta em página (Valor)
                                    $tabela .= "         <td style='text-align: right;'><strong>" . number_format($tt_valor, 2, ',' , '.') . "</strong></td>";
                                    $tabela .= "    </tr>";
                                    
                                    // Gerar linha de registro nos arquivos HTML e TXT
                                    $es = fwrite($fp_txt, $ln . "\r\n");
                                    $es = fwrite($fp_htm, 
                                          "     <tr{$class}>"
                                        . $htm 
                                        . "     </tr>" . "\r\n");

                                    $par += 1;
                                }
                                
                                $par += 1;
                            }
                            
                            $tabela .= "    <tr>";
                            $tabela .= "        <td colspan='17' style='text-align: left; font-size:2px'>&nbsp;</td>";
                            $tabela .= "    </tr>";

                            $es = fwrite($fp_htm, 
                                  "     <tr>"
                                . "         <td colspan='17' style='text-align: left; font-size:2px'>&nbsp;</td>"
                                . "     </tr>" . "\r\n");
                            
                            // Gerar linha de registro da Consulta em página (Valor)
                            $tabela .= "    <tr>";
                            $tabela .= "         <td style='text-align: left;'><strong><cite>(+) Total Vencimentos / Vantagens</cite></strong></td>";

                            $ln  = "(+) Total Vencimentos / Vantagens";
                            $htm = "<td style='text-align: left;'><strong><cite>(+) Total Vencimentos / Vantagens</cite></strong></td>";

                            $tt_valor = 0.0;
                            for ($x = 1; $x < 16; $x++) {
                                // Gerar linha de registro da Consulta em página (Valor)
                                $tt_valor += floatval($totais['V'][$x]);
                                $valor     = number_format($totais['V'][$x], 2, ',' , '.');
                                $tabela .= "         <td style='text-align: right;'><strong>{$valor}</strong></td>";

                                $ln  .= "|" . $valor ;
                                $htm .= "<td style='text-align: right;'><strong>{$valor}</strong></td>";
                            }

                            $ln  .= "|" . number_format($tt_valor, 2, ',' , '.') ;
                            $htm .= "<td style='text-align: right;'><strong>" . number_format($tt_valor, 2, ',' , '.') . "</strong></td>";

                            // Gerar linha de registro da Consulta em página (Valor)
                            $tabela .= "         <td style='text-align: right;'><strong>" . number_format($tt_valor, 2, ',' , '.') . "</strong></td>";
                            $tabela .= "    </tr>";

                            // Gerar linha de registro nos arquivos HTML e TXT
                            $es = fwrite($fp_txt, $ln . "\r\n");
                            $es = fwrite($fp_htm, 
                                  "     <tr{$class}>"
                                . $htm 
                                . "     </tr>" . "\r\n");
                            
//                            // Gerar linha de registro da Consulta em página
//                            $tabela .= "    <tr>";
//                            $tabela .= "        <td colspan='17' style='text-align: left;'><strong><cite>(-) Total Descontos</cite></strong></td>";
//                            $tabela .= "    </tr>";
//                            
//                            // Gerar linha de registro nos arquivos HTML e TXT
//                            $ln  = "(-) Total Descontos|";
//
//                            $es = fwrite($fp_txt, $ln . "\r\n");
//                            $es = fwrite($fp_htm, 
//                                  "     <tr>"
//                                . "         <td colspan='17' style='text-align: left;'><strong><cite>(-) Total Descontos</cite></strong></td>"
//                                . "     </tr>" . "\r\n");
//                            
                            // Gerar linha de registro da Consulta em página (Valor)
                            $tabela .= "    <tr>";
                            $tabela .= "         <td style='text-align: left;'><strong><cite>(-) Total Descontos</cite></strong></td>";

                            $ln  = "(-) Total Descontos";
                            $htm = "<td style='text-align: left;'><strong><cite>(-) Total Descontos</cite></strong></td>";

                            $tt_valor = 0.0;
                            for ($x = 1; $x < 16; $x++) {
                                // Gerar linha de registro da Consulta em página (Valor)
                                $tt_valor += floatval($totais['D'][$x]);
                                $valor     = number_format($totais['D'][$x], 2, ',' , '.');
                                $tabela .= "         <td style='text-align: right;'><strong>{$valor}</strong></td>";

                                $ln  .= "|" . $valor ;
                                $htm .= "<td style='text-align: right;'><strong>{$valor}</strong></td>";
                            }

                            $ln  .= "|" . number_format($tt_valor, 2, ',' , '.') ;
                            $htm .= "<td style='text-align: right;'><strong>" . number_format($tt_valor, 2, ',' , '.') . "</strong></td>";

                            // Gerar linha de registro da Consulta em página (Valor)
                            $tabela .= "         <td style='text-align: right;'><strong>" . number_format($tt_valor, 2, ',' , '.') . "</strong></td>";
                            $tabela .= "    </tr>";

                            // Gerar linha de registro nos arquivos HTML e TXT
                            $es = fwrite($fp_txt, $ln . "\r\n");
                            $es = fwrite($fp_htm, 
                                  "     <tr{$class}>"
                                . $htm 
                                . "     </tr>" . "\r\n");
                            
//                            // Gerar linha de registro da Consulta em página
//                            $tabela .= "    <tr>";
//                            $tabela .= "        <td colspan='17' style='text-align: left;'><strong><cite>(=) Salário Líquido</cite></strong></td>";
//                            $tabela .= "    </tr>";
//                            
//                            // Gerar linha de registro nos arquivos HTML e TXT
//                            $ln  = "(=) Salário Líquido|";
//
//                            $es = fwrite($fp_txt, $ln . "\r\n");
//                            $es = fwrite($fp_htm, 
//                                  "     <tr>"
//                                . "         <td colspan='17' style='text-align: left;'><strong><cite>(=) Salário Líquido</cite></strong></td>"
//                                . "     </tr>" . "\r\n");
//                            
                            // Gerar linha de registro da Consulta em página (Valor)
                            $tabela .= "    <tr>";
                            $tabela .= "         <td style='text-align: left;'><strong><cite>(=) Salário Líquido</cite></strong></td>";

                            $ln  = "(=) Salário Líquido";
                            $htm = "<td style='text-align: left;'><strong><cite>(=) Salário Líquido</cite></strong></td>";

                            $tt_valor = 0.0;
                            for ($x = 1; $x < 16; $x++) {
                                // Gerar linha de registro da Consulta em página (Valor)
                                $salario   = ($totais['V'][$x] - $totais['D'][$x]);
                                $tt_valor += floatval($salario);
                                $valor     = number_format($salario, 2, ',' , '.');
                                $tabela .= "         <td style='text-align: right;'><strong>{$valor}</strong></td>";

                                $ln  .= "|" . $valor ;
                                $htm .= "<td style='text-align: right;'><strong>{$valor}</strong></td>";
                            }

                            $ln  .= "|" . number_format($tt_valor, 2, ',' , '.') ;
                            $htm .= "<td style='text-align: right;'><strong>" . number_format($tt_valor, 2, ',' , '.') . "</strong></td>";

                            // Gerar linha de registro da Consulta em página (Valor)
                            $tabela .= "         <td style='text-align: right;'><strong>" . number_format($tt_valor, 2, ',' , '.') . "</strong></td>";
                            $tabela .= "    </tr>";

                            // Gerar linha de registro nos arquivos HTML e TXT
                            $es = fwrite($fp_txt, $ln . "\r\n");
                            $es = fwrite($fp_htm, 
                                  "     <tr{$class}>"
                                . $htm 
                                . "     </tr>" . "\r\n");
                            
                            $par += 1;
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                        
                        // Gerar linha total dos registros no arquivo HTML
                        $es = fwrite($fp_htm, 
                              " <tbody>"
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
