<!DOCTYPE html>
<?php
    // http://www.drhtransparencia.com.br/admin/lancar_eventos_print.php

    include("../lib/mpdf60/mpdf.php");
    ob_start();

    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';
    
    ini_set('default_charset', 'UTF-8');
    ini_set('display_errors', true);
    error_reporting(E_ALL);
    
    $id = md5(date('d/m/Y'));
    $cliente = null;
    $evento  = null;
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = 
          "Select     "
        . "    a.id_cliente "
        . "  , a.id_unid_gestora "
        . "  , g.descricao as nm_unid_gestora "
        . "  , a.id_unid_orcament "
        . "  , o.descricao as nm_unid_orcament "
        . "  , a.id_evento "
        . "  , e.descricao as nm_evento "
        . "  , e.codigo    as cd_evento "
        . "  , a.ano_mes "
        . "  , substring(a.ano_mes from 5 for 2) || '/' || substring(a.ano_mes from 1 for 4) as competencia "
        . "  , a.controle "
        . "  , a.data "
        . "  , a.hora "
        . "  , a.usuario  as responsavel_id "
        . "  , u.nome     as responsavel_nome "
        . "  , u.e_mail   as responsavel_email "
        . "  , a.situacao as cd_situacao "
        . "  , Case a.situacao "
        . "      when 0 then 'Aberto' "
        . "      when 1 then 'Finalizado' "
        . "      when 2 then 'Cancelado' "
        . "    end as ds_situacao "
        . "  , Case a.situacao "
        . "      when 0 then 'label-warning' "
        . "      when 1 then 'label-success' "
        . "      when 2 then 'label-danger' "
        . "    end as lb_situacao "
        . "  , a.importado "
        . "from REMUN_EVENTO_AVULSO a "
        . "  inner join REMUN_UNID_GESTORA g on (g.id_cliente = a.id_cliente and g.id = a.id_unid_gestora) "
        . "  inner join REMUN_UNID_ORCAMENT o on (o.id_cliente = a.id_cliente and o.id = a.id_unid_orcament) "
        . "  inner join REMUN_EVENTO e on (e.id_cliente = a.id_cliente and e.id_evento = a.id_evento) "
        . "  left join ADM_USUARIO u on (u.id_cliente = a.id_cliente and u.id = a.usuario) "
        . "where (a.controle = :controle) ";

    $stm = $pdo->prepare($sql);
    $res = $stm->execute(array(
        ':controle' => 19
    ));
    $evento = $stm->fetch(PDO::FETCH_OBJ);
    
    $sql = 
         "Select     "
        ."    u.id   "
        ."  , u.nome as nome_cliente "
        ."  , u.cnpj as cnpj_cliente "
        ."  , case when coalesce(g.dados_ug_ccheque, 'N') = 'S' then g.razao_social else u.nome end as nome "
        ."  , case when coalesce(g.dados_ug_ccheque, 'N') = 'S' then g.cnpj else u.cnpj end as cnpj "
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
        ."  left join REMUN_UNID_GESTORA g on (g.id_cliente = u.id and g.id = :id_unidade) "
        ."where (u.id = :id_cliente) "
        ."order by "
        ."    trim(coalesce(u.titulo_portal, u.nome))";
    
    $stm = $pdo->prepare($sql);
    $res = $stm->execute(array(
        ':id_cliente' => $evento->id_cliente,
        ':id_unidade' => $evento->id_unid_gestora
    ));
    $cliente = $stm->fetch(PDO::FETCH_OBJ);

    // Fechar conexão PDO
    unset($qry);
    unset($pdo);
