<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    $print = (isset($_REQUEST['print'])?1:0);
    
    if ($print === 1) {
        include("./lib/mpdf60/mpdf.php");
        ob_start();
    }
            
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body onload="ajustar_tamanho()" id="corpo">
        <div id='page-content'>
            <div class='col-md-12'>
                <div id="page-title">
                    <h2><strong>Lei de Acesso a Informação</strong></h2>
                    <p><strong>LEI Nº 12.527, DE 18 DE NOVEMBRO DE 2011</p>
                </div>
                <div class='panel ng-scope'>
                    <div class='panel-body' id='panel-body'>
                        <iframe id="conteudo" frameborder="0" src="http://www.planalto.gov.br/ccivil_03/_Ato2011-2014/2011/Lei/L12527.htm" style="width: 100%; height: 100%; margin: 0px; padding: 0px;">
                            Não foi possível carregar o conteúdo da página da <a href="http://www.planalto.gov.br/ccivil_03/_Ato2011-2014/2011/Lei/L12527.htm">LEI Nº 12.527, DE 18 DE NOVEMBRO DE 2011</a>.
                        </iframe>
                    </div>
                </div>
                <div id="page-wait">
                    <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait"></a>
                </div>
            </div>
            
            <?php
                if ($print === 1) {
                   echo "<script type='text/javascript' src='./js/jquery.js'></script>";
                }
            ?>
            
            <script type="text/javascript">
                function ajustar_tamanho() {
                    var alturaTela = jQuery(window).height();
                    $('#panel-body').css('height', alturaTela);
                    
                    var alturaCont = (document.getElementById("conteudo").scrollHeight + 40) * 8; //40: Margem Superior e Inferior, somadas
                    $('#panel-body').css('height', (alturaTela + alturaCont));
                }
            </script>
        </div>
    </body>
</html>

<?php
    if ($print === 1) {
//        ini_set("display_errors", 0);
//        $html = ob_get_clean(); 
//
//        $filename = "lei.pdf";
//        $mpdf = new mPDF('A4');    
//        //$mpdf = new mPDF('utf-8', 'A4-L');
//        $mpdf->SetDisplayMode('fullpage');
//
//        $mpdf->WriteHTML($html);
//        $mpdf->Output($filename, 'I'); 

        // Tentativa 1
        //$html = file_get_contents('http://www.planalto.gov.br/ccivil_03/_Ato2011-2014/2011/Lei/L12527.htm');
        
        // Tentativa 2
        //$ch = curl_init();
        //$timeout = 0;
        //curl_setopt($ch, CURLOPT_URL, 'http://www.planalto.gov.br/ccivil_03/_Ato2011-2014/2011/Lei/L12527.htm');
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        //$html = curl_exec($ch);
        //curl_close($ch);

        //$mpdf->WriteHTML($html);
        //$mpdf->Output($filename, 'I'); 
    }
?>;