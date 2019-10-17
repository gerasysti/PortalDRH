<!DOCTYPE html>
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 
 Exemplos de SIMPLE TOUR
 1. https://alvaroveliz.github.io/aSimpleTour/
 2. https://linkedin.github.io/hopscotch/
 3. https://clu3.github.io/bootstro.js/#
 4. https://tracelytics.github.io/pageguide/
 
 */
    require_once './lib/classes/configuracao.php';
    require_once './lib/Constantes.php';
    require_once './lib/funcoes.php';

    session_start();
    session_unset();
    session_destroy();
    
    $unidade     = "0";
    $md5_unidade = md5("0000000");
    $des_unidade = "Título da Unidade no Portal";
    $inf_unidade = "Informações da Unidade";
    
    $exibe_lista_servidor = 0;
    
    if (isset($_REQUEST['un'])) {
        $md5_unidade = trim($_REQUEST['un']); 
    } else {
        header('location: ./index.php');
        exit;
    }
    
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
        ."  , coalesce(u.exibe_lista, '0') as exibe_lista_servidor "
        ."  , trim(coalesce(nullif(trim(u.titulo_portal), ''), trim(u.nome))) as titulo_portal "
        ."  , coalesce(u.ender_lograd, '...') as endereco "
        ."  , coalesce(u.ender_num,    '...') as numero "
        ."  , coalesce(u.ender_bairro, '...') as bairro "
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
            $exibe_lista_servidor = (int)$obj->exibe_lista_servidor;
            break;
        }
    }
    
    if ($unidade === "0") {
        header('location: ./index.php');
        exit;
    }
    
    $sql = 
          "Select "
        . "    count(s.id_servidor) as qt_cadastro "
        . "  , sum(case when s.situacao = 1 then 1 else 0 end) as qt_servidores "
        . "from REMUN_SERVIDOR s "
        . "where (s.id_cliente = {$unidade})";
        
    $qry = $pdo->query($sql);    
    $dados    = $qry->fetchAll(PDO::FETCH_ASSOC);
    $cadastro = null;
    foreach($dados as $item) {
        $cadastro = $item;
    }
    
    $sql = 
          "Select "
        . "    x.ano_mes "
        . "  , substring(x.ano_mes from 5 for 2) || '/' || substring(x.ano_mes from 1 for 4) as ds_competencia "
        . "  , count( s.ano_mes ) as qt_registros "
        . "  , sum( case when coalesce(s.id_est_funcional, 0) = 1 then 1 else 0 end ) as qt_servidores "
        . "  , sum( s.tot_venctos )   as tot_venctos "
        . "  , sum( s.tot_descontos ) as tot_descontos "
        . "  , sum( s.sal_liquido )   as tot_salarios "
        . "from ( "
        . "    Select "
        . "        x.parcela "
        . "      , x.id_cliente "
        . "      , max(x.ano_mes) as ano_mes "
        . "    from REMUN_BASE_CALC_MES x "
        . "    where (x.id_cliente = {$unidade}) "
        . "    group by "
        . "        x.parcela "
        . "      , x.id_cliente "
        . ") x "
        . "  inner join REMUN_BASE_CALC_MES s on (s.id_cliente = x.id_cliente and s.ano_mes = x.ano_mes and s.parcela = x.parcela) "
        . "group by "
        . "    x.ano_mes ";
        
    $qry = $pdo->query($sql);    
    $dados   = $qry->fetchAll(PDO::FETCH_ASSOC);
    $valores = null;
    $ano_mes = date('Ym');
    $ano     = substr($ano_mes, 0, 4);
    foreach($dados as $item) {
        $valores = $item;
        $ano_mes = $valores['ano_mes'];
    }
    
    $sql = 
          "Select first 6 "
        . "  y.* "
        . "from ( "
        . "    Select "
        . "      x.* "
        . "    from ( "
        . "        Select first 6 "
        . "            s.ano_mes "
        . "          , substring(s.ano_mes from 5 for 2) || '/' || substring(s.ano_mes from 1 for 4) as ds_competencia "
        . "          , count( s.ano_mes ) as qt_registros "
        . "          , sum( case when coalesce(s.id_est_funcional, 0) = 1 then 1 else 0 end ) as qt_servidores "
        . "          , (sum( s.tot_venctos )   / 1000000.0) as tot_venctos "
        . "          , (sum( s.tot_descontos ) / 1000000.0) as tot_descontos "
        . "          , (sum( s.sal_liquido )   / 1000000.0) as tot_salarios "
        . "        from REMUN_BASE_CALC_MES s "
        . "        where (s.id_cliente = {$unidade}) "
        . "          and (substring(s.ano_mes from 1 for 4) = '{$ano}') "
        . "          and (s.ano_mes < '{$ano_mes}') "
        . "        group by "
        . "            s.ano_mes "
        . "        order by "
        . "          s.ano_mes DESC "
        . "    ) x "
        . "    "
        . "    Union "
        . "    "
        . "    Select '{$ano}94', null, 0, 0, 0.0, 0.0, 0.0 from VW_INFORMACOES union "
        . "    Select '{$ano}95', null, 0, 0, 0.0, 0.0, 0.0 from VW_INFORMACOES union "
        . "    Select '{$ano}96', null, 0, 0, 0.0, 0.0, 0.0 from VW_INFORMACOES union "
        . "    Select '{$ano}97', null, 0, 0, 0.0, 0.0, 0.0 from VW_INFORMACOES union "
        . "    Select '{$ano}98', null, 0, 0, 0.0, 0.0, 0.0 from VW_INFORMACOES union "
        . "    Select '{$ano}99', null, 0, 0, 0.0, 0.0, 0.0 from VW_INFORMACOES "
        . ") y          "
        . "order by     "
        . "  y.ano_mes  ";
        
    $qry = $pdo->query($sql);    
    $dados_grafico_sparkline = $qry->fetchAll(PDO::FETCH_ASSOC);
    
    unset($res);
    unset($qry);
    unset($dados);
    
    $titulo_pagina = removerAcentos($des_unidade);
