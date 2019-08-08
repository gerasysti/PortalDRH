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
    $nr_cal = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'cal')));
    $nr_exe = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_GET, 'exe')));
    
    $unidade     = "0";
    $brasao_unid = "../dist/img/brasoes/brasao_da_republica.png";
    $md5_unidade = trim(filter_input(INPUT_GET, 'un')); 
    $des_unidade = "Título da Unidade no Portal";
    $inf_unidade = "Informações da Unidade";
?>
<html>
    <head>
        <?php
        
        ini_set('default_charset', 'UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Remuneratu$ | DRH Transparência | Comprovante de Rendimentos</title>
    </head>
    <body>
        <style>
            .irpf-fonte-padrao-grupo {
                font-size: 10px;
                font-family: sans-serif;
            }
            .irpf-fonte-rotulo {
                padding: 2;
                font-size: 7px;
                font-family: sans-serif;
            }
            .irpf-fonte-itensvalores {
                padding: 2;
                font-size: 7px;
                font-family: sans-serif;
            }
            .irpf-fonte-padrao {
                padding: 4;
                font-size: 10px;
                font-family: sans-serif;
                vertical-align: middle;
            }
            .irpf-caixa-texto {
                border-top:    1px solid #000000;
                border-bottom: 1px solid #000000;
                border-left:   1px solid #000000;
                border-right:  1px solid #000000;
                padding: 4;
            }
            .irpf-celula-tabela {
                padding: 4;
                line-height: 10px;
                vertical-align: middle;
            }
            .irpf-borda-esquerda {
                border-left: 1px solid #000000;
            }
            .irpf-borda-direita {
                border-right: 1px solid #000000;
            }
            .irpf-borda-cima {
                border-top: 1px solid #000000;
            }
            .irpf-borda-baixo {
                border-bottom: 1px solid #000000;
            }
            table#tb_irpf_cabecalho {
                font-size: 12px;
                font-family: sans-serif;
                text-align: center;
            }
            table.irpf-bordasimples {
                border-top:    1px solid #000000;
                border-bottom: 1px solid #000000;
                border-left:   1px solid #000000;
                border-right:  1px solid #000000;
            }
            table.irpf-bordasimples tr td {
                padding: 4;
                line-height: 10px;
                vertical-align: middle;
            }
            /*
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
            */
            /* Centralizar na verticação as células de tabelas renderizadas pela classe "dataTable()"*/
            /*
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
            table#tb_remunecacao tr td { // Toda a tabela com fundo Creme 
                background: #ffc;
            }
            table#tb_remunecacao tr.dif td {
                background: #eee; // Linhas com fundo Cinza 
            }
            */
            
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
                        break;
                    }
                }
                
            } catch (Exception $ex) {
                echo "<p>" . $ex . "<br><br>" . $ex->getMessage() . "</p>";
            }
        ?>
        <table class="irpf-bordasimples" cellspacing="0" width="100%" id="tb_irpf_cabecalho">
            <tr>
                <td rowspan="4" width="70">
                    <img src="<?php echo $brasao_unid;?>" height="70">
                </td>
                <td><strong>Ministério da Fazenda</strong></td>
                <td class="irpf-borda-esquerda">Comprovante de Rendimentos Pagos e de</td>
            </tr>
            <tr>
                <td>Secretaria da Receita Federal do Brasil</td>
                <td class="irpf-borda-esquerda">Imposto sobre a Renda Retido na Fonte</td>
            </tr>
            <tr>
                <td><strong>Imposto sobre a Renda da Pessoa Física</strong></td>
                <td class="irpf-borda-esquerda">&nbsp;</td>
            </tr>
            <tr>
                <td><strong><cite>Exercício de <?php echo $nr_exe;?></cite></strong></td>
                <td class="irpf-borda-esquerda"><strong><cite>Ano-Calendário <?php echo $nr_cal;?></cite></strong></td>
            </tr>
        </table>
        
        <p align="justify" class="irpf-fonte-padrao irpf-caixa-texto">
            Verifique as condições e o prazo para a apresentação da Declaração do Imposto sobre a Renda da Pessoa Física para
            ano-calendário no sítio da Secretaria da Receita Federal do Brasil na internet no endereço www.receita.fazenda.gov.br.
        </p>
        
        <table class="irpf-fonte-padrao" cellspacing="0" width="100%" id="tb_irpf_valores">
            <tr>
                <td colspan="5" class="irpf-fonte-padrao-grupo"><strong>1. Fonte Pagadora Pessoa Jurídica</strong></td>
            </tr>
            <tr>
                <td width="150" class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-rotulo">CNPJ</td>
                <td colspan="4" class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-rotulo">Nome empresarial</td>
            </tr>
            <tr>
                <td width="150" class="irpf-borda-esquerda irpf-borda-baixo">XX.XXX.XXX/XXXX-XX</td>
                <td colspan="4" class="irpf-borda-esquerda irpf-borda-baixo irpf-borda-direita">ENTIDADE FULADO DE TAL</td>
            </tr>
            <tr>
                <td colspan="5" class="irpf-fonte-padrao-grupo"><strong>2. Pessoa Física Beneficiária dos Rendimentos</strong></td>
            </tr>
            <tr>
                <td width="150" class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-rotulo">CPF</td>
                <td colspan="4" class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-rotulo">Nome completo</td>
            </tr>
            <tr>
                <td width="150" class="irpf-borda-esquerda irpf-borda-baixo">XXX.XXX.XXX-XX</td>
                <td colspan="4" class="irpf-borda-esquerda irpf-borda-baixo irpf-borda-direita">FULADO DE TAL</td>
            </tr>
            <tr>
                <td colspan="5" class="irpf-borda-esquerda irpf-borda-direita irpf-fonte-rotulo">Natureza do Rendimento</td>
            </tr>
            <tr>
                <td colspan="5" class="irpf-borda-esquerda irpf-borda-baixo irpf-borda-direita">XXX XXXX XXXXXX</td>
            </tr>
            <tr>
                <td colspan="4" class="irpf-fonte-padrao-grupo"><strong>3. Rendimentos Tributáveis, Deduções e Impostos sobre a Renda Retido na Fonte</strong></td>
                <td width="150" class="irpf-fonte-padrao-grupo" align="right"><strong>Valores em reais</strong></td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">1. Total de Rendimentos (inclusive férias)</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">00.000,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">2. Contribuição previdenciária oficial</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0.000,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">3. Contribuições a entidades de previdêcia complementar e a fundos de aposentadoria prog. individual (Fapi)(Preencher também o quadro 7)</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">4. Pensão alimentíca (Preencher também o quadro 7)</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">5. Imposto sobre a renda na fonte</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0.000,00</td>
            </tr>
            <tr>
                <td colspan="4" class="irpf-fonte-padrao-grupo irpf-borda-cima"><strong>4. Rendimentos Isentos e Não Tributáveis</strong></td>
                <td width="150" class="irpf-fonte-padrao-grupo irpf-borda-cima" align="right"><strong>Valores em reais</strong></td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">1. Parcela isenta dos proventos de aposentadoria, reserva numerada, reforma e pensão (65 anos ou mais)</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">2. Diária e ajuda de custo</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">3. Pensão e proventos de aposentadoria ou reforma por moléstia grave; proventos de aposentadoria ou reforma por acidente em serviço</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">4. Lucros e dividendos, apurados a partir de 1996, pagos por pessoa jurídica (lucro real, presumido ou arbitrado)</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">5. Valores pagos ao titular ou sócio da microempresa ou empresa de pequeno porte, exceto pro labore, aluguéis ou serviços prestados</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">6. Indenizações por rescisão de contrato de trabalho, inclusive a título de PDV, e por acidente de trabalho</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">7. Outros (espefificar) Sal. Família / Outros</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0.000,00</td>
            </tr>
            <tr>
                <td colspan="4" class="irpf-fonte-padrao-grupo irpf-borda-cima"><strong>5. Rendimentos sujeitos à Tributação Exclusiva (Rendimento Líquido)</strong></td>
                <td width="150" class="irpf-fonte-padrao-grupo irpf-borda-cima" align="right"><strong>Valores em reais</strong></td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">1. Décimo terceiro salário</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0.000,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">2. Impostos sobre a renda retido na fonte sobre 13o. salário</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">000,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">3. Outros</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td colspan="5" class="irpf-fonte-padrao-grupo irpf-borda-cima"><strong>6. Rendimentos Recebidos Acumuladamente - Art. 12-A da Lei n° 7.713, de 1998 (sujeito à tributação exclusiva)</strong></td>
            </tr>
            <tr>
                <td colspan="2" class="irpf-fonte-padrao-grupo irpf-borda-esquerda irpf-borda-cima">6.1 Número do processo: </td>
                <td width="150" class="irpf-fonte-padrao-grupo irpf-borda-esquerda irpf-borda-cima" align="center"><strong>Quantidade de meses</strong></td>
                <td class="irpf-fonte-padrao-grupo irpf-borda-esquerda irpf-borda-cima">&nbsp;</td>
                <td width="150" class="irpf-fonte-padrao-grupo irpf-borda-esquerda" align="right">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" class="irpf-fonte-padrao-grupo irpf-borda-esquerda irpf-borda-cima">Natureza do Rendimento: </td>
                <td width="150" class="irpf-fonte-padrao-grupo irpf-borda-esquerda" align="right"><strong>Valores em reais</strong></td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">1. Total de rendimentos tributáveis (inclusive férias e décimo terceiro salário)</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">2. Exclusão: Despesas com ação judicial</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">3. Dedução: Contribuição previdencial oficial</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">4. Dedução: Pensão alimentícia (Preencher também quadro 7)</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">5. Imposto sobre a renda retido na fonte</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-itensvalores" colspan="4">6. Rendimentos isentos de pensão, proventos de aposentadoria ou reforma por moléstia grava ou aposentadoria ou reforma por acidente em serviço</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" width="150" align="right">0,00</td>
            </tr>
            <tr>
                <td colspan="5" class="irpf-fonte-padrao-grupo irpf-borda-cima"><strong>7. Informações Complementares</strong></td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-itensvalores" colspan="5">
                    <p>
                        &nbsp;<br>
                        &nbsp;<br>
                        &nbsp;<br>
                        &nbsp;<br>
                        &nbsp;
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="irpf-fonte-padrao-grupo irpf-borda-cima"><strong>8. Responsável pelas informações</strong></td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-rotulo" colspan="2">Nome</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-fonte-rotulo">Data</td>
                <td class="irpf-borda-esquerda irpf-borda-cima irpf-borda-direita irpf-fonte-rotulo" colspan="2">Assinatura</td>
            </tr>
            <tr>
                <td class="irpf-borda-esquerda irpf-borda-baixo" colspan="2">&nbsp;</td>
                <td class="irpf-borda-esquerda irpf-borda-baixo">&nbsp;</td>
                <td class="irpf-borda-esquerda irpf-borda-baixo irpf-borda-direita" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" class="irpf-fonte-rotulo">Aprovado pela IN RFB n° de 2011.</td>
            </tr>
        </table>
    </body>
</html>
<?php
    error_reporting(0);
    $html = ob_get_clean(); 
    /*
    $pdf  = new DOMPDF();
    
    $pdf->set_paper("A4", "portrait"); // Altera o papel para modo retrato(portrait) ou paisagem (landscape)
    $pdf->load_html($html);
    $pdf->render();
    $pdf->stream("IRPF_{$md5_unidade}.pdf");
    */
    $filename = "IRPF_{$md5_unidade}.pdf";
    $mpdf = new mPDF('A4');    
    $mpdf->SetDisplayMode('fullpage');

    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, 'I');
?>