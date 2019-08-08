<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    ini_set('display_errors', true);
    error_reporting(E_ALL);

    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ac'])) {
            switch ($_POST['ac']) {
                case 'consultar_rendimentos' : {
                    try {
                        $id_cliente    = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'un')));
                        $id_servidor   = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'sv')));
                        $nr_calendario = (int)preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_calendario')));
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr>";
                        $tabela .= "            <th>Calendário</th>";
                        $tabela .= "            <th>Exercício</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>Rendimentos</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>Contribuições</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>13o. Salário</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>Impostos Retidos</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>Outros</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false'>Despesas</th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = 
                              "Select "
                            . "    r.id_cliente  "
                            . "  , r.id_servidor "
                            . "  , cast(substring(r.ano_mes from 1 for 4) as smallint)     as nr_calendario "
                            . "  , cast(substring(r.ano_mes from 1 for 4) as smallint) + 1 as nr_exercicio  "
                            . "  , u.descricao as nm_unid_gestora        "
                            . "  , sum(r.tot_venctos)   as tot_venctos   "
                            . "  , sum(r.tot_descontos) as tot_descontos "
                            . "  , sum(r.sal_liquido)   as sal_liquido   "
                            . "  , sum(r.bc_sal_fam)    as bc_sal_fam    "
                            . "  , sum(r.bc_ats)        as bc_ats        "
                            . "  , sum(r.bc_ferias)     as bc_ferias     "
                            . "  , sum(r.bc_dec_terc)   as bc_dec_terc   "
                            . "  , sum(r.bc_irrf)       as bc_irrf "
                            . "from REMUN_BASE_CALC_MES r    "
                            . "  left join REMUN_UNID_GESTORA u on (u.id_cliente = r.id_cliente and u.id = r.id_unid_gestora) "
                            . "where r.id_cliente  = '{$id_cliente}' "
                            . "  and r.id_servidor = {$id_servidor}  "
                            . "  and r.ano_mes between '{$nr_calendario}01' and '{$nr_calendario}12' "
                            . "group by "
                            . "    r.id_cliente "
                            . "  , r.id_servidor "
                            . "  , cast(substring(r.ano_mes from 1 for 4) as smallint) "
                            . "  , u.descricao  ";
                        
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $tot_venctos   = number_format($obj->tot_venctos,  2, ',' , '.');
                            $tot_descontos = number_format($obj->tot_descontos,  2, ',' , '.');
                            $sal_liquido   = number_format($obj->sal_liquido,  2, ',' , '.');
                            $bc_sal_fam    = number_format($obj->bc_sal_fam,  2, ',' , '.');
                            $bc_ats   = number_format($obj->bc_ats,  2, ',' , '.');
                            $bc_ferias   = number_format($obj->bc_ferias,  2, ',' , '.');
                            $bc_dec_terc   = number_format($obj->bc_dec_terc,  2, ',' , '.');
                            $bc_irrf   = number_format($obj->bc_irrf,  2, ',' , '.');

                            $id_link = trim(md5($id_cliente) . "_" . $id_cliente . "_" . $id_servidor . "_" . $obj->nr_calendario . "_" . $obj->nr_exercicio);
                            $onclick = "onclick='pdfRendimentosIRPF(this.id)'";
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr>";
                            $tabela .= "        <td><a  id='{$id_link}' href='javascript:void(0);'  title='Baixar PDF do Comprovante de Rendimento' {$onclick}>&nbsp;<i class='glyph-icon icon-file-pdf-o'></i>&nbsp;&nbsp;&nbsp;{$obj->nr_calendario}</a></tb>"; 
                            $tabela .= "        <td>{$obj->nr_exercicio}</tb>"; 
                            $tabela .= "        <td style='text-align: right;'>{$tot_venctos}</tb>";
                            $tabela .= "        <td style='text-align: right;'>{$tot_descontos}</tb>";
                            $tabela .= "        <td style='text-align: right;'>{$sal_liquido}</tb>";
                            $tabela .= "        <td style='text-align: right;'>{$bc_sal_fam}</tb>";
                            $tabela .= "        <td style='text-align: right;'>{$bc_ats}</tb>";
                            $tabela .= "        <td style='text-align: right;'>{$bc_irrf}</tb>";
                            $tabela .= "    </tr>";
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
            }
        } else {
            echo "Erro ao tentar identificar ação requistada!";
        }
    }
