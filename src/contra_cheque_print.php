<!DOCTYPE html>
<?php
    // Importa arquivo de config da classe DOMPDF
    /**
     * Armazena saída do HTML em buffer
     * Referências
     * http://php.net/manual/pt_BR/function.ob-start.php
     */ 
    //require_once '../lib/dompdf/dompdf_config.inc.php';
    //ini_set('memory_limit', '512M');
    include("../lib/mpdf60/mpdf.php");
    
    ob_start();
    
    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';

    $id_ser = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'ser')));
    $nr_ano = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'ano')));
    $nr_mes = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'mes')));
    $nr_par = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'par')));
    
    $unidade     = "0";
    $brasao_unid = "../dist/img/remuneratus_logo.png";
    $brasao_tama = "100";
    $md5_unidade = trim(filter_input(INPUT_GET, 'un')); 
    $des_unidade = "Título da Unidade no Portal";
    $inf_unidade = "Informações da Unidade";
    
    //$arquivo_css = file_get_contents("./contra_cheque_template.css");
    $arquivo_tpt = file_get_contents("./contra_cheque_template.html");
    
    $dados = [];
    $dados_cliente     = [];
    $dados_servidor    = [];
    $dados_competencia = [];
    $dados_base_calc   = [];
    
    $dados_eventos_calc  = [];
    $dados_vencimentos_v = [];
    $dados_vencimentos_d = [];
    $dados_vencimentos   = []; 
    
    function getNameMes($value) {
        $intValue = empty($value) ? 0 : intVal( $value );
        $meses = ['No-Value'
            , 'JANEIRO'             // 01
            , 'FEVEREIRO'           // 02
            , 'MARÇO'               // 03
            , 'ABRIL'               // 04
            , 'MAIO'                // 05
            , 'JUNHO'               // 06
            , 'JULHO'               // 07
            , 'AGOSTO'              // 08
            , 'SETEMBRO'            // 09    
            , 'OUTUBRO'             // 10
            , 'NOVEMBRO'                 // 11
            , 'DEZEMBRO'                 // 12
            , 'DEC. TERC. 1º PARCELA'    // 13
            , 'DEC. TERC. PARCELA FINAL' // 14
            , 'ABONO FUNDEB'             // 15   
            , 'SEXTO DE FERIAS'          // 'ABONO PASEP/OUTROS'       // 16 (Provisório)
        ];
        return $meses[$intValue]; 
    }
    
    function filtro_servidor($dados) {
        $dados['id_servidor'] = str_pad($dados['id_servidor'], 5, "0", STR_PAD_LEFT);
        $dados['dt_admissao'] = !empty( $dados['dt_admissao'] ) ? date('d/m/Y', strtotime($dados['dt_admissao']) ) : '';
        $dados['cpf'] = formatarTexto('###.###.###-##', $dados['cpf']);
        return $dados;
    }
    
    function filtro_base_calc($dados) {
        $dados['id_servidor']   = str_pad($dados['id_servidor'], 5, "0", STR_PAD_LEFT);
        $dados['tot_venctos']   = number_format( $dados['tot_venctos'], 2,',','.' );
        $dados['tot_descontos'] = number_format( $dados['tot_descontos'], 2,',','.' );
        $dados['sal_liquido']   = number_format( $dados['sal_liquido'], 2,',','.' );
        $dados['bc_sal_fam']    = number_format( $dados['bc_sal_fam'],  2,',','.' );
        $dados['bc_ats']        = number_format( $dados['bc_ats'],      2,',','.' );
        $dados['bc_ferias']     = number_format( $dados['bc_ferias'],   2,',','.' );
        $dados['bc_dec_terc']   = number_format( $dados['bc_dec_terc'], 2,',','.' );
        $dados['bc_faltas']     = number_format( $dados['bc_faltas'],   2,',','.' );
        $dados['bc_previd']     = number_format( $dados['bc_previd'],   2,',','.' );
        $dados['bc_irrf']       = number_format( $dados['bc_irrf'],     2,',','.' );
        $dados['bc_outra1']     = number_format( $dados['bc_outra1'],   2,',','.' );
        $dados['bc_outra2']     = number_format( $dados['bc_outra2'],   2,',','.' );
        $dados['bc_outra3']     = number_format( $dados['bc_outra3'],   2,',','.' );
        $dados['vencto_base_cargo'] = number_format( $dados['vencto_base_cargo'],2,',','.' );
        $dados['tot_deduc_depend']  = number_format( $dados['tot_deduc_depend'], 2,',','.' );
        $dados['dt_admissao'] = !empty( $dados['dt_admissao'] ) ? date('d/m/Y', strtotime($dados['dt_admissao']) ) : '';
        $dados['dt_movim1']   = !empty( $dados['dt_movim1'] ) ? date('d/m/Y', strtotime($dados['dt_movim1']) ) : '';
        $dados['dt_movim2']   = !empty( $dados['DT_MOVIM2'] ) ? date('d/m/Y', strtotime($dados['dt_movim2']) ) : '';
        $dados['tipo_sal']    = $dados['tipo_sal'] == 1 ? 'NORMAL' : 'HORA-AULA';
        return $dados;
    }

    function filtro_evento_calc($dados) {
        $count = count($dados);
        for ($i = 0; $i < $count; $i++) { 
            $dados[$i]['ref_qtd'] = number_format( $dados[$i]['ref_qtd'],2,',','' );
            $dados[$i]['valor']   = number_format( $dados[$i]['valor'],  2,',','.' );
        }
        return $dados;
    }
    /*
    function montar_vencimentos($dados, $venc) {
        $arrVenctos  = [];
        $strVencitos = ""; //'<tr>';
        
        foreach ($dados as $key => $venctos) {
            if ( trim($venctos['tipo_evento']) === $venc ) {
                //$strVencitos .= "<tr>";
                $strVencitos .= "<td class='centerText'>{$venctos['cod_evento']}</td>";
                $strVencitos .= "<td>{$venctos['descricao']}{$venctos['observacao']}</td>";
                $strVencitos .= "<td class='rightText'>{$venctos['ref_qtd']}</td>";
                $strVencitos .= "<td class='rightText'>{$venctos['valor']}</td>";
                //$strVencitos .= "</tr>";
                
                array_push($arrVenctos, $venctos);
            }
        }
        
        $num = count($arrVenctos);
        for ($i = $num; $i < 20; $i++) {
            //$strVencitos .= "<tr>";
            $strVencitos .= "<td class='centerText'>&nbsp;</td>";
            $strVencitos .= "<td>&nbsp;</td>";
            $strVencitos .= "<td class='rightText'>&nbsp;</td>";
            $strVencitos .= "<td class='rightText'>&nbsp;</td>";
            //$strVencitos .= "</tr>";
        }
        //var_dump($strVencitos);die;
        return ["VENCIMENTO{$venc}"=>$strVencitos];
    }
    */
    function juntar_vencimentos($vencimentos_v, $vencimentos_d) {
        //var_dump($vencimentos_v); 
        //var_dump($vencimentos_d[0]); die;
        //$arrVenctosV = [];
        //$arrVenctosD = [];
        $strVencitos = ""; 

        $i = 0;
        $l = 0;
        /*
        foreach ($vencimentos_v as $key => $venctosV) {
            $strVencitos .= "<tr>";
            
            $strVencitos .= "<td class='centerText borderLeft'>{$venctosV['cod_evento']}</td>";
            $strVencitos .= "<td>{$venctosV['descricao']}{$venctosV['observacao']}</td>";
            $strVencitos .= "<td class='rightText'>{$venctosV['ref_qtd']}</td>";
            $strVencitos .= "<td class='rightText borderRight'>{$venctosV['valor']}</td>";
            
            $strVencitos .= "<td class='no-border'>&nbsp;</td>";
            
            if (!empty($vencimentos_d[$i])) {
                $strVencitos .= "<td class='centerText borderLeft'>{$vencimentos_d[$i]['cod_evento']}</td>";
                $strVencitos .= "<td>{$vencimentos_d[$i]['descricao']}{$vencimentos_d[$i]['observacao']}</td>";
                $strVencitos .= "<td class='rightText'>{$vencimentos_d[$i]['ref_qtd']}</td>";
                $strVencitos .= "<td class='rightText borderRight'>{$vencimentos_d[$i]['valor']}</td>";
                $i += 1;
            } else {
                $strVencitos .= "<td class='centerText borderLeft'>&nbsp;</td>";
                $strVencitos .= "<td>&nbsp;</td>";
                $strVencitos .= "<td class='rightText'>&nbsp;</td>";
                $strVencitos .= "<td class='rightText borderRight'>&nbsp;</td>";
            }
            
            $strVencitos .= "</tr>";

            $l += 1;
            array_push($arrVenctosV, $venctosV);
            //array_push($arrVenctosD, $venctosD);
        }
        
        for ($i = $l; $i < 20; $i++) {
            $strVencitos .= "<tr>";
            
            $strVencitos .= "<td class='centerText borderLeft'>&nbsp;</td>";
            $strVencitos .= "<td>&nbsp;</td>";
            $strVencitos .= "<td class='rightText'>&nbsp;</td>";
            $strVencitos .= "<td class='rightText borderRight'>&nbsp;</td>";
            
            $strVencitos .= "<td class='no-border'>&nbsp;</td>";
            
            $strVencitos .= "<td class='centerText borderLeft'>&nbsp;</td>";
            $strVencitos .= "<td>&nbsp;</td>";
            $strVencitos .= "<td class='rightText'>&nbsp;</td>";
            $strVencitos .= "<td class='rightText borderRight'>&nbsp;</td>";
            
            $strVencitos .= "</tr>";
        }
        */
        
        for ($i = $l; $i < 20; $i++) {
            $strVencitos .= "<tr>";
            
            if (!empty($vencimentos_v[$i])) {
                $strVencitos .= "<td class='centerText borderLeft'>{$vencimentos_v[$i]['cod_evento']}</td>";
                $strVencitos .= "<td>{$vencimentos_v[$i]['descricao']}{$vencimentos_v[$i]['observacao']}</td>";
                $strVencitos .= "<td class='rightText'>{$vencimentos_v[$i]['ref_qtd']}</td>";
                $strVencitos .= "<td class='rightText borderRight'>{$vencimentos_v[$i]['valor']}</td>";
            } else {
                $strVencitos .= "<td class='centerText borderLeft'>&nbsp;</td>";
                $strVencitos .= "<td>&nbsp;</td>";
                $strVencitos .= "<td class='rightText'>&nbsp;</td>";
                $strVencitos .= "<td class='rightText borderRight'>&nbsp;</td>";
            }
            
            $strVencitos .= "<td class='no-border'>&nbsp;</td>";
            
            if (!empty($vencimentos_d[$i])) {
                $strVencitos .= "<td class='centerText borderLeft'>{$vencimentos_d[$i]['cod_evento']}</td>";
                $strVencitos .= "<td>{$vencimentos_d[$i]['descricao']}{$vencimentos_d[$i]['observacao']}</td>";
                $strVencitos .= "<td class='rightText'>{$vencimentos_d[$i]['ref_qtd']}</td>";
                $strVencitos .= "<td class='rightText borderRight'>{$vencimentos_d[$i]['valor']}</td>";
            } else {
                $strVencitos .= "<td class='centerText borderLeft'>&nbsp;</td>";
                $strVencitos .= "<td>&nbsp;</td>";
                $strVencitos .= "<td class='rightText'>&nbsp;</td>";
                $strVencitos .= "<td class='rightText borderRight'>&nbsp;</td>";
            }
            
            $strVencitos .= "</tr>";
        }
        //print_r("<table border='1'>" . $strVencitos . "</table>"); die;
        return ["VENCIMENTOS"=>$strVencitos];
    }

