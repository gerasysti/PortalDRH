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
        //$referencia = $obj->id_unid_gestora . "_" . $obj->id_unid_lotcao . "_" . $obj->id_evento . "_" . $obj->ano_mes;
        $referencia = $obj->controle;

        $controle        = str_pad($obj->controle, 5, "0", STR_PAD_LEFT);
        $id_cliente      = $obj->id_cliente;
        $id_unid_gestora = $obj->id_unid_gestora;
        $id_unid_lotacao = $obj->id_unid_lotacao;
        $id_evento       = $obj->id_evento; 
        $ano_mes         = (int)$obj->ano_mes;
        $data            = (!empty($obj->data)?date('d/m/Y', strtotime($obj->data) ):"&nbsp;");
        $situacao        = (int)$obj->situacao;
        $importado       = (int)$obj->importado;

        $rubrica = (!empty($obj->rubrica)?trim($obj->rubrica):"&nbsp;");
        $tipo    = (!empty($obj->tipo)?trim($obj->tipo):"D");
        $tipo_lancamento = (!empty($obj->tipo_lancamento)?$obj->tipo_lancamento:"0");
        $unidade_gestora = (!empty($obj->unidade_gestora)?$obj->unidade_gestora:"&nbsp;");
        $unidade_lotacao = (!empty($obj->unidade_lotacao)?$obj->unidade_lotacao:"&nbsp;");
        $evento = (!empty($obj->evento)?$obj->evento:"&nbsp;");
        $total  = ((int)$tipo_lancamento === 0?number_format($obj->total_quant, 0, ',' , '.'):number_format($obj->total_valor, 2, ',' , '.'));

        $style = "padding-left: 1px; padding-right: 1px; padding-top: 1px; padding-bottom: 1px; ";
        $input = 
              "<input type='hidden' id='controle_{$referencia}' value='{$controle}'>"
            . "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
            . "<input type='hidden' id='id_unid_gestora_{$referencia}' value='{$id_unid_gestora}'>"
            . "<input type='hidden' id='id_unid_lotacao_{$referencia}' value='{$id_unid_lotacao}'>"
            . "<input type='hidden' id='id_evento_{$referencia}' value='{$id_evento}'>"
            . "<input type='hidden' id='tipo_lancamento_{$referencia}' value='{$tipo_lancamento}'>"
            . "<input type='hidden' id='ano_mes_{$referencia}' value='{$ano_mes}'>"
            . "<input type='hidden' id='data_{$referencia}' value='{$data}'>"
            . "<input type='hidden' id='situacao_{$referencia}' value='{$situacao}'>"
            . "<input type='hidden' id='importado_{$referencia}' value='{$importado}'>";

        $icon_ed = "<button id='editar_evento_lancamento_{$referencia}'  class='btn btn-round btn-primary' title='Editar Registro'  onclick='editarLancamentoEvento(this.id)'  style='{$style}'><i class='glyph-icon icon-edit'></i></button>";
        $icon_ex = "<button id='excluir_evento_lancamento_{$referencia}' class='btn btn-round btn-primary' title='Excluir Registro' onclick='excluirLancamentoEvento(this.id)' style='{$style}'><i class='glyph-icon icon-trash'></i></button>";
        $icon_st = "";

        if ($importado === 0) {
            $icon_st = "<i class='glyph-icon icon-square-o'></i>";
        } else {
            $icon_st = "<i class='glyph-icon icon-check-square-o' title='Lançamentos Importados pelo REMUNERATU$'></i>";
        }

        // Gerar linha de registro da Consulta em página
        $tabela .= "    <tr class='custom-font-size-10' id='linha_{$referencia}'>";
        $tabela .= "        <td style='text-align: center;'>{$controle}</td>";
        $tabela .= "        <td>{$unidade_gestora}</td>";
        $tabela .= "        <td>{$unidade_lotacao}</td>";
        $tabela .= "        <td style='text-align: center;'>{$rubrica}</td>";
        $tabela .= "        <td>{$evento}</td>";
        $tabela .= "        <td style='text-align: center;'>{$tipo}</td>";
        $tabela .= "        <td class='numeric' style='text-align: center;'>{$obj->servidores}</td>";
        $tabela .= "        <td class='numeric' style='text-align: right;'>{$total}</td>";
        $tabela .= "        <td style='text-align: center;'>{$icon_st}</td>";
        $tabela .= "        <td style='text-align: center;' style='{$style}'>{$icon_ed}&nbsp;{$icon_ex}{$input}</td>";
        $tabela .= "    </tr>";
        
        return $tabela;
    }
    
    function getRegistro($id_cliente, $ano_mes, $controle) {
        $retorno = false;

        $cnf = Configuracao::getInstancia();
        $pdo = $cnf->db('', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 
              "Select "
            . "    mv.controle   "
            . "  , mv.id_cliente "
            . "  , mv.id_unid_gestora "
            . "  , mv.id_unid_lotacao "
            . "  , mv.id_evento "
            . "  , mv.ano_mes "
            . "  , mv.data "
            . "  , mv.situacao "
            . "  , mv.importado "
            . "  , mv.id_evento as id "
            . "  , ev.codigo    as rubrica "
            . "  , ev.descricao as evento "
            . "  , ev.tipo "
            . "  , ev.tipo_lancamento "
            . "  , ug.razao_social as unidade_gestora "
            . "  , lo.descricao    as unidade_lotacao "
            . "  , coalesce(lc.servidores,  0) as servidores "
            . "  , coalesce(lc.total_quant, 0) as total_quant "
            . "  , coalesce(lc.total_valor, 0) as total_valor "
            . "from REMUN_EVENTO_AVULSO mv "
            . "  left join REMUN_UNID_GESTORA ug on (ug.id_cliente = mv.id_cliente and ug.id = mv.id_unid_gestora) "
            . "  left join REMUN_UNID_LOTACAO lo on (lo.id_cliente = mv.id_cliente and lo.id_lotacao = mv.id_unid_lotacao) "
            . "  left join REMUN_EVENTO ev on (ev.id_cliente = mv.id_cliente and ev.id_evento = mv.id_evento) "
            . "  left join ( "
            . "    Select "
            . "        i.id_cliente "
            . "      , i.id_unid_gestora "
            . "      , i.id_unid_lotacao "
            . "      , i.id_evento "
            . "      , i.ano_mes "
            . "      , count(i.id_servidor) as servidores "
            . "      , sum(i.quant) as total_quant "
            . "      , sum(i.valor) as total_valor "
            . "    from REMUN_EVENTO_AVULSO_ITEM i "
            . "    where (i.id_cliente = {$id_cliente}) "
            . "      and (i.ano_mes    = '{$ano_mes}')  "
            . "    group by "
            . "        i.id_cliente "
            . "      , i.id_unid_gestora "
            . "      , i.id_unid_lotacao "
            . "      , i.id_evento "
            . "      , i.ano_mes "
            . "  ) lc on (lc.id_cliente      = mv.id_cliente "
            . "       and lc.id_unid_gestora = mv.id_unid_gestora "
            . "       and lc.id_unid_lotacao = mv.id_unid_lotacao "
            . "       and lc.id_evento       = mv.id_evento "
            . "       and lc.ano_mes         = mv.ano_mes "
            . "  ) "
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
                case 'consultar_eventos_lancados' : {
                    try {
                        $id = strip_tags( trim(filter_input(INPUT_POST, 'id')) );
                        $us = strip_tags( trim(filter_input(INPUT_POST, 'us')) );
                        $to = (int)preg_replace("/[^0-9]/", "", "0" . strip_tags( trim(filter_input(INPUT_POST, 'to')) ));
                        $ug = (int)preg_replace("/[^0-9]/", "", "0" . strip_tags( trim(filter_input(INPUT_POST, 'ug')) ));
                        $lo = (int)preg_replace("/[^0-9]/", "", "0" . strip_tags( trim(filter_input(INPUT_POST, 'lo')) ));
                        $cp = (int)preg_replace("/[^0-9]/", "", "0" . strip_tags( trim(filter_input(INPUT_POST, 'cp')) ));
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'>#</th>";
                        $tabela .= "            <th>UG</th>";
                        $tabela .= "            <th>Lotação</th>";
                        $tabela .= "            <th>Rubrica</th>";
                        $tabela .= "            <th>Evento</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'>Tipo</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>Serv.</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Total</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $filtro = "";
                        if ($ug !== 0) $filtro .= "  and (mv.id_unid_gestora = {$ug}) \n";
                        if ($lo !== 0) $filtro .= "  and (mv.id_unid_lotacao = {$lo}) \n";
                        
                        $ln  = "";
                        $sql = 
                              "Select "
                            . "    mv.controle   "
                            . "  , mv.id_cliente "
                            . "  , mv.id_unid_gestora "
                            . "  , mv.id_unid_lotacao "
                            . "  , mv.id_evento "
                            . "  , mv.ano_mes "
                            . "  , mv.data "
                            . "  , mv.situacao "
                            . "  , mv.importado "
                            . "  , mv.id_evento as id "
                            . "  , ev.codigo    as rubrica "
                            . "  , ev.descricao as evento "
                            . "  , ev.tipo "
                            . "  , ev.tipo_lancamento "
                            . "  , ug.razao_social as unidade_gestora "
                            . "  , lo.descricao    as unidade_lotacao "
                            . "  , coalesce(lc.servidores,  0) as servidores "
                            . "  , coalesce(lc.total_quant, 0) as total_quant "
                            . "  , coalesce(lc.total_valor, 0) as total_valor "
                            . "from REMUN_EVENTO_AVULSO mv "
                            . "  inner join ADM_USUARIO_UNID_GESTORA x on (x.id_cliente = mv.id_cliente and x.id_unid_gestora = mv.id_unid_gestora and x.id_usuario = {$user_id} and x.acesso = 1) "
                            . "  inner join ADM_USUARIO_UNID_LOTACAO y on (y.id_cliente = mv.id_cliente and y.id_unid_lotacao = mv.id_unid_lotacao and y.id_usuario = {$user_id} and y.acesso = 1) "
                            . "  left join REMUN_UNID_GESTORA ug on (ug.id_cliente = mv.id_cliente and ug.id = mv.id_unid_gestora) "
                            . "  left join REMUN_UNID_LOTACAO lo on (lo.id_cliente = mv.id_cliente and lo.id_lotacao = mv.id_unid_lotacao) "
                            . "  left join REMUN_EVENTO ev on (ev.id_cliente = mv.id_cliente and ev.id_evento = mv.id_evento) "
                            . "  left join ( "
                            . "    Select "
                            . "        i.id_cliente "
                            . "      , i.id_unid_gestora "
                            . "      , i.id_unid_lotacao "
                            . "      , i.id_evento "
                            . "      , i.ano_mes "
                            . "      , count(i.id_servidor) as servidores "
                            . "      , sum(i.quant) as total_quant "
                            . "      , sum(i.valor) as total_valor "
                            . "    from REMUN_EVENTO_AVULSO_ITEM i "
                            . "    where (i.id_cliente = {$to})    "
                            . "      and (i.ano_mes    = '{$cp}')  "
                            . "    group by "
                            . "        i.id_cliente "
                            . "      , i.id_unid_gestora "
                            . "      , i.id_unid_lotacao "
                            . "      , i.id_evento "
                            . "      , i.ano_mes "
                            . "  ) lc on (lc.id_cliente      = mv.id_cliente "
                            . "       and lc.id_unid_gestora = mv.id_unid_gestora "
                            . "       and lc.id_unid_lotacao = mv.id_unid_lotacao "
                            . "       and lc.id_evento       = mv.id_evento "
                            . "       and lc.ano_mes         = mv.ano_mes "
                            . "  ) "
                            . "where (mv.id_cliente = {$to})   "
                            . "  and (mv.ano_mes    = '{$cp}') "
                            . $filtro
                            . "order by "
                            . "    mv.data desc "
                            . "  , ug.razao_social asc "
                            . "  , lo.descricao    asc "
                            . "  , ev.descricao    asc ";

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
                
                case 'carregar_servidores_lancamento' : {
                    try {
                        $op = trim(filter_input(INPUT_POST, 'op'));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        $controle   = floatval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'controle'))) );
                        $id_cliente = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente'))) );
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<p>&nbsp;</p>";
                        $tabela .= "<table id='datatable-responsive_serv' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'>#</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'>ID</th>";
                        $tabela .= "            <th>Nome</th>";
                        $tabela .= "            <th>Cargo/Função</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: right;'>Quantidade</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'>Valor (R$)</th>";
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
                            . "    mv.controle "
                            . "  , mv.situacao "
                            . "  , lc.id_cliente "
                            . "  , lc.id_unid_gestora "
                            . "  , lc.id_unid_lotacao "
                            . "  , lc.id_evento "
                            . "  , lc.ano_mes "
                            . "  , lc.id_servidor "
                            . "  , lc.sequencia "
                            . "  , sv.nome "
                            . "  , lc.quant "
                            . "  , lc.valor "
                            . "  , lc.obs "
                            . "  , ev.tipo "
                            . "  , ev.tipo_lancamento "
                            . "  , coalesce(cf.descricao, '* CARGO/FUNÇÃO NÃO INFORMADO') as cargo_funcao \n"
                            . "from REMUN_EVENTO_AVULSO mv \n"
                            . "  inner join REMUN_EVENTO_AVULSO_ITEM lc on ( "
                            . "        lc.id_cliente      = mv.id_cliente "
                            . "    and lc.id_unid_gestora = mv.id_unid_gestora "
                            . "    and lc.id_unid_lotacao = mv.id_unid_lotacao "
                            . "    and lc.id_evento       = mv.id_evento "
                            . "    and lc.ano_mes         = mv.ano_mes "
                            . "  ) \n"
                            . "  left join REMUN_SERVIDOR sv on (sv.id_cliente = lc.id_cliente and sv.id_servidor = lc.id_servidor)     \n"
                            . "  left join REMUN_EVENTO   ev on (ev.id_cliente = mv.id_cliente and ev.id_evento = mv.id_evento)         \n"
                            . "  left join REMUN_CARGO_FUNCAO cf on (cf.id_cliente = sv.id_cliente and cf.id_cargo = sv.id_cargo_atual) \n"
                            . " \n"
                            . "where (mv.id_cliente = {$id_cliente}) \n"
                            . "  and (mv.controle   = {$controle})   \n"
                            . " \n"
                            . "order by \n"
                            . "    lc.id_cliente "
                            . "  , lc.id_unid_gestora "
                            . "  , lc.id_unid_lotacao "
                            . "  , lc.id_evento "
                            . "  , lc.ano_mes "
                            . "  , lc.sequencia \n"; 
                        
                        $qtde_servidores  = 1;
                        $total_servidores = 0.0;
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = $controle . "_" . $qtde_servidores; //$obj->sequencia;
                            
                            $style = "padding-left: 1px; padding-right: 1px; padding-top: 1px; padding-bottom: 1px; ";
                            $input = 
                                  "<input type='hidden' id='controle_{$referencia}' value='{$obj->controle}'>"
                                . "<input type='hidden' id='sequencia_{$referencia}' value='{$obj->sequencia}'>"
                                . "<input type='hidden' id='id_cliente_{$referencia}' value='{$obj->id_cliente}'>"
                                . "<input type='hidden' id='id_unid_gestora_{$referencia}' value='{$obj->id_unid_gestora}'>"
                                . "<input type='hidden' id='id_unid_lotacao_{$referencia}' value='{$obj->id_unid_lotacao}'>"
                                . "<input type='hidden' id='id_evento_{$referencia}' value='{$obj->id_evento}'>"
                                . "<input type='hidden' id='ano_mes_{$referencia}' value='{$obj->ano_mes}'>"
                                . "<input type='hidden' id='id_servidor_{$referencia}' value='{$obj->id_servidor}'>";

                            $qt_readonly  = ((int)$obj->situacao !== 0?"readonly":((int)$obj->tipo_lancamento === 1?"readonly":""));
                            $vl_readonly  = ((int)$obj->situacao !== 0?"readonly":((int)$obj->tipo_lancamento === 0?"readonly":""));
                            
                            $qt_proximo = ((int)$obj->situacao !== 0?"":((int)$obj->tipo_lancamento === 0?"proximo_campo":""));
                            $vl_proximo = ((int)$obj->situacao !== 0?"":((int)$obj->tipo_lancamento === 1?"proximo_campo":""));
                            
                            $quant = 
                                  "<input type='text' class='form-control text lg-text {$qt_proximo}' maxlength='10' id='quant_{$referencia}' onchange='salvar_lancamento_servidor(this.id, 0)' "
                                . "value='" . number_format($obj->quant, 0, ',' , '.') . "' "
                                . "style='text-align: right; margin: 0px; border: 0; background-color:transparent; width: 100%; height: 50px; {$qt_readonly}'>";
                            $valor = 
                                  "<input type='text' class='form-control text lg-text {$vl_proximo}' maxlength='10' id='valor_{$referencia}' onchange='salvar_lancamento_servidor(this.id, 0)' "
                                . "value='" . number_format($obj->valor, 2, ',' , '.') . "' "
                                . "style='text-align: right; margin: 0px; border: 0; background-color:transparent; width: 100%; height: 50px;' {$vl_readonly}>";
                            $icon_ex = "<button id='excluir_servidor_lancamento_{$referencia}' class='btn btn-sm btn-round btn-primary excluir_servidor' title='Excluir Registro' onclick='excluirLancamentoServidor(this.id)' style='{$style}'><i class='glyph-icon icon-trash'></i></button>";
                            
                            $tabela .= "    <tr class='custom-font-size-10' id='linha_servidor_{$referencia}'>";
                            //$tabela .= "        <td style='text-align: center;'>{$obj->sequencia}</td>";
                            $tabela .= "        <td style='text-align: center;'>{$qtde_servidores}</td>";
                            $tabela .= "        <td style='text-align: center;'>" . str_pad($obj->id_servidor, 7, "0", STR_PAD_LEFT) . "</td>";
                            $tabela .= "        <td>{$obj->nome}</td>";
                            $tabela .= "        <td>{$obj->cargo_funcao}</td>";
                            $tabela .= "        <td style='text-align: right; margin: 0px; padding: 0px;'>{$quant}</td>";
                            $tabela .= "        <td style='text-align: right; margin: 0px; padding: 0px;'>{$valor}</td>";
                            $tabela .= "        <td style='text-align: center;' style='{$style}'>{$icon_ex}{$input}</td>";
                            $tabela .= "    </tr>";
                            
                            $qtde_servidores  += 1;
                            $total_servidores += floatval( ((int)$obj->tipo_lancamento === 0?$obj->quant:$obj->valor) );
                        }
                        
                        // Fechar conexão PDO
                        unset($res);
                        unset($pdo);
                        
                        $qtde_servidores -= 1;
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>\n";
                        $tabela .= "<input type='hidden' id='qtde_servidores'  value='{$qtde_servidores}'>\n";
                        $tabela .= "<input type='hidden' id='total_servidores' value='{$total_servidores}'>\n";

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
                
                case 'gravar_lancamento_evento' : {
                    try {
                        $op = trim(filter_input(INPUT_POST, 'op'));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        $controle   = floatval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'controle'))) );
                        $id_cliente = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente'))) );
                        $ano_mes    = strip_tags( trim(filter_input(INPUT_POST, 'ano_mes')) );
                        $id_unid_gestora = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unid_gestora'))) );
                        $id_unid_lotacao = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unid_lotacao'))) );
                        $id_evento       = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_evento'))) );
                        $data = strip_tags( trim(filter_input(INPUT_POST, 'data')) );
                        $hora = strip_tags( trim(filter_input(INPUT_POST, 'hora')) );
                        
                        $file = '../downloads/lanc_evento_' . $hs . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else {
                            // Verificar se já existe lançamento mensal do evento para o Cliente/Unidade/Lotação/Competência
                            $sql = 
                                  "Select "
                                . "    mv.controle "
                                . "  , mv.data     "
                                . "  , mv.usuario  "
                                . "  , us.nome     "
                                . "from REMUN_EVENTO_AVULSO mv "
                                . "  left join ADM_USUARIO us on (us.id = mv.usuario) "
                                . "where (mv.id_cliente = {$id_cliente}) "
                                . "  and (mv.ano_mes    = '{$ano_mes}')  "
                                . "  and (mv.id_unid_gestora = {$id_unid_gestora}) "
                                . "  and (mv.id_unid_lotacao = {$id_unid_lotacao}) "
                                . "  and (mv.id_evento       = {$id_evento}) "
                                . "  and (mv.controle       <> {$controle})  "
                                . "  and (mv.situacao       <> 2) ";
                            
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
                                    $controle = $dao->getGeneratorID("GEN_EVENTO_AVULSO");
                                    // Inserir Lançamento
                                    $stm = $pdo->prepare(
                                          "Insert Into REMUN_EVENTO_AVULSO ("
                                        . "    controle         "
                                        . "  , id_cliente       "
                                        . "  , id_unid_gestora  "
                                        . "  , id_unid_lotacao  "
                                        . "  , id_evento        "
                                        . "  , ano_mes          "
                                        . "  , data             "
                                        . "  , hora             "
                                        . "  , usuario          "
                                        . "  , situacao         "
                                        . "  , importado        "
                                        . ") values ("
                                        . "    :controle         "
                                        . "  , :id_cliente       "
                                        . "  , :id_unid_gestora  "
                                        . "  , :id_unid_lotacao  "
                                        . "  , :id_evento        "
                                        . "  , :ano_mes          "
                                        . "  , current_date      "
                                        . "  , current_time      "
                                        . "  , :usuario          "
                                        . "  , 0 "
                                        . "  , 0 "
                                        . ")");
                                    $stm->execute(array(
                                        ':controle'         => $controle,
                                        ':id_cliente'       => $id_cliente,
                                        ':id_unid_gestora'  => $id_unid_gestora,
                                        ':id_unid_lotacao'  => $id_unid_lotacao,
                                        ':id_evento'        => $id_evento,
                                        ':ano_mes'          => $ano_mes,
                                        ':usuario'          => $user_id
                                    ));
                                    $pdo->commit();
                                    /*
                                    // Gerar Itens do Lançamento
                                    $stm = $pdo->prepare(
                                          "Execute procedure SET_REMUN_EVENTO_AVULSO_ITEM ( "
                                        . "    :id_cliente      "
                                        . "  , :id_unid_gestora "
                                        . "  , :id_unid_lotacao "
                                        . "  , :id_evento       "
                                        . "  , :ano_mes         "
                                        . ")");
                                    $stm->execute(array(
                                        ':id_cliente'       => $id_cliente,
                                        ':id_unid_gestora'  => $id_unid_gestora,
                                        ':id_unid_lotacao'  => $id_unid_lotacao,
                                        ':id_evento'        => $id_evento,
                                        ':ano_mes'          => $ano_mes
                                    ));
                                    $pdo->commit();
                                    */
                                } else
                                if ($op === "editar_lancamento") {
                                    $stm = $pdo->prepare(
                                          "Update REMUN_EVENTO_AVULSO mv Set            "
                                        . "    mv.id_unid_gestora  = :id_unid_gestora   "
                                        . "  , mv.id_unid_lotacao  = :id_unid_lotacao   "
                                        . "  , mv.id_evento        = :id_evento         "
                                        . "  , mv.ano_mes          = :ano_mes           "
                                        . "where (mv.situacao  = 0)  "
                                        . "  and (mv.importado = 0)  "
                                        . "  and (mv.controle  = :controle)");
                                    $stm->execute(array(
                                        ':controle'         => $controle,
                                        ':id_unid_gestora'  => $id_unid_gestora,
                                        ':id_unid_lotacao'  => $id_unid_lotacao,
                                        ':id_evento'        => $id_evento,
                                        ':ano_mes'          => $ano_mes
                                    ));
                                    $pdo->commit();
                                } 

                                $obj = getRegistro($id_cliente, $ano_mes, $controle);
                                
                                $registros = array('form' => array());
                                $registros['form'][0]['referencia'] = $controle;
                                $registros['form'][0]['controle']   = str_pad($controle, 5, "0", STR_PAD_LEFT);
                                $registros['form'][0]['ano_mes']    = $ano_mes;
                                $registros['form'][0]['data']       = $data;
                                $registros['form'][0]['hora']       = $hora;
                                $registros['form'][0]['rubrica']    = $obj->rubrica;
                                $registros['form'][0]['tipo']       = $obj->tipo;
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

                case 'gravar_lancamentos_servidores' : {
                    try {
                        $op = trim(filter_input(INPUT_POST, 'op'));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        $controle   = floatval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'controle'))) );
                        $id_cliente = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente'))) );
                        $ano_mes    = strip_tags( trim(filter_input(INPUT_POST, 'ano_mes')) );
                        $id_unid_gestora = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unid_gestora'))) );
                        $id_unid_lotacao = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unid_lotacao'))) );
                        $id_evento       = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_evento'))) );
                        
                        $servidores  = strip_tags( trim(filter_input(INPUT_POST, 'servidores')) );
                        $quantidades = strip_tags( trim(filter_input(INPUT_POST, 'quantidades')) );
                        $valores     = strip_tags( trim(filter_input(INPUT_POST, 'valores')) );
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else 
                        if ((trim($servidores) !== '') && (trim($servidores) !== '#')) {    
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            // Gravar quantidades
                            if ((trim($quantidades) !== '') && (trim($quantidades) !== '#')) {
                                $ids = explode("||", $servidores);
                                $qts = explode("||", $quantidades);
                                
                                $pdo->beginTransaction();
                                for ($i = 0; $i < count($ids); $i++) {
                                    $quant = str_replace(",", ".", str_replace(".", "", $qts[$i]));
                                    $quant = floatval('0' . $quant);
                                    
                                    $stm = $pdo->prepare(
                                          "Execute procedure SET_REMUN_EVENTO_SERVIDOR ( "
                                        . "    :ano_mes         "
                                        . "  , :id_cliente      "
                                        . "  , :id_unid_gestora "
                                        . "  , :id_unid_lotacao "
                                        . "  , :id_evento       "
                                        . "  , :id_servidor     "
                                        . "  , :tipo_lanc       "
                                        . "  , :quant           "
                                        . "  , :valor           "
                                        . "  , :obs             "
                                        . ")");
                                    $stm->execute(array(
                                          ':ano_mes'          => $ano_mes
                                        , ':id_cliente'       => $id_cliente
                                        , ':id_unid_gestora'  => $id_unid_gestora
                                        , ':id_unid_lotacao'  => $id_unid_lotacao
                                        , ':id_evento'        => $id_evento
                                        , ':id_servidor'      => $ids[$i]
                                        , ':tipo_lanc'        => 0
                                        , ':quant'            => $quant
                                        , ':valor'            => null
                                        , ':obs'              => null
                                    ));
                                }
                                $pdo->commit();
                            } else
                            // Gravar valores
                            if ((trim($valores) !== '') && (trim($valores) !== '#')) {
                                $ids = explode("||", $servidores);
                                $vls = explode("||", $valores);
                                
                                $pdo->beginTransaction();
                                for ($i = 0; $i < count($ids); $i++) {
                                    $valor = str_replace(",", ".", str_replace(".", "", $vls[$i]));
                                    $valor = floatval('0' . $valor);
                                    
                                    $stm = $pdo->prepare(
                                          "Execute procedure SET_REMUN_EVENTO_SERVIDOR ( "
                                        . "    :ano_mes         "
                                        . "  , :id_cliente      "
                                        . "  , :id_unid_gestora "
                                        . "  , :id_unid_lotacao "
                                        . "  , :id_evento       "
                                        . "  , :id_servidor     "
                                        . "  , :tipo_lanc       "
                                        . "  , :quant           "
                                        . "  , :valor           "
                                        . "  , :obs             "
                                        . ")");
                                    $stm->execute(array(
                                          ':ano_mes'          => $ano_mes
                                        , ':id_cliente'       => $id_cliente
                                        , ':id_unid_gestora'  => $id_unid_gestora
                                        , ':id_unid_lotacao'  => $id_unid_lotacao
                                        , ':id_evento'        => $id_evento
                                        , ':id_servidor'      => $ids[$i]
                                        , ':tipo_lanc'        => 1
                                        , ':quant'            => null
                                        , ':valor'            => $valor
                                        , ':obs'              => null
                                    ));
                                }
                                $pdo->commit();
                            }
                            
                            // Fechar conexão PDO
                            unset($pdo);
                            
                            echo "OK";
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;

                case 'grava_lancamento_servidor' : {
                    try {
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        $controle   = floatval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'controle'))) );
                        $id_cliente = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente'))) );
                        $id_unid_gestora = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unid_gestora'))) );
                        $id_unid_lotacao = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_unid_lotacao'))) );
                        $id_evento   = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_evento'))) );
                        $ano_mes     = strip_tags( trim(filter_input(INPUT_POST, 'ano_mes')) );
                        $sequencia   = intval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'sequencia'))) );
                        $id_servidor = floatval( preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_servidor'))) );
                        $quant = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'quant')) );
                        $valor = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'valor')) );
                        $obs   = strip_tags( trim(filter_input(INPUT_POST, 'obs')) );
                        
                        $file = '../downloads/lanc_srv_' . $hs . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }

                        $tipo_lancamento = 0;
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else 
                        if ($quant !== '') {
                            $tipo_lancamento = 0;
                            
                            $quant = str_replace(",", ".", str_replace(".", "", $quant));
                            $quant = floatval('0' . $quant);
                            if ($obs === "") { $obs = null; }

                            $stm = $pdo->prepare(
                                  "Execute procedure SET_REMUN_EVENTO_SERVIDOR ( "
                                . "    :ano_mes         "
                                . "  , :id_cliente      "
                                . "  , :id_unid_gestora "
                                . "  , :id_unid_lotacao "
                                . "  , :id_evento       "
                                . "  , :id_servidor     "
                                . "  , :tipo_lanc       "
                                . "  , :quant           "
                                . "  , :valor           "
                                . "  , :obs             "
                                . ")");
                            $stm->execute(array(
                                  ':ano_mes'          => $ano_mes
                                , ':id_cliente'       => $id_cliente
                                , ':id_unid_gestora'  => $id_unid_gestora
                                , ':id_unid_lotacao'  => $id_unid_lotacao
                                , ':id_evento'        => $id_evento
                                , ':id_servidor'      => $id_servidor
                                , ':tipo_lanc'        => $tipo_lancamento
                                , ':quant'            => $quant
                                , ':valor'            => null
                                , ':obs'              => $obs
                            ));
                            $pdo->commit();
                        } else 
                        if ($valor !== '') {
                            $tipo_lancamento = 1;
                            
                            $valor = str_replace(",", ".", str_replace(".", "", $valor));
                            $valor = floatval('0' . $valor);
                            if ($obs === "") { $obs = null; }

                            $stm = $pdo->prepare(
                                  "Execute procedure SET_REMUN_EVENTO_SERVIDOR ( "
                                . "    :ano_mes         "
                                . "  , :id_cliente      "
                                . "  , :id_unid_gestora "
                                . "  , :id_unid_lotacao "
                                . "  , :id_evento       "
                                . "  , :id_servidor     "
                                . "  , :tipo_lanc       "
                                . "  , :quant           "
                                . "  , :valor           "
                                . "  , :obs             "
                                . ")");
                            $stm->execute(array(
                                  ':ano_mes'          => $ano_mes
                                , ':id_cliente'       => $id_cliente
                                , ':id_unid_gestora'  => $id_unid_gestora
                                , ':id_unid_lotacao'  => $id_unid_lotacao
                                , ':id_evento'        => $id_evento
                                , ':id_servidor'      => $id_servidor
                                , ':tipo_lanc'        => $tipo_lancamento
                                , ':quant'            => null
                                , ':valor'            => $valor
                                , ':obs'              => $obs
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

                case 'lancamento_servidor' : {
                    try {
                        $to = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'to'))) ); // Cliente
                        $ug = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ug'))) );
                        $lo = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'lo'))) );
                        $ev = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ev'))) ); // Competência - ano/mês
                        $cp = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cp'))) );
                        $id = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id'))) ); // Controle
                        $sv = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'sv'))) ); // Servidor
                        $hs = trim(filter_input(INPUT_POST, 'hs'));

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql =
                              "Select "
                            . "  count(lanc.sequencia) as lancamentos "
                            . "from REMUN_EVENTO_AVULSO_ITEM lanc "
                            . "where (id_cliente      = {$to})    "
                            . "  and (id_unid_gestora = {$ug})    "
                            . "  and (id_unid_lotacao = {$lo})    "
                            . "  and (id_evento       = {$ev})    "
                            . "  and (ano_mes         = '{$cp}')  "
                            . "  and (id_servidor     = {$sv})    "; 
                            
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
                
                case 'excluir_lancamento_evento' : {
                    try {
                        $to = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'to'))) );
                        $ug = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ug'))) );
                        $lo = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'lo'))) );
                        $ev = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ev'))) );
                        $cp = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cp'))) );
                        $id = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id'))) );
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
                                      "Delete from REMUN_EVENTO_AVULSO "
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
