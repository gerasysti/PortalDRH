<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.

-- DADOS PARA O GRAFICOS EM LINHAS (.sparkline)
    Select
      x.*
    from (
        Select first 6
            s.ano_mes
          , substring(s.ano_mes from 5 for 2) || '/' || substring(s.ano_mes from 1 for 4) as ds_competencia
          , count( s.ano_mes ) as qt_registros
          , sum( case when coalesce(s.id_est_funcional, 0) = 1 then 1 else 0 end ) as qt_servidores
          , (sum( s.tot_venctos )   / 1000000.0) as tot_venctos
          , (sum( s.tot_descontos ) / 1000000.0) as tot_descontos
          , (sum( s.sal_liquido )   / 1000000.0) as tot_salarios
        from REMUN_BASE_CALC_MES s
        where (s.id_cliente = 15049)
          and (substring(s.ano_mes from 1 for 4) = '2019')
          and (s.ano_mes < '201913')
        group by
            s.ano_mes
        order by
          s.ano_mes DESC
    ) x
    order by
      x.ano_mes

-->
<?php
    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';
    
    $id = 0;
    $des_unidade = "Título da Unidade no Portal";
    $inf_unidade = "Informações da Unidade";
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 
          "Select "
        . "    u.id "
        . "  , u.nome "
        . "  , u.cnpj "
        . "  , u.municipio_nome "
        . "  , u.municipio_uf "
        . "  , coalesce(u.exibe_lista, '0') as exibe_lista_servidor "
        . "  , trim(coalesce(nullif(trim(u.titulo_portal), ''), trim(u.nome))) as titulo_portal "
        . "  , coalesce(u.ender_lograd, '...') as endereco "
        . "  , coalesce(u.ender_num,    '...') as numero "
        . "  , coalesce(u.ender_bairro, '...') as bairro "
        . "from ADM_CLIENTE u "
        . "where (u.id = {$_REQUEST['id']})";

    $res = $pdo->query($sql);
    if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        //$id = md5($obj->id);
        $id = $obj->id;
        $des_unidade = $obj->titulo_portal;
        $inf_unidade = "CNPJ: " . formatarTexto('##.###.###/####-##', $obj->cnpj);
    }
    
    $sql = 
          "Select "
        . "    count(s.id_servidor) as qt_cadastro "
        . "  , sum(case when s.situacao = 1 then 1 else 0 end) as qt_servidores "
        . "from REMUN_SERVIDOR s "
        . "where (s.id_cliente = {$id})";
        
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
        . "    where (x.id_cliente = {$id}) "
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
    foreach($dados as $item) {
        $valores = $item;
    }
    
    unset($res);
    unset($qry);
    unset($dados);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <div id='page-content'>
            <div class='col-md-12'>
                <!-- Sparklines charts -->
                <script type="text/javascript" src="./assets/widgets/charts/sparklines/sparklines.js"></script>
                <script type="text/javascript" src="./assets/widgets/charts/sparklines/sparklines-demo.js"></script>

                <!-- Flot charts -->
                <script type="text/javascript" src="./assets/widgets/charts/flot/flot.js"></script>
                <script type="text/javascript" src="./assets/widgets/charts/flot/flot-resize.js"></script>
                <script type="text/javascript" src="./assets/widgets/charts/flot/flot-stack.js"></script>
                <script type="text/javascript" src="./assets/widgets/charts/flot/flot-pie.js"></script>
                <script type="text/javascript" src="./assets/widgets/charts/flot/flot-tooltip.js"></script>
                <script type="text/javascript" src="./assets/widgets/charts/flot/flot-demo-1.js"></script>

                <!-- PieGage charts -->
                <script type="text/javascript" src="./assets/widgets/charts/piegage/piegage.js"></script>
                <script type="text/javascript" src="./assets/widgets/charts/piegage/piegage-demo.js"></script>

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
                            
                            <div class="row">
                                
                                <div class="col-md-4">
                                    <div class="dashboard-box dashboard-box-chart bg-white content-box">
                                        <div class="content-wrapper">
                                            <div class="header">
                                                ...
                                                <span>Vencimentos de <b> 01/2019</b> até <b>06/2019</b></span>
                                            </div>
                                            <div class="bs-label bg-green">0%</div>
                                            <div class="center-div sparkline-big-alt">0,0.5,0,0,0,0.6</div>
                                            <div class="row list-grade">
                                                <div class="col-md-2">01/2019</div>
                                                <div class="col-md-2">02/2019</div>
                                                <div class="col-md-2">03/2019</div>
                                                <div class="col-md-2">04/2019</div>
                                                <div class="col-md-2">05/2019</div>
                                                <div class="col-md-2">06/2019</div>
                                            </div>
                                        </div>
                                        <div class="button-pane">
                                            <div class="size-md float-left">
                                                <a href="#" title="">
                                                    Dados financeiros consolidados
                                                </a>
                                            </div>
                                            <!--
                                            <a href="#" class="btn btn-info float-right tooltip-button" data-placement="top" title="View details">
                                                <i class="glyph-icon icon-plus"></i>
                                            </a>
                                            -->
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div id="page-wait">
                    <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait"></a>
                </div>
            </div>
            
            <script type="text/javascript">
                $(".sparkline-big-alt").sparkline('html', {
                    type: 'line',
                    width: '90%',
                    height: '110',
                    highlightLineColor: '#accfff',
                    lineColor: 'rgba(0,0,0,0.1)',
                    fillColor: '#fcfeff',
                    lineWidth: 1,
                    spotColor: 'transparent',
                    minSpotColor: 'transparent',
                    maxSpotColor: 'transparent',
                    highlightSpotColor: '#65a6ff',
                    spotRadius: 6
                });
            </script>
        </div>
    </body>
</html>
