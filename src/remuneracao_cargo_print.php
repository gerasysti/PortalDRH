<!DOCTYPE html>
<?php
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

    $nr_ano = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'ano')));
    $nr_mes = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'mes')));
    $id_vin = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'vin')));
    $ds_vin = "";
    
    $unidade     = "0";
    $brasao_unid = "../dist/img/remuneratus_logo.png";
    $brasao_tama = "100";
    $md5_unidade = trim(filter_input(INPUT_GET, 'un')); 
    $des_unidade = "Título da Unidade no Portal";
    $inf_unidade = "Informações da Unidade";
    
    $tabela = file_get_contents("../downloads/Renumeracao_Cargo_{$nr_ano}{$nr_mes}_{$md5_unidade}.html");
?>
<html>
    <head>
        <?php
        
        ini_set('default_charset', 'UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>GeraSys TI | DRH Transparência | Consolidado de Remuneração por VÍnculos</title>
    </head>
    <body>
        <style>
            @page {
                margin-top: 1.5cm;
                margin-bottom: 1.5cm;
                margin-left: 1cm;
                margin-right: 1cm;
            }
            html { 
                margin: 25px
            }
            h2 {
                font-size: 22px;
                text-transform: uppercase;
                padding: 0;
                font-weight: 100;
                margin: 0;
                color: #414C59;
            }
            h3 {
                font-size: 16px;
                text-transform: uppercase;
                padding: 0;
                font-weight: 70;
                margin: 0;
                color: #414C59;
            }
            p {
                font-size: 12px;
                padding: 0;
                font-weight: 50;
                margin: 0;
                opacity: 0.6;
                width:100%;
            }
            strong {
                font-weight:bold;
            }            
            hr {
                display: block;
                margin-top: 0.5em;
                margin-bottom: 0.5em;
                margin-left: auto;
                margin-right: auto;
                border-style: inset;
                border-width: 1px;
            }
            
            /* Centralizar na verticação as células de tabelas renderizadas pela classe "dataTable()"*/
            table.dataTable tbody td {
                vertical-align: middle;
            }            
            #tb_remunecacao{
                font-size: 12px;
                font-weight: 70;
            }   
            table#tb_remunecacao th.titulo { 
                font-size: 10px;
                background: #0a0a2a; 
                color: rgb(255, 255, 255);
            }
            table#tb_remunecacao th.titulo.direita { 
                text-align: right;
            }
            table#tb_remunecacao th.titulo.esquerda { 
                text-align: left; 
            }
            table#tb_remunecacao th.titulo.centro { 
                text-align: center;
            }
            table#tb_remunecacao th.rodape { 
                text-align: right;
                font-size: 10px;
                background: #0a0a2a; 
                color: rgb(255, 255, 255);
            }
            table#tb_remunecacao tr td { /* Toda a tabela com fundo Creme */
                background: #ffc;
            }
            table#tb_remunecacao tr.dif td {
                background: #eee; /* Linhas com fundo Cinza */
            }
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
                    ."  , coalesce(nullif(trim(u.logo), ''), '../dist/img/remuneratus_logo.png') as brasao "    
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
                        $brasao_unid = $obj->brasao;
                        $brasao_tama = "40";
                        break;
                    }
                }
                
                if (intval($id_vin) !== 0) {
                    $sql = 
                         "Select "
                        ."    v.id "
                        ."  , v.descricao "
                        ."from REMUN_VINCULO v "
                        ."where v.id = " . intval($id_vin);

                    $res = $pdo->query($sql);
                    if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                        $ds_vin = $obj->descricao;
                    }
                }
            } catch (Exception $ex) {
                echo "<p>" . $ex . "<br><br>" . $ex->getMessage() . "</p>";
            }
        ?>
        <table border="0" width="100%">
            <tr>
                <td rowspan="2" width="<?php echo $brasao_tama;?>">
                    <img src="<?php echo $brasao_unid;?>" height="50">
                </td>
                <td>
                    <h2><strong><?php echo $des_unidade;?></strong></h2>
                </td>
            </tr>
            <tr>
                <td>
                    <p><?php echo $inf_unidade;?></p>
                </td>
            </tr>
        </table>
        <hr>
        <h2><strong>Consolidação de Remuneração por Vínculos</strong></h2>
        <p>Dados disponibilizados de acordo com a Lei de Acesso à Informação - <strong>Lei Nº 12.527, de 18 de Novembro de 2011.</strong></p>
        <h3>Competência : <?php echo getDescricaoMes_v2($nr_mes) . " / " . $nr_ano?></h3>
        <?php
            if (intval($id_vin) !== 0) {
                echo "<p>Vínculo : <strong>{$ds_vin}</strong></p>";
            }
        ?>
        <br>
        <?php
            echo $tabela;
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
    $pdf->stream("Remuneracao_Cargo_{$md5_unidade}.pdf");
    */
    $filename = "Remuneracao_Cargo_{$md5_unidade}.pdf";
    $mpdf = new mPDF('A4');    
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->SetFooter('Página {PAGENO}/{nbpg}');
    //$stylesheet = file_get_contents('../lib/mpdf60/examples/mpdfstyleA4.css');
    //$mpdf->WriteHTML($stylesheet, 1);
    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, 'I');
?>