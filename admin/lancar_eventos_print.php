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
    $token = strip_tags( trim(filter_input(INPUT_POST, 'hs')) );
    $controle = (float)preg_replace("/[^0-9]/", "", "0" . strip_tags( trim(filter_input(INPUT_POST, 'controle')) ));
            
    $cliente = null;
    $evento  = null;
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = 
          "Select     "
        . "    a.controle   "
        . "  , a.id_cliente "
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
        . "  , coalesce(a.data_finalizacao, a.data) as data "
        . "  , coalesce(a.hora_finalizacao, a.hora) as hora "
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
        ':controle' => $controle
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
    unset($stm);
    unset($pdo);
    
    // Hora
    $temp = date('H:i:s', strtotime($evento->hora) );
    $hora_gravacao = substr($temp, 0, 2) . "h" . substr($temp, 3, 2);
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
            
            .td_height_21  { height: 21px; }
            .td_height_25  { height: 25px; }
            
            .td_padding_1 {
                padding-left: 1px;
                padding-right: 1px;
            }
            
            .td_padding_5 {
                padding-left: 5px;
                padding-right: 5px;
            }
            
            .fundoCinzaEscuro { background-color: grey; }
            .fundoCinzaClaro { background-color: #E8E8E8; }
            .fundoBranco { background-color: #FFFFFF; }
            
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
                        <img src="<?php echo $cliente->brasao;?>" height="70" alt="Brasão da Unidade/Órgão"/>
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
                            <td>: <b><?php echo $evento->cd_evento . " - " . $evento->nm_evento;?></b></td>
                        </tr>
                        <tr>
                            <td>Competência &nbsp;&nbsp;</td>
                            <td>: <b><?php echo $evento->competencia;?></b></td>
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
            
            
            <table border="0" width="100%" class="font-size-12">
                <thead>
                    <tr class="fundoCinzaEscuro">
                        <th class='td_height_21 td_padding_5 td_align_center' style="color: white;">#</th>
                        <th class='td_height_21 td_padding_5' style="color: white;">Matrícula</th>
                        <th class='td_height_21 td_padding_5' style="color: white;">Servidor</th>
                        <th class='td_height_21 td_padding_5' style="color: white;">Cargo/Função</th>
                        <th class='td_height_21 td_padding_5 td_align_right' style="color: white;">Qtde</th>
                        <th class='td_height_21 td_padding_5 td_align_right' style="color: white;">Valor (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = 
                              "Select           \n"
                            . "    a.controle   \n"
                            . "  , s.nome       \n"
                            . "  , s.matricula  \n"
                            . "  , s.cpf        \n"
                            . "  , s.rg         \n"
                            . "  , coalesce(f.descricao, '* CARGO/FUNÇÃO NÃO INFORMADO') as cargo_funcao   \n"
                            . "  , s.pis_pasep      \n"
                            . "  , s.dt_admissao    \n"
                            . "  , s.situacao       \n"
                            . "  , s.status     \n"
                            . "  , coalesce(b.quant,   0) as quant  \n"
                            . "  , coalesce(b.valor, 0.0) as valor  \n"
                            . "  , coalesce(b.obs,    '') as obs    \n"
                            . "from REMUN_EVENTO_AVULSO a   \n"
                            . "  inner join REMUN_EVENTO_AVULSO_ITEM b on (     \n"
                            . "        b.id_cliente       = a.id_cliente        \n"
                            . "    and b.id_unid_gestora  = a.id_unid_gestora   \n"
                            . "    and b.id_unid_orcament = a.id_unid_orcament  \n"
                            . "    and b.id_evento = a.id_evento    \n"
                            . "    and b.ano_mes   = a.ano_mes)     \n"
                            . "  inner join REMUN_SERVIDOR s on (s.id_cliente = b.id_cliente and s.id_servidor = b.id_servidor)     \n"
                            . "  left join REMUN_CARGO_FUNCAO f on (f.id_cliente = s.id_cliente and f.id_cargo = s.id_cargo_atual)  \n"
                            . "   \n"
                            . "where (a.controle = {$evento->controle}) \n"
                            . "   \n"
                            . "order by     \n"
                            . "    s.nome   \n";

                        $res = $pdo->query($sql);
                        $i = 0;
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $i += 1;
                            
                            $quant = number_format( $obj->quant, 0, ',', '.');
                            $valor = number_format( $obj->valor, 2, ',', '.');
                            $cor = ($i & 1?"fundoCinzaClaro":"fundoBranco");
                            
                            echo
                                  "<tr> "
                                . "    <td class='td_padding_5 td_height_25 td_align_center {$cor}'>{$i}</td> "
                                . "    <td class='td_padding_5 td_height_25 {$cor}'>{$obj->matricula}</td> "
                                . "    <td class='td_padding_5 td_height_25 {$cor}'>{$obj->nome}</td> "
                                . "    <td class='td_padding_5 td_height_25 {$cor}'>{$obj->cargo_funcao}</td> "
                                . "    <td class='td_padding_5 td_height_25 td_align_right {$cor}'>{$quant}</td> "
                                . "    <td class='td_padding_5 td_height_25 td_align_right {$cor}'>{$valor}</td> " 
                                . "</tr>    \n";
                        }
                        
                        // Fechar conexão PDO
                        unset($res);
                        unset($pdo);
                    ?>    
                </tbody>
                
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
            
            <div class="row">
                &nbsp;
            </div>
            
            <div class="content-box">
                <div class="col-md-12">
                    <p class="text-justify">Os servidores relacionados aqui tiveram seus lançamentos, para o evento 
                        <b><?php echo $evento->cd_evento . " - " . $evento->nm_evento;?></b> na competência de <b><?php echo $evento->competencia;?></b>, 
                        realizados no portal <b>Remuneratu$Web</b> pelo usuário <b><?php echo $evento->responsavel_nome;?></b> no dia 
                        <b><?php echo (!empty($evento->data)?date('d/m/Y', strtotime($evento->data) ):"&nbsp;");?></b> às 
                        <b><?php echo $hora_gravacao;?></b>.</p>
                </div>
            </div>
            
            <div class="col-sm-12">
                <table border="0" width="50%" class="font-size-12" align="center">
                    <tr width="100%" >
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class=" borderTop">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="text-center"><?php echo $cliente->nome;?></td>
                    </tr>
                    <tr>
                        <td class="text-center">CNPJ : <?php echo formatarTexto('##.###.###/####-##', $cliente->cnpj_cliente);?></td>
                    </tr>
                </table>
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

    $filename = "../downloads/{$token}.pdf";
    
    $mpdf = new mPDF('utf-8', 'A4');    
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->SetFooter('Página {PAGENO}/{nbpg}');

    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, 'F');
?>