?>
<html ng-app="monarchApp" lang="en">
    <head>
        <style>
            /* Loading Spinner */
            .spinner{
                margin:0;
                width:70px;
                height:18px;
                margin:-35px 0 0 -9px;
                position:absolute;
                top:50%;
                left:50%;
                text-align:center
            }
            .spinner > div{
                width:18px;
                height:18px;
                background-color:#333;
                border-radius:100%;
                display:inline-block;
                -webkit-animation:bouncedelay 1.4s infinite ease-in-out;
                animation:bouncedelay 1.4s infinite ease-in-out;
                -webkit-animation-fill-mode:both;
                animation-fill-mode:both
            }
            .spinner .bounce1{
                -webkit-animation-delay:-.32s;
                animation-delay:-.32s
            }
            .spinner .bounce2{
                -webkit-animation-delay:-.16s;
                animation-delay:-.16s
            }
            @-webkit-keyframes bouncedelay{
                0%,80%,100%{
                    -webkit-transform:scale(0.0)
                }40%{
                    -webkit-transform:scale(1.0)
                }
            }
            @keyframes bouncedelay{
                0%,80%,100%{
                    transform:scale(0.0);
                    -webkit-transform:scale(0.0)
                }40%{
                    transform:scale(1.0);
                    -webkit-transform:scale(1.0)
                }
            }
            
            .loading-spinner, .loading-stick {
                float: left;
                margin-top: 5px;
            }    
            
            .td_align_right  { text-align: right; }
            .td_align_left   { text-align: left; }
            .td_align_center { text-align: center; }
            .td_align_justify{ text-align: justify; }
            
            .centralizar-vertical {
                vertical-align: middle;
            }
            .custom-font-size-10 {
                font-size: 10px;
            }
            .custom-font-size-12 {
                font-size: 12px;
            }
            /* Centralizar na verticação as células de tabelas renderizadas pela classe "dataTable()"*/
            table.dataTable tbody td {
                vertical-align: middle;
            }            
        </style>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <title>Remuneratu$ | DRH Transparência | <?php echo $titulo_pagina;?></title>

        <?php
            include './page_head_links.php';
        ?>
   </head>

    <body ng-controller="indexController">

        <div id="page-wrapper">
            <div id="page-header" class="bg-gradient-9">
                <input type="hidden" id="id_cliente" value="<?php echo $unidade;?>">
                <div id="mobile-navigation">
                    <button id="nav-toggle" class="collapsed" data-toggle="collapse" data-target="#page-sidebar"><span></span></button>
                    <a href="principal.php?un=<?php echo $md5_unidade;?>" class="logo-content-small" title="Controle de Remunerações"></a>
                    <!--<a href="#" class="logo-content-small" title="Controle de Remunerações" onclick="home()"></a>-->
                </div>
                
                <div id="header-logo" class="logo-bg">
                    <a href="principal.php?un=<?php echo $md5_unidade;?>" class="logo-content-big" title="Controle de Remunerações">
                    <!--<a href="#" class="logo-content-big" title="Controle de Remunerações" onclick="home()">-->    
                        Remuneratu<i>$</i>
                        <span>A solução perfeita para controle da Folha</span>
                    </a>
                    <a href="principal.php?un=<?php echo $md5_unidade;?>" class="logo-content-small" title="Controle de Remunerações">
                    <!--<a href="#" class="logo-content-small" title="Controle de Remunerações" onclick="home()">-->
                        Remuneratu<i>$</i>
                        <span>A solução perfeita para controle da Folha</span>
                    </a>
                    <a id="close-sidebar" href="#" title="Exibir/Ocultar menu vertical">
                        <i class="glyph-icon icon-angle-left"></i>
                    </a>
                </div>
                
                <div id="header-nav-right">
                    <a href="#" class="hdr-btn" id="fullscreen-btn" title="Tela Cheia">
                        <i class="glyph-icon icon-arrows-alt"></i>
                    </a>
                    <a class="header-btn" id="logout-btn" href="index.php" title="Sair">
                        <i class="glyph-icon icon-power-off"></i>
                    </a>
                </div><!-- #header-nav-right -->

            </div>
            
            <div id="page-sidebar">
                <div class="scroll-sidebar no-print">

                    <ul id="sidebar-menu">
