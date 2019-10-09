<!DOCTYPE html>
<?php
/*
Usuário para teste:
 * Cliente      : PRIFEITURA MINUCIPAL DE DOM ELISEU
 * Matrícula    :   10613
 * Senha        :   Aguiar866
*/
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
        $meses = ['No-Value','JANEIRO','FEVEREIRO','MARÇO','ABRIL','MAIO','JUNHO','JULHO','AGOSTO','SETEMBRO','OUTUBRO','NOVEMBRO','DEZEMBRO','DEC. TERC. 1º PARCELA','DEC. TERC. PARCELA FINAL','ABONO FUNDEB'];
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

        $dados['bc_mrg_consig']    = number_format( $dados['bc_mrg_consig'],   2,',','.' );
        $dados['val_mrg_consig']   = number_format( $dados['val_mrg_consig'],   2,',','.' );
        $dados['saldo_mrg_consig'] = number_format( $dados['saldo_mrg_consig'],   2,',','.' );
        
        return $dados;
    }

    function tirarAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/", "/(ç)/", "/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$string);
    }

    function filtro_evento_calc($dados) {
        $count = count($dados);
        for ($i = 0; $i < $count; $i++) {
            $dados[$i]['descricao'] = substr(strtoupper(trim(tirarAcentos($dados[$i]['descricao']))), 0, 35);
            $dados[$i]['descricao_observacao'] = substr(strtoupper(trim(tirarAcentos($dados[$i]['descricao_observacao']))), 0, 35);
            $dados[$i]['ref_qtd']   = number_format( $dados[$i]['ref_qtd'],2,',','' );
            $dados[$i]['valor']     = number_format( $dados[$i]['valor'],  2,',','.' );
        }
        return $dados;
    }
    
    function juntar_vencimentos($vencimentos_v, $vencimentos_d) {
        $strVencitos = ""; 

        $i = 0;
        $l = 0;
        
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
            @page {
                margin-top: 1.5cm;
                margin-bottom: 2.5cm;
                margin-left: 1cm;
                margin-right: 1cm;
            }
            .font_arial { 
                font-family: "Arial"; 
            }
            .font_serif { 
                font-family: Times, "Times New Roman", Georgia, serif; 
            }
            .font_sansserif { 
                font-family: Verdana, Arial, Helvetica, sans-serif; 
            }
            .font_monospace { 
                font-family: "Lucida Console", Courier, monospace; 
            }
            .font_cursive { 
                font-family: cursive; 
            }
            .font_fantasy { 
                font-family: fantasy; 
            }
            .fonteCourier { 
                font-family: "courier"; 
            }
            .fonteNegrito { 
                font-weight: bold; 
            }
            .fonteItalico { 
                font-style: italic;  
            }
            .fonteTamanho1 { 
                font-size: 1pt;  
            }
            .fonteTamanho3 { 
                font-size: 3pt;  
            }
            .fonteTamanho4 { 
                font-size: 4pt;  
            }
            .fonteTamanho4_5 { 
                font-size: 4.5pt;  
            }
            .fonteTamanho5 { 
                font-size: 5pt;  
            }
            .fonteTamanho6 { 
                font-size: 6pt;  
            }
            .fonteTamanho7 { 
                font-size: 7pt;  
            }
            .fonteTamanho8 { 
                font-size: 8pt;  
            }
            .fonteTamanho9 { 
                font-size: 9pt;  
            }
            .fonteTamanho10 { 
                font-size: 10pt;  
            }
            .fonteTamanhoValores { 
                font-size: 10pt;  
            }
            .textoAlinhadoDireita { 
                text-align: right; 
            }
            .textoAlinhadoEsquerda { 
                text-align: left; 
            }
            .textoAlinhadoAoTopo { 
                vertical-align: top; 
            }
            .textoVeriticalCentro { 
                vertical-align: central; 
            }
            .caixaTexto { 
                display:block; 
            }
            .espacoCelula1{
                padding: 1px;
            }
            .espacoCelula2{
                padding: 2px;
            }
            .espacoCelula3{
                padding: 3px;
            }
            .espacoCelula4{
                padding: 4px;
            }
            .espacoCelula5{
                padding: 5px;
            }
            .semBorda { 
                border:0px solid #000; 
            }
            .comBorda { 
                border:1px solid #000; 
            }
            .bordaADireita { 
                border-right:1px solid #000; 
            }
            .bordaAEsquerda { 
                border-left:1px solid #000; 
            }
            .bordaAcima { 
                border-top:1px solid #000; 
            }
            .bordaABaixo { 
                border-bottom:1px solid #000; 
            }
            .corFundo {
                background-color: #FFFFEE;
            }
            .corFundoCinza1 {
                background-color: #E5E1E1;
            }
            .corFundoCinza2 {
                background-color: #D9D5D5;
            }
            .corFundoCinza3 {
                background-color: #CEC8C8;
            }
            .corFundoCinza4 {
                background-color: #C5C0C0;
            }
            .corFundoCinza5 {
                background-color: #B5B3B3;
            }
            .larguraColuna140px {
                width: 140px;
            }
            .larguraColuna148px {
                width: 148px;
            }
            .larguraColuna150px {
                width: 150px;
            }
            .larguraColuna300px {
                width: 300px;
            }
            .larguraColuna10perc {
                width: 10%;
            }
            .larguraColuna15perc {
                width: 15%;
            }
            .larguraColuna30perc {
                width: 30%;
            }
            .larguraColuna50perc {
                width: 50%;
            }
        </style>
<!--        
        <style type="text/css">
            @media print {
                    .noPrint {
                            display:none;
                            width:29cm;
                            font-size:7pt;
                    }
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
                font-size:7pt;//6pt; 
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
                //background: #E1E1E1;
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
                width:5%; //3%;
                padding:2px 0; 
            }
            #valores .descricao { 
                background: #E1E1E1;
                width:55%;//52%;
                padding:2px; 
            }
            #valores .quantidade { 
                background: #E1E1E1;
                width:6%;//6%;
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
                //padding:0.4%; 
                padding:5px;
            }
            #imposto .baseCalc { 
                width:20%;
                //padding:0.4%; 
                padding:5px;
            }
            #imposto .baseIrrf { 
                width:20%;
                //padding:0.4%; 
                padding:5px;
            }
            #imposto .dependIrrf { 
                width:20%;
                //padding:0.4%; 
                padding:5px;
            }
            #imposto .deducao { 
                width:20%;
                //padding:0.4%; 
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
        </style>
        -->
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
                     "Select     "
                    ."    u.id   "
                    ."  , u.nome "
                    ."  , u.cnpj "
                    ."  , u.municipio_nome "
                    ."  , u.municipio_uf   "
                    ."  , coalesce(u.margem_consignavel, 0) as margem_consignavel "
                    ."  , coalesce(nullif(trim(u.logo), ''), '../dist/img/brasoes/ssbv.png') as brasao "    
                    ."  , trim(coalesce(u.titulo_portal, u.nome)) as titulo_portal "
                    ."  , coalesce(u.ender_lograd, '...') as endereco "
                    ."  , coalesce(u.ender_num,    '...') as numero "
                    ."  , coalesce(u.ender_bairro, '...') as bairro "
                    ."  , coalesce(u.ender_cep,    '00000000') as cep " 
                    ."from ADM_CLIENTE u "
                    ."order by "
                    ."    trim(coalesce(u.titulo_portal, u.nome))";

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
                        $dados_cliente['BRASAO-TAM'] = $brasao_tama;
                        $dados_cliente['NOME-CLI']   = $obj->nome;
                        $dados_cliente['CNPJ']       = formatarTexto('##.###.###/####-##', $obj->cnpj);
                        $dados_cliente['ENDER_LOGRAD-CLI']   = $obj->endereco;
                        $dados_cliente['ENDER_NUM-CLI']      = $obj->numero;
                        $dados_cliente['ENDER_BAIRRO-CLI']   = $obj->bairro;
                        $dados_cliente['ENDER_CEP-CLI']      = formatarTexto('##.###-###', $obj->cep);
                        $dados_cliente['MUNICIPIO_NOME']     = $obj->municipio_nome;
                        $dados_cliente['MUNICIPIO_UF']       = $obj->municipio_uf;
                        $dados_cliente['MARGEM_CONSIGNAVEL'] = $obj->margem_consignavel;
                        $dados_cliente['EXIBIR_MATRICULA']   = "1";
                        
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
                    ."  , coalesce(o.descricao, '...') as desc_cargo_origem "
                    ."  , coalesce(a.descricao, '...') as desc_cargo_atual "
                    ."from REMUN_BASE_CALC_MES c "
                    ."  left join REMUN_CARGO_FUNCAO o on (o.id_cliente = c.id_cliente and o.id_cargo = c.id_cargo_origem) "
                    ."  left join REMUN_CARGO_FUNCAO a on (a.id_cliente = c.id_cliente and a.id_cargo = c.id_cargo_atual) "
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

                // Carregar todos os dados de Eventos (Vencimento V)
                $stm = $pdo->prepare(
                     "Select "
                    ."    e.* "
                    ."  , trim(coalesce(e.descricao, '') || ' ' || coalesce(e.observacao, '')) as descricao_observacao "
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
                    ."  , trim(coalesce(e.descricao, '') || ' ' || coalesce(e.observacao, '')) as descricao_observacao "
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
        
        <div>
            <table class="comBorda fonteTamanho7" cellpadding="3" cellspacing="0" width="100%">
                <tr>
                    <td class="bordaADireita" rowspan="3" align="center" width="100">
                        <img src="<?php echo $dados_cliente['BRASAO-CLI'];?>" height="70" alt="Brasão da Unidade/Órgão"/> <!-- <?php // echo $dados_cliente['BRASAO-TAM'];?> -->
                    </td>
                    <td class="fonteNegrito fonteItalico textoAlinhadoDireita">
                        <span>DEMONSTRATIVO DE PAGAMENTO DE SALÁRIO (CONTRA-CHEQUE)</span><br>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="fonteNegrito"><?php echo $dados_cliente['NOME-CLI'];?></span><br>
                        <span class="fonteNegrito"><?php echo $dados_cliente['ENDER_LOGRAD-CLI'];?>, <?php echo $dados_cliente['ENDER_NUM-CLI'];?>, <?php echo $dados_cliente['ENDER_BAIRRO-CLI'];?></span><br>
                        <span class="fonteNegrito"><?php echo $dados_cliente['MUNICIPIO_NOME'];?> - CEP: <?php echo $dados_cliente['ENDER_CEP-CLI'];?> - <?php echo $dados_cliente['MUNICIPIO_UF'];?></span><br>
                        <span class="fonteNegrito">CNPJ: <?php echo $dados_cliente['CNPJ'];?></span><br>
                    </td>
                </tr>
                <tr>
                    <td class="fonteNegrito fonteItalico textoAlinhadoDireita">
                        <span><?php echo $dados_competencia['ANO_MESEXT'];?></span>
                    </td>
                </tr>
            </table>
            
            <p class="fonteTamanho3"></p>
            
            <table class="comBorda fonteTamanho7 textoAlinhadoAoTopo" cellpadding="3" cellspacing="0"  width="100%">
                <tr>
                    <!--COLUNA 1-->
                    <?php if (intval($dados_cliente['EXIBIR_MATRICULA']) === 1):?>
                    <td class="espacoCelula3"><span class="fonteTamanho5">ID</span><br><b><?php echo $dados['id_servidor'];?></b></td>
                    <?php else:?>
                    <td class="espacoCelula3"><span class="fonteTamanho5">MATRICULA</span><br><b><?php echo $dados['id_servidor'];?></b>/td>
                    <?php endif;?>
                    
                    <!--COLUNA 2-->
                    <td class="espacoCelula3" style="width: 40%;"><span class="fonteTamanho5">NOME DO SERVIDOR</span><br><b><?php echo $dados['nome'];?></b></td>
                    <!--COLUNA 3-->
                    <td class="espacoCelula3"><span class="fonteTamanho5">ADMISSÃO</span><br><b><?php echo $dados['dt_admissao'];?></b></td>
                    
                    <!--COLUNA 4-->
                    <?php if (trim($dados['desc_cargo_origem']) !== trim($dados['desc_cargo_atual'])): ?>
                    <td class="espacoCelula3" colspan="3"><span class="fonteTamanho5">CARGO/FUNÇÃO ORIGEM</span><br><b><?php echo $dados['id_cargo_origem'];?> - <?php echo $dados['desc_cargo_origem'];?></b></td>
                    <?php else:?>
                    <td class="espacoCelula3" colspan="3">&nbsp;</td>
                    <?php endif;?>
                    
                    <!--COLUNA 5-->
                    <?php if (intval($dados_cliente['EXIBIR_MATRICULA']) === 1):?>
                    <td class="espacoCelula3"><span class="fonteTamanho5">MATRÍCULA</span><br><b><?php echo $dados['matricula'];?></b></td>
                    <?php else:?>
                    <td class="espacoCelula3">&nbsp;</td>
                    <?php endif;?>
                </tr>
                <tr>
                    <td class="espacoCelula3" colspan="4"><span class="fonteTamanho5">SUB-UNID. ORÇAMENTÁRIA (CENTRO DE CUSTO)</span><br><b><?php echo $dados['id_sub_unid_orcam'];?> - <?php echo $dados['descr_sub_unid_orcam'];?></b></td>
                    <td class="espacoCelula3" colspan="3"><span class="fonteTamanho5">CARGO/FUNÇÃO ATUAL</span><br><b><?php echo $dados['id_cargo_atual'] . " - " . $dados['desc_cargo_atual'];?></b></td>
                </tr>
                <tr>
                    <td class="espacoCelula3" colspan="3"><span class="fonteTamanho5">UNID. DE LOTAÇÃO (LOCAL DE TRABALHO)</span><br><b><?php echo $dados['id_unid_lotacao'];?> - <?php echo $dados['descr_unid_lotacao'];?></b></td>
                    <td class="espacoCelula3"><span class="fonteTamanho5">CPF</span><br><b><?php echo $dados['cpf'];?></b></td>
                    <td class="espacoCelula3"><span class="fonteTamanho5">RG</span><br><b><?php echo $dados['rg'];?></b></td>
                    <td class="espacoCelula3"><span class="fonteTamanho5">PIS/PASEP</span><br><b><?php echo $dados['pis_pasep'];?></b></td>
                    <td class="espacoCelula3"><span class="fonteTamanho5"><?php echo strtoupper($dados['descr_situac_tcm']);?></span><br><b><?php echo $dados['descr_est_funcional'];?></b></td>
                </tr>
            </table>

            <p class="fonteTamanho3"></p>
            
            <table class="semBorda fonteTamanho7 textoAlinhadoAoTopo" cellpadding="3" cellspacing="0" width="100%">
                <tr>
                    <td colspan="4" class="larguraColuna50perc bordaAcima bordaABaixo bordaAEsquerda bordaADireita corFundoCinza3">
                        <center><span class="fonteTamanho7 fonteNegrito fonteItalico">VENCIMENTOS / PROVENTOS</span></center>
                    </td>
                    <td colspan="4" class="larguraColuna50perc bordaAcima bordaABaixo bordaADireita corFundoCinza3">
                        <center><span class="fonteTamanho7 fonteNegrito fonteItalico">DESCONTOS</span></center>
                    </td>
                </tr>
                <tr>
                    <td class="bordaAEsquerda bordaADireita corFundoCinza1 fonteTamanho7">CÓD.</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7 larguraColuna30perc">DESCRIÇÃO DO EVENTO</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7 textoAlinhadoDireita">QUANT.</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7 textoAlinhadoDireita larguraColuna10perc">VALOR (R$)</td>
                    
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7">CÓD.</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7 larguraColuna30perc">DESCRIÇÃO DO EVENTO</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7 textoAlinhadoDireita">QUANT.</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7 textoAlinhadoDireita larguraColuna10perc">VALOR (R$)</td>
                </tr>
                <?php for ($i = 0; $i < 15; $i++): ?>
                <tr>
                    <td class="bordaAEsquerda bordaADireita"><?php echo (!empty($dados_vencimentos_v[$i])?$dados_vencimentos_v[$i]['cod_evento']:"&nbsp;");?></td>
                    <td class="bordaADireita larguraColuna30perc"><?php echo (!empty($dados_vencimentos_v[$i])?$dados_vencimentos_v[$i]['descricao_observacao']:"&nbsp;");?></td>
                    <td class="bordaADireita textoAlinhadoDireita"><?php echo (!empty($dados_vencimentos_v[$i])?$dados_vencimentos_v[$i]['ref_qtd']:"&nbsp;");?></td>
                    <td class="bordaADireita textoAlinhadoDireita larguraColuna10perc"><?php echo (!empty($dados_vencimentos_v[$i])?$dados_vencimentos_v[$i]['valor']:"&nbsp;");?></td>
                    
                    <td class="bordaADireita"><?php echo (!empty($dados_vencimentos_d[$i])?$dados_vencimentos_d[$i]['cod_evento']:"&nbsp;");?></td>
                    <td class="bordaADireita larguraColuna30perc"><?php echo (!empty($dados_vencimentos_d[$i])?$dados_vencimentos_d[$i]['descricao_observacao']:"&nbsp;");?></td>
                    <td class="bordaADireita textoAlinhadoDireita"><?php echo (!empty($dados_vencimentos_d[$i])?$dados_vencimentos_d[$i]['ref_qtd']:"&nbsp;");?></td>
                    <td class="bordaADireita textoAlinhadoDireita larguraColuna10perc"><?php echo (!empty($dados_vencimentos_d[$i])?$dados_vencimentos_d[$i]['valor']:"&nbsp;");?></td>
                </tr>
                <?php endfor;?>
                <tr>
                    <td colspan="3" class="bordaAEsquerda corFundoCinza1 fonteNegrito fonteTamanho7 textoVeriticalCentro textoAlinhadoDireita">TOTAL &nbsp;</td>
                    <td class="bordaADireita corFundoCinza1 fonteNegrito textoAlinhadoDireita"><?php echo $dados_base_calc['tot_venctos'];?></td>
                    <td colspan="3" class="corFundoCinza1 fonteNegrito fonteTamanho7 textoVeriticalCentro textoAlinhadoDireita">TOTAL &nbsp;</td>
                    <td class="bordaADireita corFundoCinza1 fonteNegrito textoAlinhadoDireita"><?php echo $dados_base_calc['tot_descontos'];?></td>
                </tr>
                <tr>
                    <td colspan="3" class="bordaAEsquerda bordaABaixo corFundoCinza3 fonteNegrito fonteTamanho7 textoVeriticalCentro textoAlinhadoDireita">SALÁRIO LÍQUIDO &nbsp;</td>
                    <td class="bordaADireita bordaABaixo corFundoCinza3 fonteNegrito textoAlinhadoDireita"><?php echo $dados_base_calc['sal_liquido'];?></td>
                    <td colspan="4" class="bordaADireita bordaABaixo corFundoCinza3 "></td>
                </tr>
            </table>
            
<!--
            <table class="semBorda fonteTamanho10 textoAlinhadoAoTopo" cellpadding="3" cellspacing="0"  width="100%">
                <tr>
                    <td width="49%" colspan="4" class="comBorda corFundoCinza3">
                        <span class="fonteTamanho7 fonteNegrito fonteItalico">VENCIMENTOS / PROVENTOS</span>
                    </td>
                    <td class="semBorda fonteTamanho3">&nbsp;</td>
                    <td width="49%" colspan="4" class="comBorda corFundoCinza3">
                        <span class="fonteTamanho7 fonteNegrito fonteItalico">DESCONTOS</span>
                    </td>
                </tr>
                <tr>
                    <td class="bordaAEsquerda bordaADireita corFundoCinza1 fonteTamanho7">CÓD.</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7">DESCRIÇÃO DO EVENTO</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7 textoAlinhadoDireita">QUANT.</td>
                    <td class="bordaADireita corFundoCinza1  fonteTamanho7 textoAlinhadoDireita">VALOR (R$)</td>
                    <td class="semBorda fonteTamanho3">&nbsp;</td>
                    <td class="bordaAEsquerda bordaADireita corFundoCinza1 fonteTamanho7">CÓD.</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7">DESCRIÇÃO DO EVENTO</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7 textoAlinhadoDireita">QUANT.</td>
                    <td class="bordaADireita corFundoCinza1 fonteTamanho7 textoAlinhadoDireita">VALOR (R$)</td>
                </tr>
                <?php // for ($i = 0; $i < 10; $i++): ?>
                <tr>
                    <td class="bordaAEsquerda bordaADireita"><?php // echo (!empty($dados_vencimentos_v[$i])?$dados_vencimentos_v[$i]['cod_evento']:"&nbsp;");?></td>
                    <td class="bordaADireita"><?php // echo (!empty($dados_vencimentos_v[$i])?$dados_vencimentos_v[$i]['descricao']:"&nbsp;");?></td>
                    <td class="bordaADireita textoAlinhadoDireita"><?php // echo (!empty($dados_vencimentos_v[$i])?$dados_vencimentos_v[$i]['ref_qtd']:"&nbsp;");?></td>
                    <td class="bordaADireita textoAlinhadoDireita"><?php // echo (!empty($dados_vencimentos_v[$i])?$dados_vencimentos_v[$i]['valor']:"&nbsp;");?></td>
                    <td class="semBorda fonteTamanho3">&nbsp;</td>
                    <td class="bordaAEsquerda bordaADireita"><?php // echo (!empty($dados_vencimentos_d[$i])?$dados_vencimentos_d[$i]['cod_evento']:"&nbsp;");?></td>
                    <td class="bordaADireita"><?php // echo (!empty($dados_vencimentos_d[$i])?$dados_vencimentos_d[$i]['descricao']:"&nbsp;");?></td>
                    <td class="bordaADireita textoAlinhadoDireita"><?php // echo (!empty($dados_vencimentos_d[$i])?$dados_vencimentos_d[$i]['ref_qtd']:"&nbsp;");?></td>
                    <td class="bordaADireita textoAlinhadoDireita"><?php // echo (!empty($dados_vencimentos_d[$i])?$dados_vencimentos_d[$i]['valor']:"&nbsp;");?></td>
                </tr>
                <?php // endfor;?>
                <tr>
                    <td colspan="2" class="bordaAEsquerda corFundoCinza1 fonteNegrito fonteTamanho7 textoVeriticalCentro">TOTAL</td>
                    <td colspan="2" class="bordaADireita corFundoCinza1 fonteNegrito textoAlinhadoDireita"><?php // echo $dados_base_calc['tot_venctos'];?></td>
                    <td class="semBorda fonteTamanho3">&nbsp;</td>
                    <td colspan="2" class="bordaAEsquerda corFundoCinza1 fonteNegrito fonteTamanho7 textoVeriticalCentro">TOTAL</td>
                    <td colspan="2" class="bordaADireita corFundoCinza1 fonteNegrito textoAlinhadoDireita"><?php // echo $dados_base_calc['tot_descontos'];?></td>
                </tr>
                <tr>
                    <td colspan="2" class="bordaAEsquerda bordaABaixo corFundoCinza3 fonteNegrito fonteTamanho7 textoVeriticalCentro">SALÁRIO LÍQUIDO</td>
                    <td colspan="2" class="bordaADireita bordaABaixo corFundoCinza3 fonteNegrito textoAlinhadoDireita">R$ <?php // echo $dados_base_calc['sal_liquido'];?></td>
                    <td class="semBorda fonteTamanho3">&nbsp;</td>
                    <td colspan="4" class="bordaAEsquerda bordaADireita bordaABaixo corFundoCinza3 fonteNegrito fonteTamanho7 textoVeriticalCentro">DEPTO: <?php // echo $dados['descr_depto'];?></td>
                </tr>
            </table>
-->
            <p class="fonteTamanho3"></p>

            <table class="comBorda fonteTamanho7 textoAlinhadoAoTopo" cellpadding="3" cellspacing="0"  width="100%">
                <tr>
                    <td class="espacoCelula3 textoAlinhadoDireita"><span class="fonteTamanho5">VENCTO BASE (<?php echo $dados['tipo_sal'];?>)</span><br><b><?php echo $dados['vencto_base_cargo'];?></b></td>
                    <td class="espacoCelula3 textoAlinhadoDireita"><span class="fonteTamanho5">BASE CALC. PREVID.</span><br><b><?php echo $dados['bc_previd'];?></b></td>
                    <td class="espacoCelula3 textoAlinhadoDireita"><span class="fonteTamanho5">BASE CALC. IRRF</span><br><b><?php echo $dados['bc_irrf'];?></b></td>
                    <td class="espacoCelula3 textoAlinhadoDireita"><span class="fonteTamanho5">DEPEND. IRRF</span><br><b><?php echo $dados['qtd_depend_irrf'];?></b></td>
                    <td class="espacoCelula3 textoAlinhadoDireita"><span class="fonteTamanho5">DEDUÇÕES</span><br><b><?php echo $dados['tot_deduc_depend'];?></b></td>
                    <?php if (intval($dados_cliente['MARGEM_CONSIGNAVEL']) === 1):?>
                    <td class="espacoCelula3 textoAlinhadoDireita"><span class="fonteTamanho5">BC. CONSIGNÁVEL</span><br><b><?php echo $dados['bc_mrg_consig'];?></b></td>
                    <td class="espacoCelula3 textoAlinhadoDireita"><span class="fonteTamanho5">VALOR CONSIGNÁVEL</span><br><b><?php echo $dados['val_mrg_consig'];?></b></td>
                    <td class="espacoCelula3 textoAlinhadoDireita"><span class="fonteTamanho5">SALDO CONSIGNÁVEL</span><br><b><?php echo $dados['saldo_mrg_consig'];?></b></td>
                    <?php else:?>
                    <td class="espacoCelula3 textoAlinhadoDireita">&nbsp;</td>
                    <td class="espacoCelula3 textoAlinhadoDireita">&nbsp;</td>
                    <td class="espacoCelula3 textoAlinhadoDireita">&nbsp;</td>
                    <?php endif;?>
                    <td class="espacoCelula3 textoAlinhadoDireita">&nbsp;</td>
                    <td class="espacoCelula3 textoAlinhadoDireita">&nbsp;</td>
                </tr>
            </table>
            
            <p class="fonteTamanho3"></p>

            <table class="comBorda fonteTamanho7 textoAlinhadoAoTopo" cellpadding="3" cellspacing="0"  width="100%">
                <tr>
                    <td class="espacoCelula3"><span class="fonteTamanho5 fonteNegrito">DEPTO:</span><br><p><?php echo $dados['descr_depto'];?></p></td>
                </tr>
                <tr>
                    <td class="espacoCelula3"><span class="fonteTamanho5 fonteNegrito">PAGAMENTO/QUITAÇÃO:</span><br><p><?php echo $dados['pagto_quitacao'];?></p></td>
                </tr>
                <tr>
                    <td class="espacoCelula3"><span class="fonteTamanho5 fonteNegrito">MENSAGEM:</span><br><p><?php echo $dados['mensagem_cc'];?></p></td>
                </tr>
                <tr>
                    <td class="espacoCelula3"><p><?php echo $dados['felicit_niver'];?></p></td>
                </tr>
            </table>
        </div>
    </body>
</html>
<?php
    ini_set("display_errors", 0);
    $html = ob_get_clean(); 

    $filename = "CCH_{$id_ser}{$nr_ano}{$nr_mes}{$nr_par}_{$md5_unidade}.pdf";
    $mpdf = new mPDF('A4');    
    //$mpdf = new mPDF('utf-8', 'A4-L');
    $mpdf->SetDisplayMode('fullpage');

    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, 'I'); 
?>;