?>
<html ng-app="monarchApp" lang="pt">
    <head>
        <style>
            @page {
                margin-top   : 1.5cm;
                margin-bottom: 2.5cm;
                margin-left  : 1cm;
                margin-right : 1cm;
            }
            .td_align_right  { text-align: right; }
            .td_align_left   { text-align: left; }
            .td_align_center { text-align: center; }
            .td_align_justify{ text-align: justify; }
        </style>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <title>Remuneratu$ | Lançamentos de Eventos Avulsos </title>
        <link rel="shortcut icon" href="../gerasys.ico" >

        <?php
            $page_head_links = file_get_contents("../page_head_links.php");
            echo str_replace("./", "../", $page_head_links);
        ?>
        <!-- DRH -->
        <link rel="stylesheet" type="text/css" href="./controle.css">
   </head>

    <body>

        <div class="content"><!-- "pad25A" coloca uma margem maior -->
            <div class="row">
                <div class="col-sm-3">
                    <div class="col-xs-1"> <!-- dummy-logo -->
                        <img src="<?php echo $cliente->brasao;?>" height="60" alt="Brasão da Unidade/Órgão"/>
                    </div>
                    <div class="col-sm-11 font-size-12">
                        <p><b><?php echo $cliente->nome_cliente;?></b></p>
                        <p>CNPJ : <?php echo formatarTexto('##.###.###/####-##', $cliente->cnpj_cliente);?></p>
                        <p><?php echo $cliente->municipio_nome . " - " . $cliente->municipio_uf;?></p>
                    </div>
                </div>
                
                <div class="col-sm-6 float-right text-right">
                    <h4 class="invoice-title font-size-20">EVENTOS AVULSOS</h4>
                    Controle <b>#<?php echo str_pad($evento->controle, 5, "0", STR_PAD_LEFT);?></b>
                    <div class="divider"></div>
                    <div class="invoice-date mrg20B"><?php echo date('d/m/Y');?></div>
                    <!--
                    <button onclick="printInvoice()" class="btn btn-alt btn-hover btn-info">
                        <span>Print Invoice</span>
                        <i class="glyph-icon icon-print"></i>
                    </button>
                    <button onclick="printInvoice()" class="btn btn-alt btn-hover btn-danger">
                        <span>Cancel Invoice</span>
                        <i class="glyph-icon icon-trash"></i>
                    </button>
                    -->
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <h2 class="invoice-client mrg10T">Identificação:</h2>
                    <!--
                    <ul class="reset-ul">
                        <li><b>Id   : </b> #<?php // echo str_pad($evento->controle, 5, "0", STR_PAD_LEFT);?></li>
                        <li><b>Data : </b> <?php // echo (!empty($evento->data)?date('d/m/Y', strtotime($evento->data) ):"&nbsp;");?></li>
                        <li><b>Hora : </b> <?php // echo (!empty($evento->hora)?date('H:i:s', strtotime($evento->hora) ):"&nbsp;");?></li>
                        <li><b>Responsável : </b> <?php // echo $evento->responsavel_nome;?> (<small class="font-blue"><?php // echo $evento->responsavel_email;?></small>)</li>
                        <li><b>Status : </b> <span class="bs-label <?php // echo $evento->lb_situacao;?>"> <strong>&nbsp;&nbsp; <?php // echo $evento->ds_situacao;?> &nbsp;&nbsp;</strong> </span></li>
                    </ul>
                    -->
                    
                    <table border="0" class="font-size-12">
                        <tr>
                            <td height="21"><b>Id &nbsp;&nbsp;</b></td>
                            <td>: #<?php echo str_pad($evento->controle, 5, "0", STR_PAD_LEFT);?></td>
                        </tr>
                        <tr>
                            <td height="21"><b>Data &nbsp;&nbsp;</b></td>
                            <td>: <?php echo (!empty($evento->data)?date('d/m/Y', strtotime($evento->data) ):"&nbsp;");?></td>
                        </tr>
                        <tr>
                            <td height="21"><b>Hora &nbsp;&nbsp;</b></td>
                            <td>: <?php echo (!empty($evento->hora)?date('H:i:s', strtotime($evento->hora) ):"&nbsp;");?></td>
                        </tr>
                        <tr>
                            <td><b>Responsável &nbsp;&nbsp;</b></td>
                            <td>: <?php echo $evento->responsavel_nome;?> (<small class="font-blue"><?php echo $evento->responsavel_email;?></small>)</td>
                        </tr>
                        <tr>
                            <td height="21"><b>Status &nbsp;&nbsp;</b></td>
                            <td>: <span class="bs-label <?php echo $evento->lb_situacao;?>"> <strong>&nbsp;&nbsp; <?php echo $evento->ds_situacao;?> &nbsp;&nbsp;</strong> </span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <h2 class="invoice-client mrg10T">Dados do Lançamento:</h2>
                    <!--
                    <h5>Unidade Gestora : <?php // echo str_pad($evento->id_unid_gestora, 4, "0", STR_PAD_LEFT) . " - " . $evento->nm_unid_gestora;?></h5>
                    <h5>Unidade Orçamentária : <?php // echo str_pad($evento->id_unid_orcament, 4, "0", STR_PAD_LEFT) . " - " . $evento->nm_unid_orcament;?></h5>
                    <h5>Evento : <?php // echo $evento->cd_evento . " - " . $evento->nm_evento;?></h5>
                    <h5>Competência : <?php // echo $evento->competencia;?></h5>
                    -->
                    <table border="0" class="font-size-12">
                        <tr>
                            <td height="21">Unidade Gestora &nbsp;&nbsp;</td>
                            <td>: <?php echo str_pad($evento->id_unid_gestora, 4, "0", STR_PAD_LEFT) . " - " . $evento->nm_unid_gestora;?></td>
                        </tr>
                        <tr>
                            <td height="21">Unidade Orçamentária &nbsp;&nbsp;</td>
                            <td>: <?php echo str_pad($evento->id_unid_orcament, 4, "0", STR_PAD_LEFT) . " - " . $evento->nm_unid_orcament;?></td>
                        </tr>
                        <tr>
                            <td height="21">Evento &nbsp;&nbsp;</td>
                            <td>: <?php echo $evento->cd_evento . " - " . $evento->nm_evento;?></td>
                        </tr>
                        <tr>
                            <td>Competência &nbsp;&nbsp;</td>
                            <td>: <?php echo $evento->competencia;?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="divider"></div>

            <div class="row">
                <div class="col-md-4">
                    <h2 class="invoice-client mrg10T">Servidores:</h2>
                    <!--<p>Os servidores relacionados aqui tiveram seus lançamentos, para o evento XXX, realizados no portal XXX.</p>-->
                </div>
            </div>
            
            
