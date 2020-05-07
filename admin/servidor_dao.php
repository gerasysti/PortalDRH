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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ac'])) {
            switch ($_POST['ac']) {
                case 'consultar_servidor' : {
                    try {
                        $id = strip_tags( trim(filter_input(INPUT_POST, 'id')) );
                        $us = strip_tags( trim(filter_input(INPUT_POST, 'us')) );
                        $to = strip_tags( trim(filter_input(INPUT_POST, 'to')) );
                        $ps = strip_tags( str_replace(" ", "%", trim(filter_input(INPUT_POST, 'ps'))) );
                        
                        $id_unidade = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'ug')) );
                        $id_lotacao = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lo')) );
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th>ID</th>";
                        $tabela .= "            <th>Nome</th>";
                        $tabela .= "            <th>CPF</th>";
                        $tabela .= "            <th>UG</th>";
                        $tabela .= "            <th>Lotação</th>";
                        $tabela .= "            <th>Cargo/Função</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $id_servidor = floatval(preg_replace("/[^0-9]/", "", trim($ps) ));
                        $servidor    = "";
                        
                        if ($id_servidor > 0) {
                            $servidor = "  and (s.id_servidor = {$id_servidor})";
                            if (function_exists('validarCPF')) {
                                if (validarCPF(trim($ps)) === true) {
                                    $servidor = "  and (s.cpf = '" . preg_replace("/[^0-9]/", "", trim($ps)) . "')";
                                }
                            }
                        } else {
                            $servidor = "  and (upper(s.nome) like upper('%{$ps}%'))";
                        }
                        
                        $ln  = "";
                        $sql = 
                              "Select "
                            . "    s.* "
                            . "  , coalesce(g.descricao, '* UG NÃO INFORMADA')           as undade_gestora "
                            . "  , coalesce(l.descricao, '* LOTAÇÃO NÃO INFORMADA')      as undade_lotacao "
                            . "  , coalesce(f.descricao, '* CARGO/FUNÇÃO NÃO INFORMADO') as cargo_funcao   "
                            . "from REMUN_SERVIDOR s "
                            . "  inner join ADM_USUARIO_UNID_GESTORA x on (x.id_cliente = s.id_cliente and x.id_unid_gestora = s.id_unid_gestora and x.id_usuario = {$user_id} and x.acesso = 1) "
                            . "  inner join ADM_USUARIO_UNID_LOTACAO y on (y.id_cliente = s.id_cliente and y.id_unid_lotacao = s.id_unid_lotacao and y.id_usuario = {$user_id} and y.acesso = 1) "
                            . "  left join REMUN_UNID_GESTORA g on (g.id_cliente = s.id_cliente and g.id = s.id_unid_gestora) "
                            . "  left join REMUN_UNID_LOTACAO l on (l.id_cliente = s.id_cliente and l.id_lotacao = s.id_unid_lotacao) "
                            . "  left join REMUN_CARGO_FUNCAO f on (f.id_cliente = s.id_cliente and f.id_cargo = s.id_cargo_atual) "
                            . "where (s.id_cliente = {$to}) "
                            . $servidor
                            . "  " . ($id_unidade !== 0?"and (s.id_unid_gestora = {$id_unidade})":"")
                            . "  " . ($id_lotacao !== 0?"and (s.id_unid_lotacao = {$id_lotacao})":"")
                            . "  " . (($id_unidade === 0) && ($id_lotacao === 0)?"and (s.situacao = 1)":"") . " "
                            . "order by   "
                            . "    s.nome "; 
                        //echo $sql . "<br><br>";
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia  = $obj->id_servidor; 
                            $id_cliente  = $obj->id_cliente;
                            $id_servidor = str_pad($obj->id_servidor, 7, "0", STR_PAD_LEFT);
                            $matricula   = (!empty($obj->matricula)?$obj->matricula:"&nbsp;");
                            $dt_admissao   = (!empty($obj->dt_admissao)?date('d/m/Y', strtotime($obj->dt_admissao) ):"&nbsp;");
                            $dt_nascimento = (!empty($obj->dt_nascimento)?date('d/m/Y', strtotime($obj->dt_nascimento) ):"&nbsp;");
                            $nome   = (!empty($obj->nome)?$obj->nome:"&nbsp;");
                            $rg     = (!empty($obj->rg)?$obj->rg:"&nbsp;");
                            $cpf    = (!empty($obj->cpf)?$obj->cpf:"&nbsp;");
                            $pis_pasep  = (!empty($obj->pis_pasep)?$obj->pis_pasep:"&nbsp;");
                            $unid_gest  = (!empty($obj->id_unid_gestora)?$obj->id_unid_gestora:"0");
                            $unid_lota  = (!empty($obj->id_unid_lotacao)?$obj->id_unid_lotacao:"0");
                            $cargo_atual = (!empty($obj->id_cargo_atual)?$obj->id_cargo_atual:"0");
                            $situacao    = intval("0" . $obj->situacao);
                            
                            $style = "padding-left: 1px; padding-right: 1px; padding-top: 1px; padding-bottom: 1px; ";
                            $input = 
                                  "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
                                . "<input type='hidden' id='id_servidor_{$referencia}' value='{$id_servidor}'>"
                                . "<input type='hidden' id='matricula_{$referencia}' value='{$matricula}'>"
                                . "<input type='hidden' id='dt_admissao_{$referencia}' value='{$dt_admissao}'>"
                                . "<input type='hidden' id='nome_{$referencia}' value='{$nome}'>"
                                . "<input type='hidden' id='rg_{$referencia}' value='{$rg}'>"
                                . "<input type='hidden' id='cpf_{$referencia}' value='" . formatarTexto('###.###.###-##', $cpf) . "'>"
                                . "<input type='hidden' id='pis_pasep_{$referencia}' value='{$pis_pasep}'>"
                                . "<input type='hidden' id='dt_nascimento_{$referencia}' value='{$dt_nascimento}'>"
                                . "<input type='hidden' id='id_unid_gestora_{$referencia}' value='{$unid_gest}'>"
                                . "<input type='hidden' id='id_unid_lotacao_{$referencia}' value='{$unid_lota}'>"
                                . "<input type='hidden' id='id_cargo_atual_{$referencia}' value='{$cargo_atual}'>"
                                . "<input type='hidden' id='situacao_{$referencia}' value='{$situacao}'>";
                            
                            $icon_ed = "<button id='editar_servidor_{$referencia}'  class='btn btn-round btn-primary' title='Editar Registro'  onclick='editarServidor(this.id)'  style='{$style}'><i class='glyph-icon icon-edit'></i></button>";
                            $icon_ex = ""; //"<button id='excluir_servidor_{$referencia}' class='btn btn-round btn-primary' title='Excluir Registro' onclick='excluirServidor(this.id)' style='{$style}'><i class='glyph-icon icon-trash'></i></button>";
                            $icon_st = ""; 
                            
                            if ($situacao === 1) {
                                $icon_st = "<i class='glyph-icon icon-check-square-o'></i>";
                            } else {
                                $icon_st = "<i class='glyph-icon icon-square-o'></i>";
                            }
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr class='custom-font-size-10' id='linha_{$referencia}'>";
                            $tabela .= "        <td>{$id_servidor}</td>";
                            $tabela .= "        <td>{$nome}</td>";
                            $tabela .= "        <td>" . formatarTexto('###.###.###-##', $cpf) . "</td>";
                            $tabela .= "        <td>{$obj->undade_gestora}</td>";
                            $tabela .= "        <td>{$obj->undade_lotacao}</td>";
                            $tabela .= "        <td>" . $obj->cargo_funcao . (isset($obj->id_cargo_atual)?" ({$obj->id_cargo_atual})":"") . "</td>";
                            $tabela .= "        <td style='text-align: center;'>{$icon_st}</td>";
                            $tabela .= "        <td style='text-align: center;' style='{$style}'>{$icon_ed}&nbsp;{$icon_ex}{$input}</td>";
                            $tabela .= "    </tr>";
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'consultar_servidor-pesquisa' : {
                    try {
                        $id = strip_tags( trim(filter_input(INPUT_POST, 'id')) );
                        $us = strip_tags( trim(filter_input(INPUT_POST, 'us')) );
                        $to = strip_tags( trim(filter_input(INPUT_POST, 'to')) );
                        $ug = strip_tags( trim(filter_input(INPUT_POST, 'ug')) );
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive-serv' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th>ID</th>";
                        $tabela .= "            <th>Nome</th>";
                        $tabela .= "            <th>Cargo/Função</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        $sql = 
                              "Select "
                            . "    s.* "
                            . "  , coalesce(g.descricao, '* UG. NÃO INFORMADA')          as undade_gestora "
                            . "  , coalesce(o.descricao, '* UO. NÃO INFORMADA')          as undade_orcamentaria "
                            . "  , coalesce(b.descricao, '* SUB. UO. NÃO INFORMADA')     as subundade_orcamentaria "
                            . "  , coalesce(l.descricao, '* LOTAÇÃO NÃO INFORMADA')      as undade_lotacao "
                            . "  , coalesce(f.descricao, '* CARGO/FUNÇÃO NÃO INFORMADO') as cargo_funcao   "
                            . "from REMUN_SERVIDOR s "
                            . "  inner join REMUN_CARGO_FUNCAO f on (f.id_cliente = s.id_cliente and f.id_cargo = s.id_cargo_atual) "
                            . "  left join ADM_USUARIO_UNID_GESTORA x on (x.id_cliente = s.id_cliente and x.id_unid_gestora = s.id_unid_gestora and x.id_usuario = {$user_id} and x.acesso = 1) "
                            . "  left join ADM_USUARIO_UNID_LOTACAO y on (y.id_cliente = s.id_cliente and y.id_unid_lotacao = s.id_unid_lotacao and y.id_usuario = {$user_id} and y.acesso = 1) "
                            . "  left join REMUN_UNID_GESTORA g on (g.id_cliente = s.id_cliente and g.id = s.id_unid_gestora) "
                            . "  left join REMUN_UNID_ORCAMENT o on (o.id_cliente = s.id_cliente and o.id = s.id_unid_orcament) "
                            . "  left join REMUN_SUBUNID_ORCAMENT b on (b.id_cliente = s.id_cliente and b.id = s.id_sub_unid_orcam) "
                            . "  left join REMUN_UNID_LOTACAO l on (l.id_cliente = s.id_cliente and l.id_lotacao = s.id_unid_lotacao) "
                            . "where (s.id_cliente = {$to}) "
                            . "  and (s.id_unid_gestora = {$ug}) "
                            . "  and (s.situacao = 1)   "
                            //. "  and (f.tipo_sal = '2') " // TIPO SALÁRIO: 2 - Hora/aula
                            . "order by   "
                            . "    s.nome "; 
                        
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia  = $obj->id_servidor; 
                            $id_cliente  = $obj->id_cliente;
                            $id_servidor = str_pad($obj->id_servidor, 7, "0", STR_PAD_LEFT);
                            $matricula   = (!empty($obj->matricula)?$obj->matricula:"&nbsp;");
                            $dt_admissao   = (!empty($obj->dt_admissao)?date('d/m/Y', strtotime($obj->dt_admissao) ):"&nbsp;");
                            $dt_nascimento = (!empty($obj->dt_nascimento)?date('d/m/Y', strtotime($obj->dt_nascimento) ):"&nbsp;");
                            $nome   = (!empty($obj->nome)?$obj->nome:"&nbsp;");
                            $rg     = (!empty($obj->rg)?$obj->rg:"&nbsp;");
                            $cpf    = (!empty($obj->cpf)?$obj->cpf:"&nbsp;");
                            $pis_pasep  = (!empty($obj->pis_pasep)?$obj->pis_pasep:"&nbsp;");
                            $unid_gest  = (!empty($obj->id_unid_gestora)?$obj->id_unid_gestora:"0");
                            $unid_orca  = (!empty($obj->id_unid_orcament)?$obj->id_unid_orcament:"0");
                            $unid_lota  = (!empty($obj->id_unid_lotacao)?$obj->id_unid_lotacao:"0");
                            $cargo_funcao = (!empty($obj->cargo_funcao)?$obj->cargo_funcao:"&nbsp;");
                            $cargo_atual  = (!empty($obj->id_cargo_atual)?$obj->id_cargo_atual:"0");
                            $subundade_orcamentaria = (!empty($obj->subundade_orcamentaria)?$obj->subundade_orcamentaria:"&nbsp;");
                            $situacao     = intval("0" . $obj->situacao);
                            
                            $style = "padding-left: 1px; padding-right: 1px; padding-top: 1px; padding-bottom: 1px; margin: 1px;";
                            $input = 
                                  "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
                                . "<input type='hidden' id='id_servidor_{$referencia}' value='{$id_servidor}'>"
                                . "<input type='hidden' id='matricula_{$referencia}' value='{$matricula}'>"
                                . "<input type='hidden' id='dt_admissao_{$referencia}' value='{$dt_admissao}'>"
                                . "<input type='hidden' id='nome_{$referencia}' value='{$nome}'>"
                                . "<input type='hidden' id='rg_{$referencia}' value='{$rg}'>"
                                . "<input type='hidden' id='cpf_{$referencia}' value='" . formatarTexto('###.###.###-##', $cpf) . "'>"
                                . "<input type='hidden' id='pis_pasep_{$referencia}' value='{$pis_pasep}'>"
                                . "<input type='hidden' id='dt_nascimento_{$referencia}' value='{$dt_nascimento}'>"
                                . "<input type='hidden' id='id_unid_gestora_{$referencia}' value='{$unid_gest}'>"
                                . "<input type='hidden' id='id_unid_orcament_{$referencia}' value='{$unid_orca}'>"
                                . "<input type='hidden' id='id_unid_lotacao_{$referencia}' value='{$unid_lota}'>"
                                . "<input type='hidden' id='id_cargo_atual_{$referencia}' value='{$cargo_atual}'>"
                                . "<input type='hidden' id='cargo_funcao_{$referencia}' value='{$cargo_funcao}'>"
                                . "<input type='hidden' id='subundade_orcamentaria_{$referencia}' value='{$subundade_orcamentaria}'>"
                                . "<input type='hidden' id='situacao_{$referencia}' value='{$situacao}'>";
                            
                            //$icon_ed = "<button id='selecionar_professor_{$referencia}' class='btn btn-round btn-primary' title='Selecionar Servidor' onclick='selecionar_professor(this.id)' style='{$style}'><i class='glyph-icon icon-check-square-o'></i></button>";
                            $icon_ed = "<button id='selecionar_servidor_{$referencia}' class='btn btn-round btn-default' title='Selecionar Servidor' onclick='selecionar_servidor(this.id)' style='{$style}'><i class='glyph-icon icon-check'></i></button>";
                            
                            if ($situacao === 1) {
                                $icon_st = "<i class='glyph-icon icon-check-square-o'></i>";
                            } else {
                                $icon_st = "<i class='glyph-icon icon-square-o'></i>";
                            }
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr class='custom-font-size-10' id='linha_{$referencia}' style='{$style}'>";
                            $tabela .= "        <td>{$id_servidor}</td>";
                            $tabela .= "        <td>{$nome}</td>";
                            $tabela .= "        <td>{$cargo_funcao}</td>";
                            $tabela .= "        <td style='text-align: center;' style='{$style}'>{$icon_ed}&nbsp;{$input}</td>";
                            $tabela .= "    </tr>";
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'consultar_professor' : {
                    try {
                        $id = strip_tags( trim(filter_input(INPUT_POST, 'id')) );
                        $us = strip_tags( trim(filter_input(INPUT_POST, 'us')) );
                        $to = strip_tags( trim(filter_input(INPUT_POST, 'to')) );
                        //$ug = strip_tags( trim(filter_input(INPUT_POST, 'ug')) );
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive-prof' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th>ID</th>";
                        $tabela .= "            <th>Nome</th>";
                        $tabela .= "            <th>Cargo/Função</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        $sql = 
                              "Select "
                            . "    s.* "
                            . "  , coalesce(g.descricao, '* UG NÃO INFORMADA')           as undade_gestora "
                            . "  , coalesce(l.descricao, '* LOTAÇÃO NÃO INFORMADA')      as undade_lotacao "
                            . "  , coalesce(f.descricao, '* CARGO/FUNÇÃO NÃO INFORMADO') as cargo_funcao   "
                            . "from REMUN_SERVIDOR s "
                            . "  inner join REMUN_CARGO_FUNCAO f on (f.id_cliente = s.id_cliente and f.id_cargo = s.id_cargo_atual) "
                            . "  left join ADM_USUARIO_UNID_GESTORA x on (x.id_cliente = s.id_cliente and x.id_unid_gestora = s.id_unid_gestora and x.id_usuario = {$user_id} and x.acesso = 1) "
                            . "  left join ADM_USUARIO_UNID_LOTACAO y on (y.id_cliente = s.id_cliente and y.id_unid_lotacao = s.id_unid_lotacao and y.id_usuario = {$user_id} and y.acesso = 1) "
                            . "  left join REMUN_UNID_GESTORA g on (g.id_cliente = s.id_cliente and g.id = s.id_unid_gestora) "
                            . "  left join REMUN_UNID_LOTACAO l on (l.id_cliente = s.id_cliente and l.id_lotacao = s.id_unid_lotacao) "
                            . "where (s.id_cliente = {$to}) "
                            //. "  and (s.id_unid_gestora = {$ug})   "
                            . "  and (s.situacao = 1)   "
                            . "  and (f.tipo_sal = '2') " // TIPO SALÁRIO: 2 - Hora/aula
                            . "order by   "
                            . "    s.nome "; 
                        
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia  = $obj->id_servidor; 
                            $id_cliente  = $obj->id_cliente;
                            $id_servidor = str_pad($obj->id_servidor, 7, "0", STR_PAD_LEFT);
                            $matricula   = (!empty($obj->matricula)?$obj->matricula:"&nbsp;");
                            $dt_admissao   = (!empty($obj->dt_admissao)?date('d/m/Y', strtotime($obj->dt_admissao) ):"&nbsp;");
                            $dt_nascimento = (!empty($obj->dt_nascimento)?date('d/m/Y', strtotime($obj->dt_nascimento) ):"&nbsp;");
                            $nome   = (!empty($obj->nome)?$obj->nome:"&nbsp;");
                            $rg     = (!empty($obj->rg)?$obj->rg:"&nbsp;");
                            $cpf    = (!empty($obj->cpf)?$obj->cpf:"&nbsp;");
                            $pis_pasep  = (!empty($obj->pis_pasep)?$obj->pis_pasep:"&nbsp;");
                            $unid_gest  = (!empty($obj->id_unid_gestora)?$obj->id_unid_gestora:"0");
                            $unid_lota  = (!empty($obj->id_unid_lotacao)?$obj->id_unid_lotacao:"0");
                            $cargo_funcao = (!empty($obj->cargo_funcao)?$obj->cargo_funcao:"&nbsp;");
                            $cargo_atual  = (!empty($obj->id_cargo_atual)?$obj->id_cargo_atual:"0");
                            $situacao     = intval("0" . $obj->situacao);
                            
                            $style = "padding-left: 1px; padding-right: 1px; padding-top: 1px; padding-bottom: 1px; margin: 1px;";
                            $input = 
                                  "<input type='hidden' id='id_cliente_{$referencia}' value='{$id_cliente}'>"
                                . "<input type='hidden' id='id_servidor_{$referencia}' value='{$id_servidor}'>"
                                . "<input type='hidden' id='matricula_{$referencia}' value='{$matricula}'>"
                                . "<input type='hidden' id='dt_admissao_{$referencia}' value='{$dt_admissao}'>"
                                . "<input type='hidden' id='nome_{$referencia}' value='{$nome}'>"
                                . "<input type='hidden' id='rg_{$referencia}' value='{$rg}'>"
                                . "<input type='hidden' id='cpf_{$referencia}' value='" . formatarTexto('###.###.###-##', $cpf) . "'>"
                                . "<input type='hidden' id='pis_pasep_{$referencia}' value='{$pis_pasep}'>"
                                . "<input type='hidden' id='dt_nascimento_{$referencia}' value='{$dt_nascimento}'>"
                                . "<input type='hidden' id='id_unid_gestora_{$referencia}' value='{$unid_gest}'>"
                                . "<input type='hidden' id='id_unid_lotacao_{$referencia}' value='{$unid_lota}'>"
                                . "<input type='hidden' id='id_cargo_atual_{$referencia}' value='{$cargo_atual}'>"
                                . "<input type='hidden' id='cargo_funcao_{$referencia}' value='{$cargo_funcao}'>"
                                . "<input type='hidden' id='situacao_{$referencia}' value='{$situacao}'>";
                            
                            //$icon_ed = "<button id='selecionar_professor_{$referencia}' class='btn btn-round btn-primary' title='Selecionar Servidor' onclick='selecionar_professor(this.id)' style='{$style}'><i class='glyph-icon icon-check-square-o'></i></button>";
                            $icon_ed = "<button id='selecionar_professor_{$referencia}' class='btn btn-round btn-default' title='Selecionar Servidor' onclick='selecionar_professor(this.id)' style='{$style}'><i class='glyph-icon icon-check'></i></button>";
                            
                            if ($situacao === 1) {
                                $icon_st = "<i class='glyph-icon icon-check-square-o'></i>";
                            } else {
                                $icon_st = "<i class='glyph-icon icon-square-o'></i>";
                            }
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr class='custom-font-size-10' id='linha_{$referencia}' style='{$style}'>";
                            $tabela .= "        <td>{$id_servidor}</td>";
                            $tabela .= "        <td>{$nome}</td>";
                            $tabela .= "        <td>{$cargo_funcao}</td>";
                            $tabela .= "        <td style='text-align: center;' style='{$style}'>{$icon_ed}&nbsp;{$input}</td>";
                            $tabela .= "    </tr>";
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'carregar_servidor' : {
                    try {
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id_cliente  = preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_cliente')) );
                        $id_servidor = preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'id_servidor')) );

                        $file = '../downloads/servidor_' . $hs . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else {
                            $sql = 
                                  "Select "
                                . "    s.* "
                                . "  , coalesce(g.descricao, '* UG. NÃO INFORMADA')     as unidade_gestora "
                                . "  , coalesce(o.descricao, '* UO. NÃO INFORMADA')     as unidade_orcamentaria "
                                . "  , coalesce(l.descricao, '* LOTAÇÃO NÃO INFORMADA') as unidade_lotacao "
                                . "  , coalesce(b.descricao, '* SUB UO. NÃO INFORMADO') as subunidade_orcamentaria   "
                                . "  , coalesce(f.descricao, '* CARGO/FUNÇÃO NÃO INFORMADO') as cargo_funcao   "
                                . "  , coalesce(f.tipo_sal,  '1') as tipo_salario "
                                . "from REMUN_SERVIDOR s "
                                . "  left join REMUN_UNID_GESTORA g on (g.id_cliente = s.id_cliente and g.id = s.id_unid_gestora)         "
                                . "  left join REMUN_UNID_ORCAMENT o on (o.id_cliente = s.id_cliente and o.id = s.id_unid_orcament) "
                                . "  left join REMUN_SUBUNID_ORCAMENT b on (b.id_cliente = s.id_cliente and b.id = s.id_sub_unid_orcam) "
                                . "  left join REMUN_UNID_LOTACAO l on (l.id_cliente = s.id_cliente and l.id_lotacao = s.id_unid_lotacao) "
                                . "  left join REMUN_CARGO_FUNCAO f on (f.id_cliente = s.id_cliente and f.id_cargo = s.id_cargo_atual)    "
                                . "where (s.id_cliente  = {$id_cliente})  "
                                . "  and (s.id_servidor = {$id_servidor}) ";


                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $res = $pdo->query($sql);
                            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                $referencia  = $obj->id_servidor; 
                                $id_cliente  = $obj->id_cliente;
                                $id_servidor = str_pad($obj->id_servidor, 7, "0", STR_PAD_LEFT);
                                $matricula   = (!empty($obj->matricula)?$obj->matricula:"");
                                $nome   = (!empty($obj->nome)?$obj->nome:"");
                                $rg     = (!empty($obj->rg)?$obj->rg:"");
                                $cpf    = (!empty($obj->cpf)?$obj->cpf:"");
                                $pis_pasep     = (!empty($obj->pis_pasep)?$obj->pis_pasep:"");
                                $dt_admissao   = (!empty($obj->dt_admissao)?date('d/m/Y', strtotime($obj->dt_admissao) ):"");
                                $dt_nascimento = (!empty($obj->dt_nascimento)?date('d/m/Y', strtotime($obj->dt_nascimento) ):"");
                                $unid_gest  = (!empty($obj->id_unid_gestora)?$obj->id_unid_gestora:"0");
                                $unid_lota  = (!empty($obj->id_unid_lotacao)?$obj->id_unid_lotacao:"0");
                                $cargo_atual  = (!empty($obj->id_cargo_atual)?$obj->id_cargo_atual:"0");
                                $situacao     = intval("0" . $obj->situacao);

                                // formatarTexto('###.###.###-##', $cpf)
                                $registros = array('form' => array());

                                $registros['form'][0]['referencia']  = $referencia;
                                $registros['form'][0]['id_servidor'] = $id_servidor;
                                $registros['form'][0]['matricula']   = $matricula;
                                $registros['form'][0]['nome']        = $nome;
                                $registros['form'][0]['cpf']         = $cpf;
                                $registros['form'][0]['rg']          = $rg;
                                $registros['form'][0]['cpf_formatado']   = formatarTexto('###.###.###-##', $cpf);
                                $registros['form'][0]['dt_admissao']     = $dt_admissao;
                                $registros['form'][0]['dt_nascimento']   = $dt_nascimento;
                                $registros['form'][0]['unid_gest']       = $unid_gest;
                                $registros['form'][0]['unid_lota']       = $unid_lota;
                                $registros['form'][0]['cargo_atual']     = $cargo_atual;
                                $registros['form'][0]['situacao']        = $situacao;
                                $registros['form'][0]['unidade_gestora'] = $obj->unidade_gestora;
                                $registros['form'][0]['unidade_orcamentaria']    = $obj->unidade_orcamentaria;
                                $registros['form'][0]['subunidade_orcamentaria'] = $obj->subunidade_orcamentaria;
                                $registros['form'][0]['unidade_lotacao'] = $obj->unidade_lotacao;
                                $registros['form'][0]['cargo_funcao']    = $obj->cargo_funcao;
                                $registros['form'][0]['tipo_salario']    = $obj->tipo_salario; // 1 - Normal, 2 - Hora/aula
                                
                                $json = json_encode($registros);
                                file_put_contents($file, $json);
                                
                                echo "OK";
                            } else {
                                $registros = array('form' => array());
                                $registros['form'][0]['referencia'] = "0";
                                $json = json_encode($registros);
                                file_put_contents($file, $json);
                                
                                echo "Servidor não localizado";
                            }
                            
                            // Fechar conexão
                            unset($res);
                            unset($pdo);
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'gravar_evento' : {
                    try {
//                        $op = trim(filter_input(INPUT_POST, 'op'));
//                        $hs = trim(filter_input(INPUT_POST, 'hs'));
//                        $id = trim(filter_input(INPUT_POST, 'id'));
//                        $id_cliente = trim(filter_input(INPUT_POST, 'id_cliente'));
//                        $nome  = trim(filter_input(INPUT_POST, 'nome'));
//                        $email = trim(filter_input(INPUT_POST, 'email'));
//                        $senha = trim(filter_input(INPUT_POST, 'senha'));
//                        $senha_confirme = trim(filter_input(INPUT_POST, 'senha_confirme'));
//                        $situacao = trim(filter_input(INPUT_POST, 'situacao'));
//                        $administrar_portal = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'administrar_portal')) );
//                        $lancar_eventos = (int)preg_replace("/[^0-9]/", "", "0" . trim(filter_input(INPUT_POST, 'lancar_eventos')) );
//                        
//                        $file = '../downloads/user_' . $hs . '.json';
//                        if (file_exists($file)) {
//                            unlink($file);
//                        }
//                        
//                        if ($hs !== $hash) {
//                            echo "Acesso Inválido";
//                        } else 
//                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//                            echo "E-mail inválido!";
//                        } else {
//                            $cnf = Configuracao::getInstancia();
//                            $pdo = $cnf->db('', '');
//                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//                            
//                            if ($op === "inserir_usuario") {
//                                $dao = Dao::getInstancia();
//                                $id = $dao->getCountID("ADM_USUARIO", "ID");
//                                $stm = $pdo->prepare(
//                                      "Insert Into ADM_USUARIO ("
//                                    . "    id         "
//                                    . "  , id_cliente "
//                                    . "  , nome       "
//                                    . "  , e_mail     "
//                                    . "  , senha      "
//                                    . "  , exe_ano    "
//                                    . "  , situacao   "
//                                    . "  , administrar_portal "
//                                    . "  , lancar_eventos     "
//                                    . ") values ("
//                                    . "    :id         "
//                                    . "  , :id_cliente "
//                                    . "  , :nome       "
//                                    . "  , :email      "
//                                    . "  , :senha      "
//                                    . "  , extract(year from current_date) "
//                                    . "  , :situacao   "
//                                    . "  , :administrar_portal "
//                                    . "  , :lancar_eventos     "
//                                    . ")");
//                                $stm->execute(array(
//                                    ':id'         => $id,
//                                    ':id_cliente' => $id_cliente,
//                                    ':nome'       => $nome,
//                                    ':email'      => $email,
//                                    ':senha'      => hashSenhaUser($senha),
//                                    ':situacao'   => $situacao,
//                                    ':administrar_portal' => $administrar_portal,
//                                    ':lancar_eventos'     => $lancar_eventos
//                                ));
//                            } else
//                            if ($op === "editar_usuario") {
//                                $stm = $pdo->prepare(
//                                      "Update ADM_USUARIO u Set "
//                                    . "    u.nome       = :nome "
//                                    . "  , u.e_mail     = :email "
//                                    . "  , u.situacao   = :situacao "
//                                    . "  , u.administrar_portal = :administrar_portal "
//                                    . "  , u.lancar_eventos     = :lancar_eventos "
//                                    . "  , u.id_cliente = :id_cliente "
//                                    . "where u.id = :id   ");
//                                $stm->execute(array(
//                                    ':nome'       => $nome,
//                                    ':email'      => $email,
//                                    ':situacao'   => $situacao,
//                                    ':administrar_portal' => $administrar_portal,
//                                    ':lancar_eventos'     => $lancar_eventos,
//                                    ':id_cliente' => $id_cliente,
//                                    ':id'         => $id
//                                ));
//                                
//                                if (($senha !== "") && ($senha === $senha_confirme)) {
//                                    $stm = $pdo->prepare(
//                                          "Update ADM_USUARIO u Set "
//                                        . "    u.senha = :senha "
//                                        . "where u.id = :id   ");
//                                    $stm->execute(array(
//                                        ':senha' => hashSenhaUser($senha),
//                                        ':id'    => $id
//                                    ));
//                                }
//                            }
//                            
//                            $pdo->commit();
//                            
//                            $registros = array('form' => array());
//
//                            $registros['form'][0]['id']    = str_pad(intval($id), 3, "0", STR_PAD_LEFT);
//                            $registros['form'][0]['nome']  = $nome;
//                            $registros['form'][0]['email'] = $email;
//
//                            $json = json_encode($registros);
//                            file_put_contents($file, $json);
//                            
//                            echo "OK";
//                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'excluir_evento' : {
                    try {
                        $to = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'to'))) );
                        $id = strip_tags( preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id'))) );
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        
                        echo "<strong>Usuário não habilitado para esta tarefa</strong>.<br>Entre em contato com a direção.";
                        exit;
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $stm = $pdo->prepare(
                                  "Delete from REMUN_EVENTO "
                                . "where (id_cliente = :id_cliente)"
                                . "  and (id_evento  = :id_evento) ");
                            $stm->execute(array(
                                  ':id_cliente' => $to
                                , ':id_evento'  => $id
                            ));
                            
                            $pdo->commit();
                            
                            echo "OK";
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