<!--                        <li class="header"><span>Administração</span></li>
                        <li>
                            <a href="#index" title="Cadastro de Clientes">
                                <i class="glyph-icon icon-linecons-tv"></i>
                                <span>Cadastro de Clientes</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        -->
                        <li class="header"><span><?php echo $unidade . " - " . $des_unidade;?></span></li>
                        <li>
                            <a href="javascript:void(0);" title="Página Inicial" onclick="home()">
                                <i class="glyph-icon icon-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" title="Lei de Acesso a Informação" onclick="lei_acesso_informacao()">
                                <i class="glyph-icon icon-legal"></i>
                                <span>Lei de Acesso a Informação</span>
                            </a>
                        </li>
                        <li>
                            <a href="login.php?un=<?php echo $md5_unidade;?>" title="Área Exclusiva do Servidor">
                                <i class="glyph-icon icon-users"></i>
                                <span>Área Exclusiva do Servidor</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="javascript:void(0);" title="Tabela de Cargos/Funções e Salários" onclick="cargos_salarios('<?php echo 'unidade_' . $unidade?>')">
                                <i class="glyph-icon icon-usd"></i>
                                <span>Tabela de Cargos e Salários</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" title="Despesas com Folha de Pagamento">
                                <i class="glyph-icon icon-money"></i>
                                <span>Despesas com Folha</span>
                            </a>
                            <div class="sidebar-submenu">

                                <ul>
                                    <li><a href="javascript:void(0);" title="Vínculos" onclick="vinculos('<?php echo 'unidade_' . $unidade?>')"><span>Por Vínculos</span></a></li>
                                    <li><a href="javascript:void(0);" title="Cargos / Funções" onclick="cargos('<?php echo 'unidade_' . $unidade?>')"><span>Por Cargos / Funções</span></a></li>
                                    <?php if ($exibe_lista_servidor === 1): ?>
                                    <li><a href="javascript:void(0);" title="Servidores" onclick="servidores('<?php echo 'unidade_' . $unidade?>')"><span>Por Servidores</span></a></li>
                                    <?php // else: ?>
                                    <?php endif; ?>
                                </ul>

                            </div><!-- .sidebar-submenu -->
                        </li>
                        <li>
                            <a href="index.php" title="Página Inicial">
                                <i class="glyph-icon icon-power-off"></i>
                                <span>Sair</span>
                            </a>
                        </li>
                    </ul><!-- #sidebar-menu -->
                </div>
            </div>

            <div id="descktop">
                <div id="page-content">
                    <div class="col-md-12">
                        <div id="page-title">
                            <h2><strong><?php echo $des_unidade;?></strong></h2>
                            <p><strong><?php echo $inf_unidade;?></strong></p>
                        </div>

                        <div class='panel ng-scope'>
                            <div class='panel-body'>
                                <h3 class='title-hero'>Informações</h3>
                                <div class='example-box-wrapper'>
                                    <div class='row'>
                                        <div class='col-md-4'>
                                            <div class='tile-box bg-default'>
                                                <div class='tile-header'>
                                                    Cadastro de Servidores
                                                    <div class='float-right'>
                                                        <!--<i class='glyph-icon icon-calendar'></i>-->
                                                    </div>
                                                </div>
                                                <div class='tile-content-wrapper'>
                                                    <i class='glyph-icon icon-edit'></i>
                                                    <div class='tile-content'>
                                                        <span></span> <?php echo ($cadastro === null?'...':number_format($cadastro['qt_cadastro'], 0, ',', '.'));?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='col-md-4'>
                                            <div class='tile-box bg-blue'>
                                                <div class='tile-header'>
                                                    Servidores Ativos
                                                    <div class='float-right'>
                                                        <!--<i class='glyph-icon icon-calendar'></i>-->
                                                    </div>
                                                </div>
                                                <div class='tile-content-wrapper'>
                                                    <i class='glyph-icon icon-users'></i>
                                                    <div class='tile-content'>
                                                        <span></span> <?php echo ($cadastro === null?'...':number_format($cadastro['qt_servidores'], 0, ',', '.'));?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='col-md-4'>
                                            <div class='tile-box bg-aqua'>
                                                <div class='tile-header'>
                                                    Registros de Pagamentos
                                                    <div class='float-right'>
                                                        <i class='glyph-icon icon-calendar'></i>
                                                        <?php echo ($valores === null?'...':$valores['ds_competencia']);?>
                                                    </div>
                                                </div>
                                                <div class='tile-content-wrapper'>
                                                    <i class='glyph-icon icon-calendar'></i>
                                                    <div class='tile-content'>
                                                        <span></span> <?php echo ($valores === null?'...':number_format($valores['qt_registros'], 0, ',', '.'));?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='col-md-4'>
                                            <div class='tile-box bg-primary'>
                                                <div class='tile-header'>
                                                    Vencimento Base
                                                    <div class='float-right'>
                                                        <i class='glyph-icon icon-caret-up'></i>
                                                        0%
                                                    </div>
                                                </div>
                                                <div class='tile-content-wrapper'>
                                                    <i class='glyph-icon icon-money'></i>
                                                    <div class='tile-content'>
                                                        <span>R$</span> <?php echo ($valores === null?'...':number_format($valores['tot_venctos'], 2, ',', '.'));?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='col-md-4'>
                                            <div class='tile-box bg-warning'>
                                                <div class='tile-header'>
                                                    Descontos
                                                    <div class='float-right'>
                                                        <i class='glyph-icon icon-caret-down'></i>
                                                        <?php
                                                        $vencimento = floatval($valores === null?'0':$valores['tot_venctos']);
                                                        $desconto   = floatval($valores === null?'0':$valores['tot_descontos']);
                                                        $percentual = ($vencimento === 0.0?0.0:($desconto / $vencimento * 100.0));

                                                        echo number_format($percentual, 2, ',', '.') . "%";
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class='tile-content-wrapper'>
                                                    <i class='glyph-icon icon-money'></i>
                                                    <div class='tile-content'>
                                                        <span>R$</span> <?php echo ($valores === null?'...':number_format($valores['tot_descontos'], 2, ',', '.'));?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='col-md-4'>
                                            <div class='tile-box bg-primary'>
                                                <div class='tile-header'>
                                                    Salários
                                                    <div class='float-right'>
                                                        <i class='glyph-icon icon-caret-up'></i>
                                                        <?php
                                                        $vencimento = floatval($valores === null?'0':$valores['tot_venctos']);
                                                        $salario    = floatval($valores === null?'0':$valores['tot_salarios']);
                                                        $percentual = ($vencimento === 0.0?0.0:($salario / $vencimento * 100.0));

                                                        echo number_format($percentual, 2, ',', '.') . "%";
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class='tile-content-wrapper'>
                                                    <i class='glyph-icon icon-money'></i>
                                                    <div class='tile-content'>
                                                        <span>R$</span> <?php echo ($valores === null?'...':number_format($valores['tot_salarios'], 2, ',', '.'));?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="row">
                                        <?php