Select
    a.controle
  , b.*
  , s.nome
  , s.matricula
  , s.cpf
  , s.rg
  , s.pis_pasep
  , s.dt_admissao
  , s.situacao
  , s.status
from REMUN_EVENTO_AVULSO a
  inner join REMUN_EVENTO_AVULSO_ITEM b on (
        b.id_cliente       = a.id_cliente
    and b.id_unid_gestora  = a.id_unid_gestora
    and b.id_unid_orcament = a.id_unid_orcament
    and b.id_evento = a.id_evento
    and b.ano_mes   = a.ano_mes)
  inner join REMUN_SERVIDOR s on (s.id_cliente = b.id_cliente and s.id_servidor = b.id_servidor)

--where (a.controle = 19)

order by
    s.nome

            
            <table border="1" width="100%" class="font-size-12">
                
            </table>
            
            <!--
            <table class="table mrg20T table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th class="text-center">Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Lenovo Laptop</td>
                        <td class="text-center">1</td>
                        <td>$433.10</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Mighty Mouse</td>
                        <td class="text-center">4</td>
                        <td>$41.00</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Samsung LED TV</td>
                        <td class="text-center">1</td>
                        <td>$389.50</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Apple iMac 27"</td>
                        <td class="text-center">1</td>
                        <td>$1999.05</td>
                    </tr>
                    <tr class="font-bold font-black">
                        <td colspan="3" class="text-right">Subtotal:</td>
                        <td colspan="3">$2312.50</td>
                    </tr>
                    <tr class="font-bold font-black">
                        <td colspan="3" class="text-right">Shipping:</td>
                        <td colspan="3">$12.20</td>
                    </tr>
                    <tr class="font-bold font-black">
                        <td colspan="3" class="text-right">Discount:</td>
                        <td colspan="3" class="font-red">$5.10</td>
                    </tr>
                    <tr class="font-bold font-black">
                        <td colspan="3" class="text-right">TOTAL:</td>
                        <td colspan="3" class="font-blue font-size-23">$2710.65</td>
                    </tr>
                </tbody>
            </table>
            -->
            <div class="divider"></div>
            
            <div class="row">
                <div class="col-md-12">
                    <p>Os servidores relacionados aqui tiveram seus lançamentos, para o evento XXX, realizados no portal XXX.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    1
                </div>
                <div class="col-md-4">
                    2
                </div>
                <div class="col-md-4">
                    3
                </div>
            </div>
        </div>
        
        <?php
            $page_script_gerais = file_get_contents("../page_script_gerais.php");
            echo str_replace("./", "../", $page_script_gerais);
        ?>
    </body>
</html>
<?php
    error_reporting(0);
    $html = ob_get_clean(); 

    //$filename = "../downloads/{$token}.pdf";
    $filename = "./lancar_eventos_print.pdf";
    
    $mpdf = new mPDF('utf-8', 'A4');    
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->SetFooter('Página {PAGENO}/{nbpg}');

    $mpdf->WriteHTML($html);
    //$mpdf->Output($filename, 'F');
    $mpdf->Output($filename, 'I');
?>

