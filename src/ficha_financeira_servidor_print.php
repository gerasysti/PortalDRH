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

    $nr_srv = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'srv')));
    $nr_ano = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'ano')));
    
    $unidade     = "0";
    $brasao_unid = "../dist/img/remuneratus_logo.png";
    $brasao_tama = "100";
    $md5_unidade = trim(filter_input(INPUT_GET, 'un')); 
    $des_unidade = "Título da Unidade no Portal";
    $inf_unidade = "Informações da Unidade";
    
    $file_htm = "../downloads/FFS_{$nr_srv}{$nr_ano}_{$md5_unidade}.html";
    $tabela   = file_get_contents($file_htm);
?>
<html>
    <head>
        <?php
        
        ini_set('default_charset', 'UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Remuneratu$ | DRH Transparência | Ficha Financeira</title>
    </head>
    <body>
        <style>
            @page {
                margin-top: 1cm;
                margin-bottom: 1cm;
                margin-left: 1cm;
                margin-right: 1cm;
            }
            html { 
                margin: 25px
            }
            h2 {
                font-size: 22px;
                text-transform: uppercase;
                /*//font-variant:small-caps;*/
                padding: 0;
                font-weight: 100;
                margin: 0;
                color: #414C59;
            }
            h3 {
                font-size: 16px;
                text-transform: uppercase;
                /*//font-variant:small-caps;*/
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
            #tb_fichafinanceira{
                font-size: 10px;
                font-weight: 70;
                vertical-align: middle;
            }   
            table#tb_fichafinanceira th.titulo { 
                font-size: 10px;
                background: #0a0a2a; 
                color: rgb(255, 255, 255);
                padding: 4px;
                border: 0.1px solid black;
            }
            table#tb_fichafinanceira th.titulo.direita { 
                text-align: right;
            }
            table#tb_fichafinanceira th.titulo.esquerda { 
                text-align: left; 
            }
            table#tb_fichafinanceira th.titulo.centro { 
                text-align: center;
            }
            table#tb_fichafinanceira th.rodape { 
                text-align: right;
                font-size: 10px;
                background: #0a0a2a; 
                color: rgb(255, 255, 255);
            }
            table#tb_fichafinanceira tr td { /* Toda a tabela com fundo Creme */
                background: #ffc;
                padding: 4px;
                border: 0.1px solid #D9D9D9;
                border-top: 0px;
                border-bottom: 0px;
            }
            table#tb_fichafinanceira tr.dif td {
                background: #eee; /* Linhas com fundo Cinza */
                padding: 4px;
                border: 0.1px solid #D9D9D9;
                border-top: 0px;
                border-bottom: 0px;
            }
            
            .caixa-alta {
                font-variant:small-caps;
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
        <h2><strong>Ficha Financeira</strong></h2>
        <p>Dados disponibilizados de acordo com a Lei de Acesso à Informação - <strong>Lei Nº 12.527, de 18 de Novembro de 2011.</strong></p>
        <h3>Exercício : <?php echo $nr_ano;?></h3>
        <br>
        <?php
            //echo $file_htm . "<br>";
            //echo "<p>Teste</p>";
            echo $tabela;
        ?>
    </body>
</html>
<?php
    error_reporting(0);
    $html = ob_get_clean(); 
    /*
    $pdf  = new DOMPDF();
    
    $pdf->set_paper("A4", "landscape"); // Altera o papel para modo retrato(portrait) ou paisagem (landscape)
    $pdf->load_html($html);
    $pdf->render();
    $pdf->stream("TCS_{$md5_unidade}.pdf");
    */
    $filename = "FFS_{$nr_srv}{$nr_ano}_{$md5_unidade}.pdf";
    $mpdf = new mPDF('utf-8', 'A4-L');
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->SetFooter('Página {PAGENO}/{nbpg}');
    //$stylesheet = file_get_contents('../lib/mpdf60/examples/mpdfstyleA4.css');
    //$mpdf->WriteHTML($stylesheet, 1);
    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, 'I');
?>