//                                            $sparklineV = "";
//                                            $sparklineD = "";
//                                            $sparklineS = "";
//                                            $listgrade  = "";
//                                            $mes_inicio = "00/0000";
//                                            $mes_final  = "00/0000";
//                                            foreach($dados_grafico_sparkline as $reg) {
//                                                if (intval($reg['qt_registros']) > 0) {
//                                                    $sparklineV .= $reg['tot_venctos'] . ",";
//                                                    $sparklineD .= $reg['tot_descontos'] . ",";
//                                                    $sparklineS .= $reg['tot_salarios'] . ",";
//                                                    $listgrade  .= "<div class='col-md-2'>{$reg['ds_competencia']}</div>";
//                                                    $mes_inicio  = ($mes_inicio === "00/0000"?$reg['ds_competencia']:$mes_inicio); 
//                                                    $mes_final   = $reg['ds_competencia'];
//                                                } else {
//                                                    $sparklineV .= "0,";
//                                                    $sparklineD .= "0,";
//                                                    $sparklineS .= "0,";
//                                                    $listgrade  .= "<div class='col-md-2'>...</div>  \n";
//                                                }
//                                            }
//
//                                            if ($sparklineV === "") {
//                                                $sparklineV = "0";
//                                                $sparklineD = "0";
//                                                $sparklineS = "0";
//                                                $listgrade  = "<div class='col-md-2'>...</div>";
//                                            } else {
//                                                $sparklineV = substr($sparklineV, 0, strlen($sparklineV) - 1);
//                                                $sparklineD = substr($sparklineD, 0, strlen($sparklineD) - 1);
//                                                $sparklineS = substr($sparklineS, 0, strlen($sparklineS) - 1);
//                                            }
                                        ?>
                                        <div class="col-md-4">
                                            <div class="dashboard-box dashboard-box-chart bg-white content-box">
                                                <div class="content-wrapper">
                                                    <div class="header">
                                                        <span>Vencimentos de <b> <?php // echo $mes_inicio;?></b> até <b><?php // echo $mes_final;?></b></span>
                                                    </div>
                                                    <div class="bs-label bg-primary"><i class='glyph-icon icon-money'></i></div>
                                                    <div class="center-div sparkline-big-alt"><?php // echo $sparklineV;?></div>
                                                    <div class="row list-grade">
                                                        <?php // echo $listgrade;?>
                                                    </div>
                                                </div>
                                                <div class="button-pane">
                                                    <div class="size-md float-left">
                                                        <a href="#" title="">
                                                            Vencimentos consolidados em milhões (Total / 1.000.000)
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="dashboard-box dashboard-box-chart bg-white content-box">
                                                <div class="content-wrapper">
                                                    <div class="header">
                                                        <span>Descontos de <b> <?php // echo $mes_inicio;?></b> até <b><?php // echo $mes_final;?></b></span>
                                                    </div>
                                                    <div class="bs-label bg-warning"><i class='glyph-icon icon-money'></i></div>
                                                    <div class="center-div sparkline-big-alt"><?php // echo $sparklineD;?></div>
                                                    <div class="row list-grade">
                                                        <?php // echo $listgrade;?>
                                                    </div>
                                                </div>
                                                <div class="button-pane">
                                                    <div class="size-md float-left">
                                                        <a href="#" title="">
                                                            Descontos consolidados em milhões (Total / 1.000.000)
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="dashboard-box dashboard-box-chart bg-white content-box">
                                                <div class="content-wrapper">
                                                    <div class="header">
                                                        <span>Salários de <b> <?php // echo $mes_inicio;?></b> até <b><?php // echo $mes_final;?></b></span>
                                                    </div>
                                                    <div class="bs-label bg-primary"><i class='glyph-icon icon-money'></i></div>
                                                    <div class="center-div sparkline-big-alt"><?php // echo $sparklineS;?></div>
                                                    <div class="row list-grade">
                                                        <?php // echo $listgrade;?>
                                                    </div>
                                                </div>
                                                <div class="button-pane">
                                                    <div class="size-md float-left">
                                                        <a href="#" title="">
                                                            Salários consolidados em milhões (Total / 1.000.000)
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    -->
                                </div>
                            </div>
                        </div>
                        
                        <div id="page-wait">
                            <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait">

                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
            include './page_script_gerais.php';
        ?>
        
        <script type="text/javascript" src="./principal.js"></script>
        <script type="text/javascript" src="./src/cargo_salario.js"></script>
        <script type="text/javascript" src="./src/remuneracao_vinculo.js"></script>
        <script type="text/javascript" src="./src/remuneracao_cargo.js"></script>
        <script type="text/javascript" src="./src/remuneracao_servidor.js"></script>
        
        <script type="text/javascript">
            setNomeUnidade('<?php echo $des_unidade;?>');
            setCnpjUnidade('<?php echo $inf_unidade;?>');