?>
<html>
    <head>
        <?php
        
        ini_set('default_charset', 'UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Remuneratu$ | DRH Transparência | Contra-Cheque</title>
        <link rel="shortcut icon" href="../gerasys.ico" >
        <!--<link rel="stylesheet" href="contra_cheque_template.css">-->
    </head>
    <body>
        <style type="text/css">
            @media print {
                    .noPrint {
                            display:none;
                            width:29cm;
                            font-size:7pt;
                    }
            }
            @page {
                margin-top: 1.5cm;
                margin-bottom: 1.5cm;
                margin-left: 1cm;
                margin-right: 1cm;
            }
            html { 
                margin: 25px
            }
            
            /* { font-family: Times-Roman;font-size: 12px;margin: 0;padding: 0; }
            // body { width:1024px;margin:0 auto; } */
            .row {
                display:block;
                margin-bottom: 1%; 
                font-weight: 40;
            }

            /* *** HEADER *** */
            #header { 
                width:100%;
                font-size:6.5pt; 
            }
            #header .brasao { 
                width:15%;
                text-align: center; 
            }
            #header img.brasaoImg { 
                height:75px;
                width:75px; 
            }
            #header .infoTitulo { 
                width:83%;
                padding:5px 10px; 
            }
            #header div { 
                margin-top:10px; 
            }
            #header span { 
                font-size:1.1em; 
            }

            /* *** SERVIDOR *** */
            #servidor { 
                width:100%;
                margin-top:2px;
                font-size:10pt; 
            }
            #servidor div { 
                display:block;
                margin:1%; 
            }

            #servidor .coluna { 
                width:50%; 
            }
            #servidor .matricula { 
                width:18%;
                padding:5px;
            }
            #servidor .nomeServidor { 
                width:78%;
                padding:5px;
            }
            #servidor .subUnid { 
                width:50%;
                padding:5px;
            }
            #servidor .planoCargos { 
                width:98%;
                padding:5px;
            }

            #servidor .admissao { 
                width:18%;
                padding:5px;
            }
            #servidor .funcao { 
                width:78%;
                padding:5px;
            }

            #servidor .lotacao { 
                width:50%;
                padding:5px;
            }

            #servidor .documento {
                width:32%;
                padding:5px;
            }
            #servidor .registro { 
                width:32%;
                padding:5px;
            }
            #servidor .pis { 
                width:30%;
                padding:5px;
            }

            /* *** VALORES *** */
            #valores { 
                width:100%;
                margin-top:2px;
                font-size:7pt; /*//6pt;*/ 
            }
            #valores table { 
                width:100%; 
            }
            #valores .coluna { 
                padding-left: 1px;
                width:47%;
                float:left;
                margin:0 0.5% 0; 
            }
            #valores .tituloValores { 
                /*//background: #E1E1E1;*/
                padding:5px;
            }
            #valores thead { 
                line-height: 20px; 
            }
            #valores thead th { 
                padding:5px; 
            }
            #valores tbody td { 
                padding:2px 5px;
                border-right:1px solid #000; 
            }
            #valores .codigo { 
                background: #E1E1E1;
                width:5%; /*//3%;*/
                padding:2px 0; 
            }
            #valores .descricao { 
                background: #E1E1E1;
                width:55%; /*//52%;*/
                padding:2px; 
            }
            #valores .quantidade { 
                background: #E1E1E1;
                width:6%; /*//6%;*/
                padding:2px 0; 
            }
            #valores .valor { 
                background: #E1E1E1;
                width:15%;
                padding:2px; 
            }
            #valores .totalLeg { 
                padding:5px; 
            }
            #valores .totalVal { 
                background: #E1E1E1;
                padding:5px; 
            }
            #valores .salarioLiqLeg { 
                padding:0.4%; 
            }
            #valores .salarioLiqVal { 
                padding:0.4%; 
            }

            #totalValores { 
                width:100%;padding:10px;
                margin-bottom:10px;
                font-size:6pt; 
            }

            /* *** IMPOSTO *** */
            #imposto { 
                width:100%;
                margin-top:2px;
                font-size:6.5pt;
            }
            #imposto div { 
                display:block;
                margin:0.4%; 
            }
            #imposto .coluna { 
                width:50%; 
            }

            #imposto .vencto { 
                width:20%;
                /*//padding:0.4%;*/ 
                padding:5px;
            }
            #imposto .baseCalc { 
                width:20%;
                /*//padding:0.4%;*/ 
                padding:5px;
            }
            #imposto .baseIrrf { 
                width:20%;
                /*//padding:0.4%;*/ 
                padding:5px;
            }
            #imposto .dependIrrf { 
                width:20%;
                /*//padding:0.4%;*/ 
                padding:5px;
            }
            #imposto .deducao { 
                width:20%;
                /*//padding:0.4%;*/ 
                padding:5px;
            }

            /* *** BANCO *** */
            #banco { 
                width:100%;
                margin-top:2px;
                font-size:6.5pt; 
            }
            #banco div { 
                display:block; 
            }
            #banco .coluna { 
                width:auto; 
            }
            #banco .line { 
                width:100%; 
                padding:5px;
            }
            #banco .infoBanco { 
                padding:0.4%;
                width: 47.5%; 
            }

            /* *** CUT *** */
            #cut { display:block;margin:2px 0; }
            #cut .borderCut { font:italic 0.85em Times-Roman;border-bottom: 1px dotted #CCC; }

            /*** SIGN ***/ 
            #sign { display:block;margin:2% 0; }
            #sign .dataPagto { width:38%;padding:1%;float:left; }
            #sign .signServ { width:58%;padding:1%;float:right; }


            /*** ELEMENTOS ***/ 
            .no-border { 
                border:0px solid #000; 
            }
            .border { 
                border:1px solid #000; 
            }
            .borderRight { 
                border-right:1px solid #000; 
            }
            .borderLeft { 
                border-left:1px solid #000; 
            }
            .borderTop { 
                border-top:1px solid #000; 
            }
            .borderBottom { 
                border-bottom:1px solid #000; 
            }
            .bold { 
                font-weight: bold; 
            }
            .rightText { 
                text-align: right; 
            }
            .centerText { 
                text-align: center; 
            }
            .italic { 
                font-style:italic; 
            }
            .vertTop { 
                vertical-align:top; 
            }
            .rightAlign { 
                float: right; 
            }
            .leftAlign { 
                float: left; 
            }

            .paddingLR { padding:0 5px; }
            .padLeft5 { padding-left:5px; }
            .padRight5 { padding-right:5px; }
            .arredendar_borda{
                -moz-border-radius: 5px;
                -khtml-border-radius: 5px;
                -webkit-border-radius: 5px;
                -opera-border-radius: 5px;
            }
            .radio5{
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
                -o-border-radius: 5px;
                -ms-border-radius: 5px;
            }
            .radio10{
                border-radius: 10px;
                -moz-border-radius: 10px;
                -webkit-border-radius: 10px;
                -o-border-radius: 10px;
                -ms-border-radius: 10px;
            }
            .block { 
                display:block; 
            }
            .font_mine { 
                font-size:5pt;  
            }
            <?php
                //echo $arquivo_css;
            ?>
        </style>
        
        <?php
            try {
//                $protocolo  = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === false) ? 'http' : 'https';
//                $host       = $_SERVER['HTTP_HOST'];
//                $script     = $_SERVER['SCRIPT_NAME'];
//                $parametros = $_SERVER['QUERY_STRING'];
//                $metodo     = $_SERVER['REQUEST_METHOD'];
//                $UrlAtual   = $protocolo . '://' . $host . $script . '?' . $parametros;
//
//                echo "<br>Protocolo: ".$protocolo;
//                echo "<br>Host: ".$host;
//                echo "<br>Script: ".$script;
//                echo "<br>Parametros: ".$parametros;
//                echo "<br>Metodo: ".$metodo;
//                echo "<br>Url: ".$UrlAtual."<br><br><br><br>";
//
                $cnf = Configuracao::getInstancia();
                $pdo = $cnf->db('', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = 
                     "Select "
                    ."    u.id "
                    ."  , u.nome "
                    ."  , u.cnpj "
                    ."  , u.municipio_nome "
                    ."  , u.municipio_uf "
                    ."  , coalesce(nullif(trim(u.logo), ''), '../dist/img/brasoes/ssbv.png') as brasao "    
                    ."  , trim(coalesce(nullif(trim(u.titulo_portal), ''), trim(u.nome))) as titulo_portal "
                    ."  , coalesce(u.ender_lograd, '...') as endereco "
                    ."  , coalesce(u.ender_num,    '...') as numero "
                    ."  , coalesce(u.ender_bairro, '...') as bairro "
                    ."  , coalesce(u.ender_cep,    '00000000') as cep "
                    ."from ADM_CLIENTE u "
                    ."order by "
                    ."    trim(coalesce(nullif(trim(u.titulo_portal), ''), trim(u.nome)))";

                $res = $pdo->query($sql);
                while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                    $id = md5($obj->id);
                    if ( $id === $md5_unidade ) {
                        $unidade     = $obj->id;
                        $des_unidade = $obj->titulo_portal;
                        $inf_unidade = "CNPJ: " . formatarTexto('##.###.###/####-##', $obj->cnpj);
                        $endereco    = $obj->endereco . ", " . $obj->numero . ", " . $obj->bairro;
                        $brasao_unid = $obj->brasao;
                        $brasao_tama = "40";
                        
                        // Montar dados do Cliente
                        
                        $dados_cliente['BRASAO-CLI'] = $obj->brasao;
                        $dados_cliente['NOME-CLI']   = $obj->nome;
                        $dados_cliente['CNPJ']       = formatarTexto('##.###.###/####-##', $obj->cnpj);
                        $dados_cliente['ENDER_LOGRAD-CLI'] = $obj->endereco;
                        $dados_cliente['ENDER_NUM-CLI']    = $obj->numero;
                        $dados_cliente['ENDER_BAIRRO-CLI'] = $obj->bairro;
                        $dados_cliente['ENDER_CEP-CLI']    = formatarTexto('##.###-###', $obj->cep);
                        $dados_cliente['MUNICIPIO_NOME']   = $obj->municipio_nome;
                        $dados_cliente['MUNICIPIO_UF']     = $obj->municipio_uf;
                        
                        break;
                    }
                }
                
                // Carregar dados do Servidor
                $stm = $pdo->prepare(
                     "Select "
                    ."    s.* "
                    ."from REMUN_SERVIDOR s "
                    ."where s.id_cliente  = :id_cliente  " 
                    ."  and s.id_servidor = :id_servidor "
                );
                
                $stm->execute(array(
                    ':id_cliente'  => $unidade,
                    ':id_servidor' => $id_ser
                ));
                $dados_servidor = $stm->fetchAll(PDO::FETCH_ASSOC);
                $dados_servidor = filtro_servidor($dados_servidor[0]);
                
                // Montar dados da Competência
                $dados_competencia = ['ANO_MESEXT' => getNameMes($nr_mes) . " / ". $nr_ano];
                
                // Carregar dados de Valores
                $stm = $pdo->prepare(
                     "Select "
                    ."    c.* "
                    ."  , f.descricao as desc_cargo_atual "
                    ."from REMUN_BASE_CALC_MES c "
                    ."  inner join REMUN_CARGO_FUNCAO f on (f.id_cliente = c.id_cliente and f.id_cargo = c.id_cargo_atual) "
                    ."where c.id_cliente  = :id_cliente  " 
                    ."  and c.id_servidor = :id_servidor "
                    ."  and c.ano_mes     = :ano_mes "
                    ."  and c.parcela     = :parcela "
                );
                
                $stm->execute(array(
                    ':id_cliente'  => $unidade,
                    ':id_servidor' => $id_ser,
                    ':ano_mes'     => $nr_ano . $nr_mes,
                    ':parcela'     => $nr_par
                ));
                $dados_base_calc = $stm->fetchAll(PDO::FETCH_ASSOC);
                $dados_base_calc = filtro_base_calc($dados_base_calc[0]);

                // Carregar todos os dados de Eventos 
                /*
                $stm = $pdo->prepare(
                     "Select "
                    ."    e.* "
                    ."from REMUN_EVENTO_CALC_MES e "
                    ."where e.id_cliente  = :id_cliente  " 
                    ."  and e.id_servidor = :id_servidor "
                    ."  and e.ano_mes     = :ano_mes "
                );
                
                $stm->execute(array(
                    ':id_cliente'  => $unidade,
                    ':id_servidor' => $id_ser,
                    ':ano_mes'     => $nr_ano . $nr_mes 
                ));
                $dados_eventos_calc  = $stm->fetchAll(PDO::FETCH_ASSOC);
                $dados_eventos_calc  = filtro_evento_calc($dados_eventos_calc);
                $dados_vencimentos_v = montar_vencimentos($dados_eventos_calc, 'V');
                $dados_vencimentos_d = montar_vencimentos($dados_eventos_calc, 'D');
                */

                // Carregar todos os dados de Eventos (Vencimento V)
                $stm = $pdo->prepare(
                     "Select "
                    ."    e.* "
                    ."from REMUN_EVENTO_CALC_MES e "
                    ."where e.id_cliente  = :id_cliente  " 
                    ."  and e.id_servidor = :id_servidor "
                    ."  and e.ano_mes     = :ano_mes "
                    ."  and e.parcela     = :parcela "
                    ."  and e.tipo_evento = 'V' "
                    ."order by e.cod_evento "
                );
                
                $stm->execute(array(
                    ':id_cliente'  => $unidade,
                    ':id_servidor' => $id_ser,
                    ':ano_mes'     => $nr_ano . $nr_mes,
                    ':parcela'     => $nr_par
                ));
                $dados_vencimentos_v  = $stm->fetchAll(PDO::FETCH_ASSOC);
                $dados_vencimentos_v  = filtro_evento_calc($dados_vencimentos_v);
                
                // Carregar todos os dados de Eventos (Vencimento D)
                $stm = $pdo->prepare(
                     "Select "
                    ."    e.* "
                    ."from REMUN_EVENTO_CALC_MES e "
                    ."where e.id_cliente  = :id_cliente  " 
                    ."  and e.id_servidor = :id_servidor "
                    ."  and e.ano_mes     = :ano_mes "
                    ."  and e.parcela     = :parcela "
                    ."  and e.tipo_evento = 'D' "
                    ."order by e.cod_evento "
                );
                
                $stm->execute(array(
                    ':id_cliente'  => $unidade,
                    ':id_servidor' => $id_ser,
                    ':ano_mes'     => $nr_ano . $nr_mes,
                    ':parcela'     => $nr_par
                ));
                $dados_vencimentos_d  = $stm->fetchAll(PDO::FETCH_ASSOC); 
                $dados_vencimentos_d  = filtro_evento_calc($dados_vencimentos_d);
                
                $dados_vencimentos = juntar_vencimentos($dados_vencimentos_v, $dados_vencimentos_d);
                
                // Agrupar dados
                $dados = array_merge($dados_cliente, $dados_servidor, $dados_competencia, $dados_base_calc, $dados_vencimentos);
            } catch (Exception $ex) {
                echo "<p>" . $ex . "<br><br>" . $ex->getMessage() . "</p>";
            }
        ?>
        <?php
            //echo $arquivo_tpt;
            echo converteDados($dados, $arquivo_tpt);
        ?>
    </body>
</html>
<?php
    error_reporting(0);
    $html = ob_get_clean(); 
    /**
     *  Função ob_get_clean obtém conteúdo que está no buffer
     *  e exclui o buffer de saída atual.
     *  http://br1.php.net/manual/pt_BR/function.ob-get-clean.php 
     */
    /*
    $pdf  = new DOMPDF();
    
    $pdf->set_paper("A4", "portrait"); // Altera o papel para modo retrato(portrait) ou paisagem (landscape)
    $pdf->load_html($html);
    $pdf->render();
    $pdf->stream("Contra_Cheque_{$id_ser}{$nr_ano}{$nr_mes}_{$md5_unidade}.pdf");
    */ 
    $filename = "CCH_{$id_ser}{$nr_ano}{$nr_mes}{$nr_par}_{$md5_unidade}.pdf";
    $mpdf = new mPDF('A4');    
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->SetFooter('Página {PAGENO}/{nbpg}');
    //$stylesheet = file_get_contents('../lib/mpdf60/examples/mpdfstyleA4.css');
    //$mpdf->WriteHTML($stylesheet, 1);
    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, 'I'); 
?>