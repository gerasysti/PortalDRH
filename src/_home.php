<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
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
    
/*
Select
    x.ano_mes
  , substring(x.ano_mes from 5 for 2) || '/' || substring(x.ano_mes from 1 for 4) as ds_competencia
  , count( s.ano_mes ) as qt_registros
  , sum( case when coalesce(s.id_est_funcional, 0) = 1 then 1 else 0 end ) as qt_servidores
  , sum( s.tot_venctos )   as tot_venctos
  , sum( s.tot_descontos ) as tot_descontos
  , sum( s.sal_liquido )   as tot_salarios
from (
    Select
        x.parcela
      , x.id_cliente
      , max(x.ano_mes) as ano_mes
    from REMUN_BASE_CALC_MES x
    where (x.id_cliente = 15019)
    group by
        x.parcela
      , x.id_cliente
) x
  inner join REMUN_BASE_CALC_MES s on (s.id_cliente = x.id_cliente and s.ano_mes = x.ano_mes and s.parcela = x.parcela)
group by
    x.ano_mes
*/    
    
    unset($res);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <title>Remuneratu$ | DRH Transparência | Informações</title>

        <?php
            include '../page_head_links.php';
        ?>
    </head>
    <body>
        <div id='page-content'>
            <div class='col-md-12'>
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
                                    <div class='tile-box bg-blue'>
                                        <div class='tile-header'>
                                            Servidores
                                            <div class='float-right'>
                                                <!--<i class='glyph-icon icon-calendar'></i>-->
                                                OUT/2019
                                            </div>
                                        </div>
                                        <div class='tile-content-wrapper'>
                                            <i class='glyph-icon icon-users'></i>
                                            <div class='tile-content'>
                                                <span></span> 0
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
                                            <i class='glyph-icon icon-bullhorn'></i>
                                            <div class='tile-content'>
                                                <span>R$</span> 0,00
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='col-md-4'>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="page-wait">
                    <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait"></a>
                </div>
            </div>
        </div>
    </body>
</html>