//            $(".sparkline-big-alt").sparkline('html', {
//                type: 'line',
//                width: '90%',
//                height: '110',
//                highlightLineColor: '#accfff',
//                lineColor: 'rgba(0,0,0,0.1)',
//                fillColor: '#fcfeff',
//                lineWidth: 1,
//                spotColor: 'transparent',
//                minSpotColor: 'transparent',
//                maxSpotColor: 'transparent',
//                highlightSpotColor: '#65a6ff',
//                spotRadius: 6
//            });
        </script>

		<!-- BEGIN JIVOSITE CODE -->
                <!--
		<script type='text/javascript'>
			(function(){ 
				var widget_id = 'jqdHWpEE09';
				var d=document;
				var w=window;
				
				function l(){ 
					var s = document.createElement('script'); 
					
					s.type = 'text/javascript'; 
					s.async = true; 
					s.src = '//code.jivosite.com/script/widget/'+widget_id; 
					
					var ss = document.getElementsByTagName('script')[0]; 
					ss.parentNode.insertBefore(s, ss);
				} if (d.readyState=='complete'){
					l();
				} else {
					if (w.attachEvent){ 
						w.attachEvent('onload',l);
					} else {
						w.addEventListener('load',l,false);
					}
				}
			})();
		</script>
                -->
		<!-- END JIVOSITE CODE -->								
    </body>
</html>