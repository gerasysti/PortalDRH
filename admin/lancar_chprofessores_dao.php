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
    require_once '../lib/classes/dao.php';
    require_once '../lib/funcoes.php';
    require_once './index_dao.php';
    
    session_start();
    $hash    = (!isset($_SESSION['acesso'])?md5("Erro"):(!isset($_SESSION['acesso']['id'])?md5("Erro"):$_SESSION['acesso']['id']));
    $cliente = (!isset($_SESSION['acesso']['id_cliente'])?-1:intval($_SESSION['acesso']['id_cliente']));
    $user_id = (!isset($_SESSION['acesso']['id_usuario'])?-1:intval($_SESSION['acesso']['id_usuario']));
    
    function table_tr($lin, $obj) {
        $tabela     = "";
        $referencia = substr($obj->id_lancto, 1, strlen($obj->id_lancto) - 2); // GUID sem as chaves "{}"

        $controle        = str_pad($obj->controle, 5, "0", STR_PAD_LEFT);
        $id_cliente      = $obj->id_cliente;
        $id_unid_lotacao = $obj->id_unid_lotacao;
        $ano_mes         = (int)$obj->ano_mes;
        $data            = (!empty($obj->data)?date('d/m/Y', strtotime($obj->data) ):"&nbsp;");
        $situacao        = (int)$obj->situacao;
        $importado       = (int)$obj->importado;

        $unidade_lotacao = (!empty($obj->unidade_lotacao)?$obj->unidade_lotacao:"&nbsp;");
        $servidores      = number_format($obj->servidores, 0, ',' , '.');
        $total_ch_normal = number_format($obj->total_ch_normal, 0, ',' , '.');
        $total_ch_subst  = number_format($obj->total_ch_subst, 0, ',' , '.');
        $total_ch_outra  = number_format($obj->total_ch_outra, 0, ',' , '.');
        $total_faltas    = number_format($obj->total_faltas, 0, ',' , '.');
        
        $style = "padding-left: 1px; padding-right: 1px; padding-top: 1px; padding-bottom: 1px; ";
        $input = 
              "<input type='hidden' id='controle_{$referencia}' value='{$controle}'>"
            . "<input type='hidden' id='id_lancto_{$referencia}' value='{$obj->id_lancto}'>"
            . "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
            . "<input type='hidden' id='id_unid_lotacao_{$referencia}' value='{$id_unid_lotacao}'>"
            . "<input type='hidden' id='ano_mes_{$referencia}' value='{$ano_mes}'>"
            . "<input type='hidden' id='data_{$referencia}' value='{$data}'>"
            . "<input type='hidden' id='situacao_{$referencia}' value='{$situacao}'>"
            . "<input type='hidden' id='importado_{$referencia}' value='{$importado}'>";

        $icon_ed = "<button id='editar_lancamento_ch_{$referencia}'  class='btn btn-round btn-primary' title='Editar Registro'  onclick='editarLancamentoCH(this.id)'  style='{$style}'><i class='glyph-icon icon-edit'></i></button>";
        $icon_ex = "<button id='excluir_lancamento_ch_{$referencia}' class='btn btn-round btn-primary' title='Excluir Registro' onclick='excluirLancamentoCH(this.id)' style='{$style}'><i class='glyph-icon icon-trash'></i></button>";
        $icon_st = "";

        if ($importado === 0) {
            $icon_st = "<i class='glyph-icon icon-square-o'></i>";
        } else {
            $icon_st = "<i class='glyph-icon icon-check-square-o' title='Lançamentos Importados pelo REMUNERATU$'></i>";
        }

        // Gerar linha de registro da Consulta em página
        $tabela .= "    <tr class='custom-font-size-10' id='linha_{$referencia}'>";
        $tabela .= "        <td style='text-align: center;'>{$controle}</td>";
        $tabela .= "        <td>{$unidade_lotacao}</td>";
        $tabela .= "        <td class='numeric' style='text-align: center;'>{$servidores}</td>";
        $tabela .= "        <td class='numeric' style='text-align: right;'>{$total_ch_normal}</td>";
        $tabela .= "        <td class='numeric' style='text-align: center;'>{$total_ch_subst}</td>";
        $tabela .= "        <td class='numeric' style='text-align: center;'>{$total_ch_outra}</td>";
        $tabela .= "        <td class='numeric' style='text-align: center;'>{$total_faltas}</td>";
        $tabela .= "        <td style='text-align: center;'>{$icon_st}</td>";
        $tabela .= "        <td class='numeric' style='text-align: center;' style='{$style}'>{$icon_ed}&nbsp;{$icon_ex}{$input}</td>";
        $tabela .= "    </tr>";
        
        return $tabela;
    }
    
    function getRegistro($id_cliente, $ano_mes, $controle) {
        $retorno = false;
        $user_id = (!isset($_SESSION['acesso']['id_usuario'])?-1:intval($_SESSION['acesso']['id_usuario']));

        $cnf = Configuracao::getInstancia();
        $pdo = $cnf->db('', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 
              "Select "
            . "    mv.id_lancto "
            . "  , mv.controle "
            . "  , mv.id_cliente "
            . "  , mv.id_unid_lotacao "
            . "  , mv.ano_mes "
            . "  , mv.data "
            . "  , mv.situacao "
            . "  , mv.importado "
            . "  , lo.descricao    as unidade_lotacao "
            . "  , coalesce(lc.servidores,  0)     as servidores "
            . "  , coalesce(lc.total_ch_normal, 0) as total_ch_normal "
            . "  , coalesce(lc.total_ch_subst, 0)  as total_ch_subst "
            . "  , coalesce(lc.total_ch_outra, 0)  as total_ch_outra "
            . "  , coalesce(lc.total_faltas, 0)    as total_faltas "
            . "from REMUN_LANCTO_CH mv "
            . "  inner join ADM_USUARIO_UNID_LOTACAO y on (y.id_cliente = mv.id_cliente and y.id_unid_lotacao = mv.id_unid_lotacao and y.id_usuario = {$user_id} and y.acesso = 1) "
            . "  left join REMUN_UNID_LOTACAO lo on (lo.id_cliente = mv.id_cliente and lo.id_lotacao = mv.id_unid_lotacao) "
            . "  left join ( "
            . "    Select "
            . "        i.id_lancto  "
            . "      , i.id_cliente "
            . "      , count(i.id_servidor)           as servidores "
            . "      , sum(i.qtd_h_aula_normal)       as total_ch_normal "
            . "      , sum(i.qtd_h_aula_substituicao) as total_ch_subst "
            . "      , sum(i.qtd_h_aula_outra) as total_ch_outra "
            . "      , sum(i.qtd_falta)        as total_faltas "
            . "    from REMUN_LANCTO_CH_PROF i          "
            . "    where (i.id_cliente = {$id_cliente}) "
            . "      and (i.ano_mes    = '{$ano_mes}')  "
            . "    group by         "
            . "        i.id_lancto  "
            . "      , i.id_cliente "
            . "  ) lc on (lc.id_lancto = mv.id_lancto and lc.id_cliente = mv.id_cliente) "
            . "where (mv.id_cliente = {$id_cliente}) "
            . "  and (mv.ano_mes    = '{$ano_mes}')  "
            . "  and (mv.controle   = '{$controle}') ";

        $res = $pdo->query($sql);
        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
            $retorno = $obj;
        }

        // Fechar conexão PDO
        unset($res);
        unset($pdo);
        
        return $retorno;
    }
    
    function getRegistroServidor($id_cliente, $id_servidor) {
        $retorno = false;

        $cnf = Configuracao::getInstancia();
        $pdo = $cnf->db('', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 
              "Select "
            . "    s.* "
            . "  , coalesce(g.descricao, '* UG NÃO INFORMADA')           as unidade_gestora "
            . "  , coalesce(l.descricao, '* LOTAÇÃO NÃO INFORMADA')      as unidade_lotacao "
            . "  , coalesce(f.descricao, '* CARGO/FUNÇÃO NÃO INFORMADO') as cargo_funcao   "
            . "from REMUN_SERVIDOR s "
            . "  left join REMUN_UNID_GESTORA g on (g.id_cliente = s.id_cliente and g.id = s.id_unid_gestora)         "
            . "  left join REMUN_UNID_LOTACAO l on (l.id_cliente = s.id_cliente and l.id_lotacao = s.id_unid_lotacao) "
            . "  left join REMUN_CARGO_FUNCAO f on (f.id_cliente = s.id_cliente and f.id_cargo = s.id_cargo_atual)    "
            . "where (s.id_cliente  = {$id_cliente})  "
            . "  and (s.id_servidor = {$id_servidor}) ";

        $res = $pdo->query($sql);
        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
            $retorno = $obj;
        }

        // Fechar conexão PDO
        unset($res);
        unset($pdo);
        
        return $retorno;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ac'])) {
            switch ($_POST['ac']) {
                case 'consultar_chs_lancadas' : {
                    try {
                        $id = strip_tags( trim(filter_input(INPUT_POST, 'id')) );
                        $us = strip_tags( trim(filter_input(INPUT_POST, 'us')) );
                        $to = (int)preg_replace("/[^0-9]/", "", "0" . strip_tags( trim(filter_input(INPUT_POST, 'to')) ));
                        $lo = (int)preg_replace("/[^0-9]/", "", "0" . strip_tags( trim(filter_input(INPUT_POST, 'lo')) ));
                        $cp = (int)preg_replace("/[^0-9]/", "", "0" . strip_tags( trim(filter_input(INPUT_POST, 'cp')) ));
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'>#</th>";
                        $tabela .= "            <th>Unidade de Lotação</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>Prof.</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Normal</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Subst.</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Outras</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Faltas</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $filtro = "";
                        if ($lo !== 0) $filtro .= "  and (mv.id_unid_lotacao = {$lo}) \n";
                        
                        $sql = 
                              "Select "
                            . "    mv.id_lancto "
                            . "  , mv.controle "
                            . "  , mv.id_cliente "
                            . "  , mv.id_unid_lotacao "
                            . "  , mv.ano_mes "
                            . "  , mv.data "
                            . "  , mv.situacao "
                            . "  , mv.importado "
                            . "  , lo.descricao    as unidade_lotacao "
                            . "  , coalesce(lc.servidores,  0)     as servidores "
                            . "  , coalesce(lc.total_ch_normal, 0) as total_ch_normal "
                            . "  , coalesce(lc.total_ch_subst, 0)  as total_ch_subst "
                            . "  , coalesce(lc.total_ch_outra, 0)  as total_ch_outra "
                            . "  , coalesce(lc.total_faltas, 0)    as total_faltas "
                            . "from REMUN_LANCTO_CH mv "
                            . "  inner join ADM_USUARIO_UNID_LOTACAO y on (y.id_cliente = mv.id_cliente and y.id_unid_lotacao = mv.id_unid_lotacao and y.id_usuario = {$user_id} and y.acesso = 1) "
                            . "  left join REMUN_UNID_LOTACAO lo on (lo.id_cliente = mv.id_cliente and lo.id_lotacao = mv.id_unid_lotacao) "
                            . "  left join ( "
                            . "    Select "
                            . "        i.id_lancto "
                            . "      , i.id_cliente "
                            . "      , count(i.id_servidor)           as servidores "
                            . "      , sum(i.qtd_h_aula_normal)       as total_ch_normal "
                            . "      , sum(i.qtd_h_aula_substituicao) as total_ch_subst "
                            . "      , sum(i.qtd_h_aula_outra) as total_ch_outra "
                            . "      , sum(i.qtd_falta)        as total_faltas "
                            . "    from REMUN_LANCTO_CH_PROF i    "
                            . "    where (i.id_cliente = {$to})   "
                            . "      and (i.ano_mes    = '{$cp}') "
                            . "    group by "
                            . "        i.id_lancto "
                            . "      , i.id_cliente "
                            . "  ) lc on (lc.id_lancto = mv.id_lancto and lc.id_cliente = mv.id_cliente) "
                            . "where (mv.id_cliente = {$to})   "
                            . "  and (mv.ano_mes    = '{$cp}') "
                            . $filtro 
                            . "order by "
                            . "    mv.data      desc "
                            . "  , lo.descricao asc  ";
                            
                        $lin = 1;    
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $tabela .= table_tr($lin, $obj);
                            $lin += 1;
                        }
                        
                        // Fechar conexão PDO
                        unset($res);
                        unset($pdo);
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'carregar_lancamento_professores' : {
                    try {
                        $op = trim(filter_input(INPUT_POST, 'op'));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        $controle   = floatval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'controle'))) );
                        $id_lancto  = strip_tags( strtoupper(trim($_POST['id_lancto'])) );
                        $id_cliente = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente'))) );
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<p>&nbsp;</p>";
                        $tabela .= "<table id='datatable-responsive_prof' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'>#</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'>ID</th>";
                        $tabela .= "            <th>Nome</th>";
                        $tabela .= "            <th>Cargo/Função</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Normal</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Subst.</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Outras</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Faltas</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        $sql = 
                              "Select \n"
                            . "    lc.id_lancto_prof \n"
                            . "  , lc.id_lancto      \n"
                            . "  , mv.situacao       \n"
                            . "  , lc.id_cliente     \n"
                            . "  , lc.id_servidor    \n"
                            . "  , sv.nome           \n"
                            . "  , coalesce(cf.descricao, '* CARGO/FUNÇÃO NÃO INFORMADO') as cargo_funcao \n"
                            . "  , lc.id_unid_lotacao \n"
                            . "  , lc.ano_mes \n"
                            . "  , lc.qtd_h_aula_normal \n"
                            . "  , lc.qtd_h_aula_substituicao \n"
                            . "  , lc.qtd_h_aula_outra \n"
                            . "  , lc.qtd_falta \n"
                            . "  , lc.observacao \n"
                            . "  , lc.calc_grat_series_iniciais \n"
                            . "  , lc.calc_grat_dificil_acesso \n"
                            . "  , lc.calc_grat_ensino_espec \n"
                            . "  , lc.calc_grat_multi_serie \n"
                            . "from REMUN_LANCTO_CH mv \n"
                            . "  inner join REMUN_LANCTO_CH_PROF lc on (lc.id_lancto = mv.id_lancto and lc.id_cliente = mv.id_cliente) \n"
                            . "  left join REMUN_SERVIDOR sv on (sv.id_cliente = lc.id_cliente and sv.id_servidor = lc.id_servidor) \n"
                            . "  left join REMUN_CARGO_FUNCAO cf on (cf.id_cliente = sv.id_cliente and cf.id_cargo = sv.id_cargo_atual) \n"
                            . " \n"
                            . "where (mv.id_cliente = {$id_cliente})  \n"
                            . "  and (mv.id_lancto  = '{$id_lancto}') \n"
                            . "  and (mv.controle   = {$controle})    \n"
                            . " \n"
                            . "order by \n"
                            . "    lc.id_cliente \n"
                            . "  , lc.id_unid_lotacao \n"
                            . "  , lc.ano_mes \n"
                            . "  , sv.nome \n";
                        
                        $qtde_professores  = 1;
                        $total_servidores = 0.0;
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = substr($obj->id_lancto_prof, 1, strlen($obj->id_lancto_prof) - 2); // GUID sem as chaves "{}"
                            
                            $style = "padding-left: 1px; padding-right: 1px; padding-top: 1px; padding-bottom: 1px; ";
                            $input = 
                                  "<input type='hidden' id='id_lancto_prof_{$referencia}'  value='{$obj->id_lancto_prof}'>"
                                . "<input type='hidden' id='id_lancto_{$referencia}'       value='{$obj->id_lancto}'>"
                                . "<input type='hidden' id='id_cliente_{$referencia}'      value='{$obj->id_cliente}'>"
                                . "<input type='hidden' id='id_servidor_{$referencia}'     value='{$obj->id_servidor}'>"
                                . "<input type='hidden' id='id_unid_lotacao_{$referencia}' value='{$obj->id_unid_lotacao}'>"
                                . "<input type='hidden' id='ano_mes_{$referencia}'         value='{$obj->ano_mes}'>"
                                . "<input type='hidden' id='observacao_{$referencia}'                value='{$obj->observacao}'>"
                                . "<input type='hidden' id='calc_grat_series_iniciais_{$referencia}' value='{$obj->calc_grat_series_iniciais}'>"
                                . "<input type='hidden' id='calc_grat_dificil_acesso_{$referencia}'  value='{$obj->calc_grat_dificil_acesso}'>"
                                . "<input type='hidden' id='calc_grat_ensino_espec_{$referencia}'    value='{$obj->calc_grat_ensino_espec}'>"
                                . "<input type='hidden' id='calc_grat_multi_serie_{$referencia}'     value='{$obj->calc_grat_multi_serie}'>";
                            /*    
                            $readonly  = ((int)$obj->situacao !== 0?"readonly":"");
                            $proximo   = ((int)$obj->situacao !== 0?"":"proximo_campo");
                            
                            $chn = 
                                  "<input type='text' class='form-control text lg-text {$proximo}' maxlength='10' id='qtd_h_aula_normal_{$referencia}' onchange='salvar_lancamento_professor(this.id, 0)' "
                                . "value='" . number_format($obj->qtd_h_aula_normal, 0, ',' , '.') . "' "
                                . "style='text-align: right; margin: 0px; border: 0; background-color:transparent; width: 100%; height: 50px; {$readonly}'>";
                            $chs = 
                                  "<input type='text' class='form-control text lg-text {$proximo}' maxlength='10' id='qtd_h_aula_substituicao_{$referencia}' onchange='salvar_lancamento_professor(this.id, 0)' "
                                . "value='" . number_format($obj->qtd_h_aula_substituicao, 0, ',' , '.') . "' "
                                . "style='text-align: right; margin: 0px; border: 0; background-color:transparent; width: 100%; height: 50px; {$readonly}'>";
                            $cho = 
                                  "<input type='text' class='form-control text lg-text {$proximo}' maxlength='10' id='qtd_h_aula_outra_{$referencia}' onchange='salvar_lancamento_professor(this.id, 0)' "
                                . "value='" . number_format($obj->qtd_h_aula_outra, 0, ',' , '.') . "' "
                                . "style='text-align: right; margin: 0px; border: 0; background-color:transparent; width: 100%; height: 50px; {$readonly}'>";
                            $qtf = 
                                  "<input type='text' class='form-control text lg-text {$proximo}' maxlength='10' id='qtd_falta_{$referencia}' onchange='salvar_lancamento_professor(this.id, 0)' "
                                . "value='" . number_format($obj->qtd_falta, 0, ',' , '.') . "' "
                                . "style='text-align: right; margin: 0px; border: 0; background-color:transparent; width: 100%; height: 50px; {$readonly}'>";
                            $icon_ex = "<button id='excluir_professor_lancamento_{$referencia}' class='btn btn-sm btn-round btn-primary excluir_professor' title='Excluir Registro' onclick='excluirLancamentoProfessor(this.id)' style='{$style}'><i class='glyph-icon icon-trash'></i></button>";
                            */
                            $tabela .= "    <tr class='custom-font-size-10' id='linha_professor_{$referencia}'>";
                            $tabela .= "        <td style='text-align: center;'>{$qtde_servidores}</td>";
                            $tabela .= "        <td style='text-align: center;'>" . str_pad($obj->id_servidor, 7, "0", STR_PAD_LEFT) . "</td>";
                            $tabela .= "        <td>{$obj->nome}</td>";
                            $tabela .= "        <td>{$obj->cargo_funcao}</td>";
//                            $tabela .= "        <td style='text-align: right; margin: 0px; padding: 0px;'>{$chn}</td>";
//                            $tabela .= "        <td style='text-align: right; margin: 0px; padding: 0px;'>{$chs}</td>";
//                            $tabela .= "        <td style='text-align: right; margin: 0px; padding: 0px;'>{$cho}</td>";
//                            $tabela .= "        <td style='text-align: right; margin: 0px; padding: 0px;'>{$qtf}</td>";
                            $tabela .= "        <td style='text-align: right;'>" . number_format($obj->qtd_h_aula_normal, 0, ',' , '.') . "</td>";
                            $tabela .= "        <td style='text-align: right;'>" . number_format($obj->qtd_h_aula_substituicao, 0, ',' , '.') . "</td>";
                            $tabela .= "        <td style='text-align: right;'>" . number_format($obj->qtd_h_aula_outra, 0, ',' , '.') . "</td>";
                            $tabela .= "        <td style='text-align: right;'>" . number_format($obj->qtd_falta, 0, ',' , '.') . "</td>";
                            $tabela .= "        <td style='text-align: center;' style='{$style}'>{$icon_ex}{$input}</td>";
                            $tabela .= "    </tr>";
                            
                            $qtde_professores += 1;
                        }
                        
                        // Fechar conexão PDO
                        unset($res);
                        unset($pdo);
                        
                        $qtde_professores -= 1;
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>\n";
                        $tabela .= "<input type='hidden' id='qtde_professores'  value='{$qtde_professores}'>\n";

                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'carregar_evento' : {
                    try {
                        
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'gravar_lancamento_cabecalho' : {
                    try {
                        $op = trim(filter_input(INPUT_POST, 'op'));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        $controle   = floatval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'controle'))) );
                        $id_lancto  = strip_tags( trim(filter_input(INPUT_POST, 'id_lancto')) );
                        $id_cliente = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente'))) );
                        $ano_mes    = strip_tags( trim(filter_input(INPUT_POST, 'ano_mes')) );
                        $id_unid_lotacao = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unid_lotacao'))) );
                        $data = strip_tags( trim(filter_input(INPUT_POST, 'data')) );
                        $hora = strip_tags( trim(filter_input(INPUT_POST, 'hora')) );
                        
                        $file = '../downloads/lanc_chs_' . $hs . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else {
                            // Verificar se já existe lançamento de Carga Horária para o cliente no Ano/Mês/Lotação
                            $sql = 
                                  "Select "
                                . "    mv.controle  "
                                . "  , mv.id_lancto "
                                . "  , mv.data      "
                                . "  , mv.usuario   "
                                . "  , us.nome      "
                                . "from REMUN_LANCTO_CH mv "
                                . "  left join ADM_USUARIO us on (us.id = mv.usuario) "
                                . "where (mv.id_lancto <> '{$id_lancto}') "
                                . "  and (mv.id_cliente = {$id_cliente})  "
                                . "  and (mv.ano_mes    = '{$ano_mes}')   "
                                . "  and (mv.id_unid_lotacao = {$id_unid_lotacao}) "
                                . "  and (mv.situacao       <> 2) "; // 2 - Cancelado
                            
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $res = $pdo->query($sql);
                            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                $num = str_pad($obj->controle, 5, "0", STR_PAD_LEFT);
                                $dat = date('d/m/Y', strtotime($obj->data));
                                echo "Já existem o lançamento de número <strong><code>{$num}</code></strong> feito no dia <strong><code>{$dat}</code></strong> pelo usuário <strong><code>{$obj->nome}</code></strong>.";
                            } else {
                                if ($op === "novo_lancamento") {
                                    $dao = Dao::getInstancia();
                                    $controle  = $dao->getGeneratorID("GEN_LANCTO_CH");
                                    $id_lancto = $dao->getGuidIDFormat();
                                    
                                    // Inserir Lançamento
                                    $stm = $pdo->prepare(
                                          "Insert Into REMUN_LANCTO_CH ("
                                        . "    controle         "
                                        . "  , id_lancto        "
                                        . "  , id_cliente       "
                                        . "  , id_unid_lotacao  "
                                        . "  , ano_mes          "
                                        . "  , data             "
                                        . "  , hora             "
                                        . "  , usuario          "
                                        . "  , situacao         "
                                        . "  , importado        "
                                        . ") values ("
                                        . "    :controle         "
                                        . "  , :id_lancto        "
                                        . "  , :id_cliente       "
                                        . "  , :id_unid_lotacao  "
                                        . "  , :ano_mes          "
                                        . "  , current_date      "
                                        . "  , current_time      "
                                        . "  , :usuario          "
                                        . "  , 0 "
                                        . "  , 0 "
                                        . ")");
                                    $stm->execute(array(
                                        ':controle'         => $controle,
                                        ':id_lancto'        => $id_lancto,
                                        ':id_cliente'       => $id_cliente,
                                        ':id_unid_lotacao'  => $id_unid_lotacao,
                                        ':ano_mes'          => $ano_mes,
                                        ':usuario'          => $user_id
                                    ));
                                    $pdo->commit();
                                } else
                                if ($op === "editar_lancamento") {
                                    $stm = $pdo->prepare(
                                          "Update REMUN_LANCTO_CH mv Set            "
                                        . "    mv.id_unid_lotacao  = :id_unid_lotacao   "
                                        . "  , mv.ano_mes          = :ano_mes           "
                                        . "where (mv.id_lancto = :id_lancto) "
                                        . "  and (mv.situacao  = 0)  "
                                        . "  and (mv.importado = 0)  ");
                                    $stm->execute(array(
                                        ':id_lancto'        => $id_lancto,
                                        ':id_unid_lotacao'  => $id_unid_lotacao,
                                        ':ano_mes'          => $ano_mes
                                    ));
                                    $pdo->commit();
                                } 

                                $obj = getRegistro($id_cliente, $ano_mes, $controle);
                                
                                $registros = array('form' => array());
                                $registros['form'][0]['referencia'] = substr($id_lancto, 1, strlen($id_lancto) - 2); // GUID sem as chaves "{}"
                                $registros['form'][0]['id_lancto']  = $id_lancto;
                                $registros['form'][0]['controle']   = str_pad($controle, 5, "0", STR_PAD_LEFT);
                                $registros['form'][0]['ano_mes']    = $ano_mes;
                                $registros['form'][0]['data']       = $data;
                                $registros['form'][0]['hora']       = $hora;
                                $registros['form'][0]['servidores'] = $obj->servidores;
                                $registros['form'][0]['table_tr']   = table_tr($controle, $obj);

                                $json = json_encode($registros);
                                file_put_contents($file, $json);

                                echo "OK";
                            }
                        
                            // Fechar conexão PDO
                            unset($res);
                            unset($pdo);
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;

                case 'grava_lancamento_ch_servidor' : {
                    try {
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        
                        $id_lancto   = strip_tags( trim(filter_input(INPUT_POST, 'id_lancto')) );
                        $controle    = floatval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'controle'))) );
                        $id_cliente  = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente'))) );
                        $id_servidor = floatval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_servidor'))) );
                        $id_escola   = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_escola'))) );
                        $ano_mes     = strip_tags( trim(filter_input(INPUT_POST, 'ano_mes')) );
                        $sequencia   = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'sequencia'))) );
                        $qtde_hora_aula_normal     = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'qtde_hora_aula_normal')) );
                        $qtde_hora_aula_subst      = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'qtde_hora_aula_subst')) );
                        $qtde_hora_aula_outras     = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'qtde_hora_aula_outras')) );
                        $qtde_falta                = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'qtde_falta')) );
                        $calc_grat_series_iniciais = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'calc_grat_series_iniciais')) ));
                        $calc_grat_ensino_esp      = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'calc_grat_ensino_esp')) ));
                        $calc_grat_dificio_acesso  = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'calc_grat_dificio_acesso')) ));
                        $calc_grat_multi_serie     = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'calc_grat_multi_serie')) ));
                        $observacao  = strip_tags( trim(filter_input(INPUT_POST, 'observacao')) );
                        
                        if ($observacao === "") { $observacao = null; }
                        
                        $file = '../downloads/lanc_ch_srv_' . $hs . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else {
                            $stm = $pdo->prepare(
                                  "Execute procedure SET_CARGA_HORARIA_PROFESSOR ( "
                                . "    :id_lancto   "
                                . "  , :id_cliente  "
                                . "  , :id_servidor "
                                . "  , :id_escola   "
                                . "  , :ano_mes     "
                                . "  , :qtde_ch_normal       "
                                . "  , :qtde_ch_substituicao "
                                . "  , :qtde_ch_outras "
                                . "  , :qtde_faltas    "
                                . "  , :observacao     "
                                . "  , :calc_grat_series_iniciais "
                                . "  , :calc_grat_dificil_acesso  "
                                . "  , :calc_grat_ensino_espec    "
                                . "  , :calc_grat_multi_serie     "
                                . ")");
                            $stm->execute(array(
                                  ':id_lancto'   => $id_lancto
                                , ':id_cliente'  => $id_cliente
                                , ':id_servidor' => $id_servidor
                                , ':id_escola'   => $id_escola
                                , ':ano_mes'     => $ano_mes
                                , ':qtde_ch_normal'       => $qtde_hora_aula_normal
                                , ':qtde_ch_substituicao' => $qtde_hora_aula_subst
                                , ':qtde_faltas'          => $qtde_falta
                                , ':observacao'           => $observacao
                                , ':calc_grat_series_iniciais' => $calc_grat_series_iniciais
                                , ':calc_grat_dificil_acesso'  => $calc_grat_dificil_acesso
                                , ':calc_grat_ensino_espec'    => $calc_grat_ensino_espec
                                , ':calc_grat_multi_serie'     => $calc_grat_multi_serie
                            ));
                            $pdo->commit();
                        }

                        // Fechar conexão PDO
                        unset($pdo);

                        $referencia = $controle . "_" . $sequencia;
                        $servidor   = getRegistroServidor($id_cliente, $id_servidor);

                        $style = "padding-left: 1px; padding-right: 1px; padding-top: 1px; padding-bottom: 1px; ";
                        $input = 
                              "<input type='hidden' id='controle_{$referencia}' value='{$controle}'>"
                            . "<input type='hidden' id='sequencia_{$referencia}' value='{$sequencia}'>"
                            . "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
                            . "<input type='hidden' id='id_unid_gestora_{$referencia}' value='{$id_unid_gestora}'>"
                            . "<input type='hidden' id='id_unid_lotacao_{$referencia}' value='{$id_unid_lotacao}'>"
                            . "<input type='hidden' id='id_evento_{$referencia}' value='{$id_evento}'>"
                            . "<input type='hidden' id='ano_mes_{$referencia}' value='{$ano_mes}'>"
                            . "<input type='hidden' id='id_servidor_{$referencia}' value='{$id_servidor}'>";

                        $qt_readonly  = ""; // ((int)$obj->situacao !== 0?"readonly":((int)$tipo_lancamento === 1?"readonly":""));
                        $vl_readonly  = ""; // ((int)$obj->situacao !== 0?"readonly":((int)$tipo_lancamento === 0?"readonly":""));

                        $qt_proximo = "proximo_campo"; // ((int)$obj->situacao !== 0?"":((int)$tipo_lancamento === 0?"proximo_campo":""));
                        $vl_proximo = "proximo_campo"; // ((int)$obj->situacao !== 0?"":((int)$tipo_lancamento === 1?"proximo_campo":""));

                        $quant_out = 
                              "<input type='text' class='form-control text lg-text {$qt_proximo}' maxlength='10' id='quant_{$referencia}' onchange='salvar_lancamento_servidor(this.id, 0)' "
                            . "value='" . (($quant !== null) && ($quant !== '')?number_format($quant, 0, ',' , '.'):"0") . "' "
                            . "style='text-align: right; margin: 0px; border: 0; background-color:transparent; width: 100%; height: 50px; {$qt_readonly}'>";
                        $valor_out = 
                              "<input type='text' class='form-control text lg-text {$vl_proximo}' maxlength='10' id='valor_{$referencia}' onchange='salvar_lancamento_servidor(this.id, 0)' "
                            . "value='" . (($valor !== null) && ($valor !== '')?number_format($valor, 2, ',' , '.'):"0,00") . "' "
                            . "style='text-align: right; margin: 0px; border: 0; background-color:transparent; width: 100%; height: 50px;' {$vl_readonly}>";
                        $icon_ex = "<button id='excluir_servidor_lancamento_{$referencia}' class='btn btn-sm btn-round btn-primary excluir_servidor' title='Excluir Registro' onclick='excluirLancamentoServidor(this.id)' style='{$style}'><i class='glyph-icon icon-trash'></i></button>";

                        $tr_table  = "    <tr class='custom-font-size-10' id='linha_servidor_{$referencia}'>";
                        $tr_table .= "        <td style='text-align: center;'>{$sequencia}</td>";
                        $tr_table .= "        <td style='text-align: center;'>" . str_pad($id_servidor, 7, "0", STR_PAD_LEFT) . "</td>";
                        $tr_table .= "        <td>{$servidor->nome}</td>";
                        $tr_table .= "        <td>{$servidor->cargo_funcao}</td>";
                        $tr_table .= "        <td style='text-align: right; margin: 0px; padding: 0px;'>{$quant_out}</td>";
                        $tr_table .= "        <td style='text-align: right; margin: 0px; padding: 0px;'>{$valor_out}</td>";
                        $tr_table .= "        <td style='text-align: center;' style='{$style}'>{$icon_ex}{$input}</td>";
                        $tr_table .= "    </tr>";
                        
                        $registros = array('form' => array());
                        $registros['form'][0]['referencia']  = $referencia;
                        $registros['form'][0]['controle']    = $controle;
                        $registros['form'][0]['sequencia']   = $sequencia;
                        $registros['form'][0]['id_servidor'] = $id_servidor;
                        $registros['form'][0]['table_tr']    = $tr_table;

                        $json = json_encode($registros);
                        file_put_contents($file, $json);
                        
                        echo "OK";
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;

                case 'lancamento_ch_servidor' : {
                    try {
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $to = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'to'))) ); // Cliente
                        $sv = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'sv'))) ); // Servidor
                        $lo = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'lo'))) ); // Escola
                        $cp = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cp'))) ); // Competência - ano/mês
                        $id = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id'))) ); // Controle

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql =
                              "Select "
                            . "  count(lanc.id_lancto_prof) as lancamentos "
                            . "from REMUN_LANCTO_CH_PROF lanc "
                            . "where (id_cliente      = {$to})    "
                            . "  and (id_servidor     = {$sv})    " 
                            . "  and (id_unid_lotacao = {$lo})    "
                            . "  and (ano_mes         = '{$cp}')  ";
                            
                        $qry = $pdo->query($sql);
                        if (($obj = $qry->fetch(PDO::FETCH_OBJ)) !== false) {
                            if ((int)$obj->lancamentos !== 0) {
                                echo "OK";
                            } else {
                                echo "NO";
                            }
                        } else {
                            echo "NO";
                        }
                        
                        unset($qry);
                        unset($pdo);
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'excluir_lancamento_servidor' : {
                    try {
                        $to = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'to'))) ); // Cliente
                        $ug = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ug'))) );
                        $lo = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'lo'))) );
                        $ev = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ev'))) ); // Competência - ano/mês
                        $cp = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cp'))) );
                        $id = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id'))) ); // Controle
                        $sv = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'sv'))) ); // Servidor
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        
                        $obj = getRegistro($to, $cp, $id);
                        if ((int)$obj->importado === 1) {
                            echo "Este lançamento já foi <strong>importado</strong> para o sistema de folha Remuneratus$ na central e não poderá ser excluído.<br>Entre em contato com a direção.";
                        } else 
                        if ((int)$obj->situacao !== 0) {
                            echo "Este lançamento está <strong>" . ((int)$obj->situacao === 1?"finalizado":"cancelado") . "</strong> e não poderá ser excluído.<br>Entre em contato com a direção.";
                        } else {
                            if ($hs !== $hash) {
                                echo "Acesso Inválido";
                            } else {
                                $cnf = Configuracao::getInstancia();
                                $pdo = $cnf->db('', '');
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                $stm = $pdo->prepare(
                                      "Delete from REMUN_EVENTO_AVULSO_ITEM       "
                                    . "where (id_cliente      = :id_cliente)      "
                                    . "  and (id_unid_gestora = :id_unid_gestora) "
                                    . "  and (id_unid_lotacao = :id_unid_lotacao) "
                                    . "  and (id_evento       = :id_evento)       "
                                    . "  and (ano_mes         = :ano_mes)         "
                                    . "  and (id_servidor     = :id_servidor)     ");
                                $stm->execute(array(
                                      ':id_cliente'       => $to
                                    , ':id_unid_gestora'  => $ug
                                    , ':id_unid_lotacao'  => $lo
                                    , ':id_evento'        => $ev
                                    , ':ano_mes'          => $cp
                                    , ':id_servidor'      => $sv
                                ));

                                $pdo->commit();

                                // Fechar conexão PDO
                                unset($stm);
                                unset($pdo);
                                
                                echo "OK";
                            }
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'excluir_lancamento_ch' : {
                    try {
                        $to = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'to'))) );
                        $lo = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'lo'))) );
                        $cp = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cp'))) );
                        $ct = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ct'))) );
                        $id = "{" . strip_tags( trim(filter_input(INPUT_POST, 'id')) ) . "}";
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        
                        $obj = getRegistro($to, $cp, $ct);
                        if ((int)$obj->importado === 1) {
                            echo "Este lançamento já foi <strong>importado</strong> para o sistema de folha Remuneratus$ na central e não poderá ser excluído.<br>Entre em contato com a direção.";
                        } else 
                        if ((int)$obj->situacao !== 0) {
                            echo "Este lançamento está <strong>" . ((int)$obj->situacao === 1?"finalizado":"cancelado") . "</strong> e não poderá ser excluído.<br>Entre em contato com a direção.";
                        } else {
                            if ($hs !== $hash) {
                                echo "Acesso Inválido";
                            } else {
                                $cnf = Configuracao::getInstancia();
                                $pdo = $cnf->db('', '');
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                $stm = $pdo->prepare(
                                      "Delete from REMUN_LANCTO_CH "
                                    . "where (id_cliente = :id_cliente)"
                                    . "  and (id_lancto  = :id_lancto) ");
                                $stm->execute(array(
                                      ':id_cliente' => $to
                                    , ':id_lancto'  => $id
                                ));

                                $pdo->commit();

                                // Fechar conexão PDO
                                unset($stm);
                                unset($pdo);
                                
                                echo "OK";
                            }
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'situacao_lancamento_evento' : {
                    try {
                        $to = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'to'))) );
                        $ug = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ug'))) );
                        $lo = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'lo'))) );
                        $ev = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ev'))) );
                        $cp = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cp'))) );
                        $id = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id'))) );
                        $st = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'st'))) );
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        
                        $obj = getRegistro($to, $cp, $id);
                        if ((int)$obj->importado === 1) {
                            echo "Este lançamento já foi <strong>importado</strong> para o sistema de folha Remuneratus$ na central e não poderá ser excluído.<br>Entre em contato com a direção.";
                        } else 
                        if ((int)$obj->situacao === 2) {
                            echo "Este lançamento está <strong>cancelado</strong> e não poderá ser alterado.<br>Entre em contato com a direção.";
                        } else {
                            if ($hs !== $hash) {
                                echo "Acesso Inválido";
                            } else {
                                $cnf = Configuracao::getInstancia();
                                $pdo = $cnf->db('', '');
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                $stm = $pdo->prepare(
                                      "Update REMUN_EVENTO_AVULSO Set "
                                    . "  situacao = {$st} "
                                    . "where (id_cliente      = :id_cliente)"
                                    . "  and (id_unid_gestora = :id_unid_gestora)"
                                    . "  and (id_unid_lotacao = :id_unid_lotacao)"
                                    . "  and (id_evento       = :id_evento)"
                                    . "  and (ano_mes         = :ano_mes) ");
                                $stm->execute(array(
                                      ':id_cliente'       => $to
                                    , ':id_unid_gestora'  => $ug
                                    , ':id_unid_lotacao'  => $lo
                                    , ':id_evento'        => $ev
                                    , ':ano_mes'          => $cp
                                ));

                                $pdo->commit();
                                
                                // Fechar conexão PDO
                                unset($stm);
                                unset($pdo);
                                
                                echo "OK";
                            }
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
            }
        } else {
            echo "Erro ao tentar identificar ação requistada!";
        }
    }
