<!DOCTYPE html>
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * 
    Select
        e.id_cliente
      , e.id_evento
      , e.codigo
      , e.descricao
      , e.sem_uso
    from REMUN_EVENTO e
    where (e.id_cliente = 15019) // PREFEITURA MINUCIPAL DE DOM ELISEU
      and (e.tipo_lancamento <> 9)
 * 
 */
    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';
    
    session_start();
    
    if (!isset($_SESSION['acesso'])) {
        $mensagem = 
              "<div id='page-content'>"
            . "  <div class='panel'>"
            . "    <div class='col-md-12'>"
            . "      <div class='content-box pad20A'>"
            . "        <div class='ribbon ribbon-tr'>"
            . "          <div class='bg-red'>Erro</div>"
            . "        </div>"
            . "        <p class='pad10B'><b>Esta sessão expiou!</b></p>"
            . "        <p>As sessões de acesso ao sistema têm um tempo limite de duração para melhor segurança. Com isso é necessário que você <strong>efetue login</strong> novamente para ter acesso aos dados.</p>"
            . "      </div>"
            . "    </div>"
            . "  </div>"
            . "</div>";
        
        echo $mensagem;
        exit();
    }
    
    // Montar listas
    $lista_clientes = "";
    $lista_unidades = "";
    $lista_orcament = "";
    $lista_anomes   = "";
    $lista_eventos  = "";

    $lista_unidades_lancar = "";
    $lista_orcament_lancar = "";
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Carregar Usuario
    $sql = 
          "Select "
        . "    u.* "
        . "  , coalesce(nullif(trim(c.titulo_portal), ''), c.nome, 'Administração do Sistema') as nome_cliente "
        . "  , coalesce(c.id, u.id_cliente) as cliente "
        . "from ADM_USUARIO u "
        . "  left join ADM_CLIENTE c on (c.id = u.id_cliente) "
        . "where u.e_mail = '{$_SESSION['acesso']['us']}'";
        
    $qry = $pdo->query($sql);    
    $dados   = $qry->fetchAll(PDO::FETCH_ASSOC);
    $usuario = null;
    foreach($dados as $item) {
        $usuario = $item;
    }
    
    // Carregar Clientes
    $sql = 
         "Select "
        ."    u.id "
        ."  , u.nome "
        ."  , u.cnpj "
        ."  , u.municipio_nome "
        ."  , u.municipio_uf "
        ."  , trim(coalesce(u.titulo_portal, u.nome)) as titulo_portal "
        ."from ADM_CLIENTE u "
        ."order by "
        ."    trim(coalesce(u.titulo_portal, u.nome))";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $lista_clientes .= "<option value='{$obj->id}' class='optionChild'>{$obj->titulo_portal}</option>";
    }
    
    // Carregar Unidades Gestoras
    $sql = 
         "Select "
       . "    u.id_cliente       "
       . "  , u.id as id_unidade "
       . "  , u.descricao   "
       . "  , u.cnpj        "
       . "  , coalesce(g.acesso, 0) as acesso "
       . "  , coalesce(g.lancar_eventos, 0) as lancar "
       . "from REMUN_UNID_GESTORA u "
       . "  left join ADM_USUARIO_UNID_GESTORA g on (g.id_cliente = u.id_cliente and g.id_unid_gestora = u.id and g.id_usuario = {$usuario['id']}) "
       . "where (u.id_cliente = {$usuario['cliente']}) "
       . "order by "
       . "    u.descricao ";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        if ((int)$obj->acesso === 1) $lista_unidades .= "<option value='{$obj->id_unidade}' class='optionChild'>{$obj->descricao}</option>";
        if (((int)$obj->acesso === 1) && ((int)$obj->lancar === 1)) $lista_unidades_lancar .= "<option value='{$obj->id_unidade}' class='optionChild'>{$obj->descricao}</option>";
    }
    
    // Carregar Unidades Orçamentárias
    $sql = 
         "Select "
       . "    o.id_cliente "
       . "  , o.id as id_orcament "
       . "  , o.descricao "
       . "  , coalesce(g.acesso, 0) as acesso "
       . "  , coalesce(g.lancar_eventos, 0) as lancar "
       . "from REMUN_UNID_ORCAMENT o "
       . "  inner join ( "
       . "    Select "
       . "        x.id_cliente "
       . "      , x.id_unid_gestora "
       . "    from ADM_USUARIO_UNID_GESTORA x "
       . "    where x.id_cliente = {$usuario['cliente']} "
       . "      and x.id_usuario = {$usuario['id']} "
       . "      and x.acesso     = 1 "
       . "  ) a on (a.id_cliente = o.id_cliente and a.id_unid_gestora = o.id_unid_gestora) "
       . "  left join ADM_USUARIO_UNID_ORCAMENT g on (g.id_cliente = o.id_cliente and g.id_unid_orcament = o.id and g.id_usuario = {$usuario['id']}) "
       . "where (o.id_cliente = {$usuario['cliente']}) "
       . "order by "
       . "    o.id_unid_gestora "
       . "  , o.descricao ";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        if ((int)$obj->acesso === 1) $lista_orcament .= "<option value='{$obj->id_orcament}' class='optionChild'>{$obj->descricao}</option>";
        if (((int)$obj->acesso === 1) && ((int)$obj->lancar === 1)) $lista_orcament_lancar .= "<option value='{$obj->id_orcament}' class='optionChild'>{$obj->descricao}</option>";
    }
    
    $sql = 
         "Select "
       . "    e.id_cliente "
       . "  , e.id_evento  "
       . "  , e.descricao  "
       . "  , e.codigo     "
       . "from REMUN_EVENTO e "
       . "where (e.tipo_lancamento <> 9) "
       . "  and (e.sem_uso    = 'N') "
       . "  and (e.id_cliente = {$usuario['cliente']}) "
       . "order by "
       . "    e.descricao ";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $lista_eventos .= "<option value='{$obj->id_evento}' class='optionChild'>{$obj->descricao} ({$obj->codigo})</option>";
    }
    
    // Fechar conexão PDO
    unset($qry);
    unset($pdo);
    
    $ano = date("Y");
    $inicio_data = strtotime("{$ano}/01/01");
    $final_data  = strtotime("{$ano}/12/01");
    $data_corrente = $inicio_data;
    while ($data_corrente <= $final_data) {
        $nr_mes_ano = date('Ym',    $data_corrente);
        $ds_mes_ano = date('m/Y', $data_corrente);
        $data_corrente = strtotime( date('Y/m/01/', $data_corrente).' +1 month');
        
        $lista_anomes .= "<option value='{$nr_mes_ano}' class='optionChild'>{$ds_mes_ano}</option>";
    }
    
    $competencia_atual = date('Ym');
?>
    <body>
                <style>
                    .optionGroup {
                        font-weight: bold;
                        font-style: italic;
                        font-variant:small-caps;
                    }                
                    .optionChild {
                        padding-left: 15px;
                    }                    
                    .lg-text {
                        height: 40px;
                        margin: 0 auto;
                    }
                    .lg-button {
                        width : 40px;
                        height: 40px;
                        margin: 0 auto;
                    }
                    .botao-orbit-top {
                        display : scroll;
                        position: fixed;
                        top     : 100px;
                        right   : 40px;
                        box-shadow: 0px 0px 1em #666;
                        -webkit-box-shadow: 0px 0px 1em #666;
                        -moz-box-shadow: 0px 0px 1em #666;
/*                        box-shadow: -5px -10px 30px #900, 6px 9px 15px #090;
                        -webkit-box-shadow: -5px -10px 30px #900, 6px 9px 15px #090;
                        -moz-box-shadow: -5px -10px 30px #900, 6px 9px 15px #090;*/
                    }                    
                    .botao-orbit-bottom {
                        display : scroll;
                        position: fixed;
                        bottom  : 40px;
                        right   : 40px;
                        box-shadow: 0px 0px 1em #666;
                        -webkit-box-shadow: 0px 0px 1em #666;
                        -moz-box-shadow: 0px 0px 1em #666;
                    }                    
                    /* Centralizar na verticação as células de tabelas renderizadas pela classe "dataTable()"*/
                    table.dataTable tbody td {
                        vertical-align: middle;
                    }            
                </style>
        
                <div id="page-content">
                    
                    <div class="col-md-12" id="panel_titulo">
                        <div id="page-title">
                            <h2><strong>Lançar Eventos Mensais</strong></h2>
                            <p><strong>Controle para lançamento de eventos mensais</strong></p>
                        </div>
                    </div>
                    
                    <div class="col-md-12" id="panel_pesquisa">
                    
                        <div class="panel">

                            <div class="panel-body">
                                <h3 class="title-hero">
                                    Favor selecionar os filtros necessário para pesquisa
                                </h3>

                                <div class="box-wrapper form-horizontal">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="ano_mes_pesquisa" class="col-sm-1 control-label padding-label">Competência</label>
                                            <div class="col-sm-2 padding-field">
                                                <select class="form-control chosen-select" id="ano_mes_pesquisa">
                                                    <option value="0" class="optionChild">Selecione a Competência</option>
                                                    <?php echo $lista_anomes;?>
                                                </select>
                                            </div>
                                            
                                            <label for="id_unidade" class="col-sm-1 control-label padding-label">UG</label>
                                            <div class="col-sm-3 padding-field">
                                                <select class="form-control chosen-select" id="id_unidade" onchange="listar_uniades_orcament('#id_unidade', '#lista_orcament_pesquisa', 'id_orcament')">
                                                    <option value="0" class="optionChild">Todas as Unidades Gestoras</option>
                                                    <?php echo $lista_unidades;?>
                                                </select>
                                            </div>
                                            
                                            <label for="id_orcament" class="col-sm-1 control-label padding-label">UO</label>
                                            <div class="col-sm-4 padding-field">
                                                <div class="input-group">
                                                    <div style="padding: 0px; margin: 0px;" id="lista_orcament_pesquisa">
                                                        <select class="form-control chosen-select" id="id_orcament" style="width: 100%;">
                                                            <option value="0" class="optionChild">Todas as Unidades Orçamentárias</option>
                                                            <?php echo $lista_orcament;?>
                                                        </select>
                                                    </div>
                                                    <div class="input-group-addon" style="padding: 0px; padding-left : 4px;">
                                                        <input type="hidden" id="pesquisa" value=""/>
                                                        <button id="btn_consultar" class="btn ra-round btn-primary lg-text" onclick="consultarEventosLancados('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')" title="Executar Consulta"><i class="glyph-icon icon-search"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    
                    </div>    
                    
                    <div class="col-md-12" id="panel_resultado">
                        <div class="panel">
                            <div class="panel-body">
                                <div id="page-wait">
                                    <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait">

                                    </a>
                                </div>
                                
                                <div class="box-wrapper" id="tabela-lancamentos">
                                    &nbsp;
                                </div>
                                
                                <button id="btn_home" class="btn btn-round btn-primary lg-button botao-orbit-top" onclick="home_controle()" title="Fechar"><i class="glyph-icon icon-close"></i></button>
                                <button id="btn_inserir" class="btn btn-round btn-primary lg-button botao-orbit-bottom" onclick="lancarEventos('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')" title="Lançar Eventos"><i class="glyph-icon icon-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12" id="panel_cadastro">
                        <a href="#" class="btn btn-md btn-black overlay-button" data-style="light" data-theme="bg-black" data-opacity="60" id="link_overlay"></a>
                        
                        <input type="hidden" id="op" value="novo_lancamento">
                        <input type="hidden" id="hs" value="<?php echo $_SESSION['acesso']['id'];?>">
                        <input type="hidden" id="hoje" value="<?php echo date('d/m/Y');?>">
                        <input type="hidden" id="cliente" value="<?php echo $_SESSION['acesso']['id_cliente'];?>">
                        <input type="hidden" id="competencia_atual" value="<?php echo $competencia_atual;?>">
                        
                        <div class="content-box">
                            <h3 class="content-box-header bg-default">
                                <i class="glyph-icon icon-money"></i>
                                <strong>Controle do Evento Mensal</strong>
                                <span class="header-buttons-separator">
                                    <button class="btn btn-primary btn-lg btn-round botao-orbit-top" onclick="salvarControleEventoMensal('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')" id="btn_form_salvar" style="margin: 0px; padding: 0px; right: 90px;" title="Salvar dados de Controle"><i class="glyph-icon icon-save"></i></button>
                                    <button class="btn btn-primary btn-lg btn-round botao-orbit-top" onclick="fechar_lancamentos()"  id="btn_form_fechar" style="margin: 0px; padding: 0px;" title="Voltar à Pesquisa"><i class="glyph-icon icon-arrow-left"></i></button>
                                </span>
                            </h3>
                            <div class="content-box-wrapper">
                                
                                <div class="form-horizontal bordered-row">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" style="margin: 2px;">
                                                <label for="controle" class="col-sm-2 control-label padding-label">Controle</label>
                                                <div class="col-sm-1 padding-field">
                                                    <input type="hidden" id="id_cliente" value="0">
                                                    <input type="text" class="form-control text lg-text" maxlength="10" id="controle" readonly>
                                                </div>
                                                <label for="data" class="col-sm-1 control-label padding-label">Data</label>
                                                <div class="col-sm-1 padding-field">
                                                    <input type="text" class="form-control text lg-text" maxlength="11" id="data" readonly>
                                                    <input type="hidden" id="hora">
                                                </div>
                                                <label for="ano_mes" class="col-sm-1 control-label padding-label">Competência</label>
                                                <div class="col-sm-2 padding-field">
                                                    <select class="form-control chosen-select" id="ano_mes">
                                                        <option value="0" class="optionChild">Selecione a Competência</option>
                                                        <?php echo $lista_anomes;?>
                                                    </select>
                                                </div>
                                                <label class="col-sm-4 control-label padding-label">
                                                    <a href="javascript:void(0);" title="Finalizar Lançamento" onclick="finalizar_lancamento()">
                                                        <span class="bs-badge badge-primary"><i class="glyph-icon icon-check-circle-o"></i> Finalizar Lançamento</span>
                                                    </a>
                                                    &nbsp;
                                                    <a href="javascript:void(0);" title="Finalizar Lançamento" onclick="reabrir_lancamento()">
                                                        <span class="bs-badge badge-warning"><i class="glyph-icon icon-circle-o"></i> Reabrir Lançamento</span>
                                                    </a>
                                                </label>
                                            </div>
                                            
                                            <div class="form-group" style="margin: 2px;">
                                                <label for="id_unid_gestora" class="col-sm-2 control-label padding-label">Unidade Gestora</label>
                                                <div class="col-sm-10 padding-field">
                                                    <select class="form-control chosen-select" id="id_unid_gestora" style="width: 100%;" onchange="listar_uniades_orcament('#id_unid_gestora', '#lista_orcament_cadastro', 'id_unid_orcament')">
                                                        <option value="0" class="optionChild">Selecione a Unidade Gestora</option>
                                                        <?php echo $lista_unidades_lancar;?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group" style="margin: 2px;">
                                                <label for="id_unid_orcament" class="col-sm-2 control-label padding-label">Unidade Orçamentária</label>
                                                <div class="col-sm-10 padding-field">
                                                    <div style="padding: 0px; margin: 0px;" id="lista_orcament_cadastro">
                                                        <select class="form-control chosen-select" id="id_unid_orcament" style="width: 100%;">
                                                            <option value="0" class="optionChild">Selecione a Unidade Orçamentária</option>
                                                            <?php echo $lista_orcament_lancar;?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group" style="margin: 2px;">
                                                <label for="id_evento" class="col-sm-2 control-label padding-label">Evento de Lançamento</label>
                                                <div class="col-sm-10 padding-field">
                                                    <select class="form-control chosen-select" id="id_evento" style="width: 100%;">
                                                        <option value="0" class="optionChild">Selecione o Evento</option>
                                                        <?php echo $lista_eventos;?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group" style="margin: 2px;">
                                                <div class="col-sm-2 padding-field">&nbsp;</div>
                                                <div class="col-sm-5 padding-field">
                                                    <div class="checkbox checkbox-primary">
                                                        <label>
                                                            <input type="checkbox" class="custom-checkbox" name="importado" id="importado" value="1" disabled>
                                                            Lançamentos importados pelo <strong>Remuneratu$</strong>
                                                        </label>
                                                    </div>
                                                </div>
                                                <label for="situacao" class="col-sm-2 control-label padding-label">Situação</label>
                                                <div class="col-sm-3 padding-field">
                                                    <select class="form-control chosen-select" id="situacao" style="width: 100%;" disabled>
                                                        <option value="0" class="optionChild">Aberto</option>
                                                        <option value="1" class="optionChild">Finalizado</option>
                                                        <option value="2" class="optionChild">Cancelado</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <!--
                        <div class="panel">
                            <div class="panel-body">
                                <h2 class="title-hero">
                                    <strong>Lancamento de Eventos Mensais</strong>
                                    <input type="hidden" id="op" value="novo_lancamento">
                                    <input type="hidden" id="hs" value="<?php // echo $_SESSION['acesso']['id'];?>">
                                    <input type="hidden" id="hoje" value="<?php // echo date('d/m/Y');?>">
                                    <input type="hidden" id="cliente" value="<?php // echo $_SESSION['acesso']['id_cliente'];?>">
                                    <input type="hidden" id="competencia_atual" value="<?php // echo $competencia_atual;?>">
                                </h2>
                                
                                <div class="box-wrapper">
                                    
                                    <div class="form-horizontal bordered-row">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="controle" class="col-sm-3 control-label padding-label">Controle</label>
                                                    <div class="col-sm-1 padding-field">
                                                        <input type="text" class="form-control text lg-text" maxlength="10" id="controle" readonly>
                                                    </div>
                                                    <label for="data_lancamento" class="col-sm-1 control-label padding-label">Data</label>
                                                    <div class="col-sm-1 padding-field">
                                                        <input type="text" class="form-control text lg-text" maxlength="11" id="data_lancamento" readonly>
                                                    </div>
                                                    <label for="ano_mes" class="col-sm-1 control-label padding-label">Competência</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <select class="form-control chosen-select" id="ano_mes">
                                                            <option value="0" class="optionChild">Selecione a Competência</option>
                                                            <?php echo $lista_anomes;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_unid_gestora" class="col-sm-3 control-label padding-label">Unidade Gestora</label>
                                                    <div class="col-sm-9 padding-field">
                                                        <select class="form-control chosen-select" id="id_unid_gestora" style="width: 100%;">
                                                            <option value="0" class="optionChild">Selecione a Unidade Gestora</option>
                                                            <?php echo $lista_unidades_lancar;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_unid_lotacao" class="col-sm-3 control-label padding-label">Unidade de Lotação</label>
                                                    <div class="col-sm-9 padding-field">
                                                        <select class="form-control chosen-select" id="id_unid_lotacao" style="width: 100%;">
                                                            <option value="0" class="optionChild">Selecione a Unidade de Lotação</option>
                                                            <?php echo $lista_lotacoes_lancar;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_evento" class="col-sm-3 control-label padding-label">Evento de Lançamento</label>
                                                    <div class="col-sm-9 padding-field">
                                                        <select class="form-control chosen-select" id="id_evento" style="width: 100%;">
                                                            <option value="0" class="optionChild">Selecione o Evento</option>
                                                            <?php echo $lista_eventos;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <div class="col-sm-3 padding-field">&nbsp;</div>
                                                    <div class="col-sm-5 padding-field">
                                                        <div class="checkbox checkbox-primary">
                                                            <label>
                                                                <input class="custom-checkbox" type="checkbox" name="importado" id="importado" value="1" disabled>
                                                                Lançamentos importados pelo <strong>Remuneratu$</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <label for="situacao" class="col-sm-1 control-label padding-label">Situação</label>
                                                    <div class="col-sm-3 padding-field">
                                                        <select class="form-control chosen-select" id="situacao" style="width: 100%;" disabled>
                                                            <option value="0" class="optionChild">Aberto</option>
                                                            <option value="1" class="optionChild">Finalizado</option>
                                                            <option value="2" class="optionChild">Cancelado</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>

                                        <div class="bg-default">
                                            <button class="btn btn-primary" onclick="fechar_cadastro()" id="btn_form_fechar">Fechar</button>
                                            <button class="btn btn-primary pull-right" onclick="salvarUsuarioXXX()" id="btn_form_salvar" disabled>Salvar</button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        -->
                        <button class="btn btn-default" data-toggle="modal" data-target=".box_servidor" id="box_servidor"></button>
                        <button class="btn btn-default" data-toggle="modal" data-target=".box_confirme" id="box_confirme"></button>
                        <button class="btn btn-default" data-toggle="modal" data-target=".box_informe"  id="box_informe"></button>
                        <button class="btn btn-default" data-toggle="modal" data-target=".box_alerta"   id="box_alerta"></button>
                        <button class="btn btn-default" data-toggle="modal" data-target=".box_erro"     id="box_erro"></button>
                        <button class="btn btn-default" data-toggle="modal" data-target=".box_pesquisa" id="box_pesquisa"></button>
                    </div>

                    <div class="col-md-12" id="panel_lancamentos">
                        <div class="content-box">
                            <h3 class="content-box-header bg-default">
                                <i class="glyph-icon icon-users"></i>
                                Servidores
                                <span class="header-buttons-separator">
                                    <a href="javascript:void(0);" class="icon-separator" title="Ajuda">
                                        <i class="glyph-icon icon-question"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="icon-separator refresh-button" data-style="dark" data-theme="bg-white" data-opacity="40" title="Atualizar" onclick="carregarServidoresLancamento()">
                                        <i class="glyph-icon icon-refresh"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="icon-separator remove-button" data-animation="flipOutX" title="Inserir Servidores" onclick="adicionar_servidor()">
                                        <i class="glyph-icon icon-plus"></i>
                                    </a>
                                    <!--
                                    <a href="javascript:void(0);" class="icon-separator remove-button" data-animation="flipOutX" title="Salvar Lançamentos" onclick="salvar_lancamentos()">
                                        <i class="glyph-icon icon-save"></i>
                                    </a>
                                    -->
                                </span>
                            </h3>
                            <div class="content-box-wrapper" id="tabela-lancamento_servidores" style="margin: 0px; padding: 0px;">
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade bs-example-modal box_servidor" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg"><!--modal-lg-->
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title"> Inserir Servidor</h4>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="form-horizontal bordered-row">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_servidor" class="col-sm-2 control-label padding-label">Servidor</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <input type="hidden" id="sequencia" value="0">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control text lg-text proximo_campo" maxlength="8" id="id_servidor" onkeypress="return somente_numero(event);">
                                                            <div class="input-group-addon" style="padding: 0px; padding-left : 4px;">
                                                                <button id="btn_consultar_servidor" class="btn ra-round btn-primary lg-text proximo_campo" onclick="buscar_registro_servidor()" title="Buscar Servidor"><i class="glyph-icon icon-search"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-8 padding-field">
                                                        <input type="text" class="form-control text lg-text proximo_campo" maxlength="8" id="nm_servidor" readonly>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="dt_admissao" class="col-sm-2 control-label padding-label">Admissão</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <input type="text" class="form-control text lg-text proximo_campo" id="dt_admissao" readonly>
                                                    </div>
                                                    <div class="col-sm-8 padding-field">
                                                        <input type="text" class="form-control text lg-text proximo_campo" id="cargo_funcao" readonly>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="quant" class="col-sm-2 control-label padding-label">Quantidade</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <input type="text" class="form-control text lg-text text-right proximo_campo" maxlength="10" id="quant" onkeypress="return somente_numero(event);">
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="valor" class="col-sm-2 control-label padding-label">Valor (R$)</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <input type="text" class="form-control text lg-text text-right proximo_campo" maxlength="10" id="valor" onkeypress="return somente_numero_decimal(event);">
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="obs" class="col-sm-2 control-label padding-label">Observações</label>
                                                    <div class="col-sm-10 padding-field">
                                                        <input type="text" class="form-control text lg-text proximo_campo" maxlength="40" id="obs" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal" id="btn_servidor_fechar">Fechar</button>
                                    <button type="button" class="btn btn-primary proximo_campo" id="btn_servidor_confirmar" onclick="confirmar_servidor()">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade bs-example-modal box_pesquisa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title"> Buscar Servidor</h4>
                                </div>
                                <div class="modal-body" id="tabela-servidores">
                                    <div class='remove-border glyph-icon demo-icon tooltip-button icon-spin-5 icon-spin' title='' data-original-title='icon-spin-5'></div>
                                    <p>Carregando lista de servidores.... aguarde!</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal" id="btn_pesquisa_fechar">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script type="text/javascript">
                        /* Ao pressionar uma tecla em um campo que seja de class="proximo_campo" */ 
                        $('#tabela-lancamento_servidores, .box_servidor, .box_pesquisa').on('keyup', '.proximo_campo', function(e) {
                            /* 
                             * Verifica se o evento é Keycode (para IE e outros browsers)
                             * se não for pega o evento Which (Firefox)
                             */
                            var tecla = (e.keyCode?e.keyCode:e.which);
                            if (tecla === 13) {
                                /* Guarda o seletor do campo que foi pressionado Enter */
                                var campo =  $('.proximo_campo');
                                /* Pega o indice do elemento */
                                var indice = campo.index(this);
                                /*
                                 * Soma mais um ao indice e verifica se não é null
                                 * se não for é porque existe outro elemento
                                 */
                                if (campo[indice + 1] !== null) {
                                    /* Adiciona mais 1 no valor do indice */
                                    var proximo = campo[indice + 1];
                                    try {
                                        /* Passa o foco para o proximo elemento */
                                        proximo.focus();
                                    } catch (e) {
                                        ;
                                    }
                                }
                            }
                            /* Impede o sumbit caso esteja dentro de um form */
                            e.preventDefault(e);
                            return false;
                        });
                        
                        $(function () {
                            $('#id_cliente').val(<?php echo $_SESSION['acesso']['id_cliente']?>);
                            //$('#id_cliente').trigger('chosen:updated');
                            //$('#pesquisa').focus();
                            $('#ano_mes_pesquisa').val(<?php echo $competencia_atual;?>);
                            $('#ano_mes_pesquisa').trigger('chosen:updated');
                            $('#ano_mes').val(<?php echo $competencia_atual;?>);
                            $('#ano_mes').trigger('chosen:updated');
                        });
                        
                        $('#link_overlay').fadeOut();
                        
                        $('#box_servidor').fadeOut();
                        $('#box_confirme').fadeOut();
                        $('#box_informe').fadeOut();
                        $('#box_alerta').fadeOut();
                        $('#box_erro').fadeOut();
                        $('#box_pesquisa').fadeOut();
                        
                        $(".input-mask").inputmask();
                        $('input[type="checkbox"].custom-checkbox').uniform();
                        
                        $('.box_servidor').on('shown.bs.modal', function(event) {
                            $('#id_servidor').focus();
                        });
                        
                        $('.box_pesquisa').on('shown.bs.modal', function(event) {
                            $('.dataTables_filter input').focus();
                        });
                        
                        fechar_lancamentos();
                        //formatar_checkbox();
                        consultarEventosLancados('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>');
                        
                        // Função "overlay" extraída do arquivo "overlay.js"
                        $('.overlay-button').click(function(){
                            var loadertheme = $(this).attr('data-theme');
                            var loaderopacity = $(this).attr('data-opacity');
                            var loaderstyle = $(this).attr('data-style');

                            var loader = '<div id="loader-overlay" class="ui-front loader ui-widget-overlay ' + loadertheme + ' opacity-' + loaderopacity + '"><img src="../../assets/images/spinner/loader-' + loaderstyle + '.gif" alt="" /></div>';

                            if ( $('#loader-overlay').length ) {
                                $('#loader-overlay').remove();
                            }

                            $('body').append(loader);
//
//                            $('#loader-overlay').fadeIn('fast');
//                            setTimeout(function() {
//                              $('#loader-overlay').fadeOut('fast');
//                            }, 3000);
                        });
                        
                        function listar_uniades_orcament(origem, quadro, elemento) {
                            var ls = "";
                            var id_sessao  = $('#id_sessao').val();
                            var lg_sessao  = $('#lg_sessao').val();
                            var informacao = "Todas as Unidades Orçamentárias";
                            var ug = $(origem).val();
                            
                            if (origem === 'id_unid_orcament') {
                                informacao = "Selecione a Unidade Orçamentária";
                            }
                            
                            listar_unidades_orcamentarias(id_sessao, lg_sessao, ug, function(retorno){
                                ls += "<select class='form-control chosen-select' id='" + elemento + "' style='width: 100%;'>";
                                ls += "    <option value='0' class='optionChild'>" + informacao + "</option>";
                                ls += retorno;
                                ls += "</select>";
                                
                                $(quadro).html(ls);
                                $(quadro + " .chosen-select").chosen();
                                $(quadro + " .chosen-search").append('<i class="glyph-icon icon-search"></i>');
                                $(quadro + " .chosen-single div").html('<i class="glyph-icon icon-caret-down"></i>');

                                $(quadro).val("0");
                                $(quadro).trigger('chosen:updated');
                            });
                        }
                        
                        function fechar_lancamentos() {
                            fechar_cadastro();
                            document.getElementById("panel_lancamentos").style.display  = 'none';
                            var i_linha = document.getElementById("linha_" + parseInt($('#controle').val()));
                            if (i_linha !== null) {
                                var qtde_servidores = parseInt($('#qtde_servidores').val());
                                var colunas = i_linha.getElementsByTagName('td');
                                colunas[6].firstChild.nodeValue = qtde_servidores;
                            }
                        }
                        
                        function salvar_lancamentos() {
                            var situacao = parseInt($('#situacao').val());
                            if (situacao === 0) {
                                var controle = parseFloat("0" + $('#controle').val());
                                var qtde_servidores = parseInt($('#qtde_servidores').val());
                                var tipo_lancamento = parseInt($('#tipo_lancamento_' + parseFloat("0" + $('#controle').val()) ).val());

                                var ids_servidores  = "#";
                                var qts_servidores  = "#";
                                var vls_servidores  = "#";
                                var referencia      = 0;

                                for (var i = 1; i < qtde_servidores; i++) {
                                    referencia = controle + "_" + i;
                                    if (typeof($('#id_servidor_' + referencia)) !== "undefined") {
                                    //if ( document.getElementById("id_servidor_" + referencia) !== null ) {
                                        ids_servidores += "||" + $('#id_servidor_' + referencia).val();
                                        qts_servidores += "||" + $('#quant_' + referencia).val();
                                        vls_servidores += "||" + $('#valor_' + referencia).val();
                                    }
                                }

                                ids_servidores = ids_servidores.replace("#||", "");
                                qts_servidores = qts_servidores.replace("#||", "");
                                vls_servidores = vls_servidores.replace("#||", "");
                                
                                // Salvar quantidades
                                if (tipo_lancamento === 0) {
                                    if ((ids_servidores !== '#') && (qts_servidores !== '#')) {
                                        salvarServidoresLancamento(ids_servidores, qts_servidores, null, function(){
                                            mensagem_informe("Valores gravados com sucesso.");
                                        });
                                    } else {
                                        mensagem_alerta("Favor informe as quantidades do evento para cada servidor");
                                    }
                                } else
                                // Salvar valores
                                if (tipo_lancamento === 1) {
                                    if ((ids_servidores !== '#') && (qts_servidores !== '#')) {
                                        salvarServidoresLancamento(ids_servidores, null, vls_servidores, function(){
                                            var id_sessao = $('#id_sessao').val();
                                            var lg_sessao = $('#lg_sessao').val();
                                            carregarServidoresLancamento(id_sessao, lg_sessao);

                                            mensagem_informe("Valores gravados com sucesso.");
                                        });
                                    } else {
                                        mensagem_alerta("Favor informe as quantidades do evento para cada servidor");
                                    }
                                }
                            } else {
                                var texto = $('#situacao option:selected').text();
                                mensagem_alerta("Este lançamento está <strong>" + texto + "</strong> e não poderá ser alterado.<br>Entre em contato com a direção.");
                            }
                        }
                        
                        function buscar_registro_servidor() {
                            var situacao = parseInt($('#situacao').val());
                            if (situacao === 0) {
                                var id_servidor = parseFloat("0" + $('#id_servidor').val());
                                if (id_servidor > 0) {
                                    var id_cliente = parseInt("0" + $('#id_cliente').val());
                                    var id_unid_gestora = parseInt("0" + $('#id_unid_gestora').val());
                                    carregar_registro_servidor(id_cliente, id_servidor, id_unid_gestora, function (data) {
                                        if ( id_unid_gestora !== parseInt("0" + data.form[0].unid_gest) ) {
                                            $('#id_servidor').val("");
                                            $('#nm_servidor').val("");
                                            $('#dt_admissao').val("");
                                            $('#cargo_funcao').val("");
                                            mensagem_alerta(
                                                "Servidor(a) <strong>" + data.form[0].nome + "</strong> não pertence a esta Unidade Gestora!<br><br><ul>" + 
                                                "<li>Unidade Gestora : <strong>" + data.form[0].unidade_gestora + "</strong></li>" + 
                                                "<li>Unidade Orçamentária : <strong>" + data.form[0].unidade_orcamentaria + "</strong></li>" + 
                                                "<li>Sub-unidade Orçamentária : <strong>" + data.form[0].subunidade_orcamentaria + "</strong></li>" + 
                                                "<li>Cargo/Função : <strong>" + data.form[0].cargo_funcao + "</strong></li></ul>");
                                        } else
                                        if ( parseInt("0" + data.form[0].situacao) !== 1 ) {
                                            $('#nm_servidor').val("");
                                            $('#dt_admissao').val("");
                                            $('#cargo_funcao').val("");
                                            mensagem_alerta("Servidor(a) <strong>" + data.form[0].nome + "</strong> não está ativo(a)");
                                        } else {
                                            $('#id_servidor').val(data.form[0].id_servidor);
                                            $('#nm_servidor').val(data.form[0].nome + " (CPF : " + data.form[0].cpf_formatado + ")");
                                            $('#dt_admissao').val(data.form[0].dt_admissao);
                                            $('#cargo_funcao').val(data.form[0].cargo_funcao);
                                            $('#quant').focus();
                                        }
                                    });
                                } else {
                                    pesquisarServidores();
                                }
                            }
                        }
                        
                        function configurarTabelaServidorLocal() {
                            // Configurando Tabela
                            // https://datatables.net/manual/styling/classes#nowrap
                            var table = $('#datatable-responsive-serv').DataTable({
                                "paging": true,
                                "pageLength": 10, // Quantidade de registros na paginação
                                "lengthChange": false,
                                "searching": true,
                                "ordering": true,
                                "info": true,
                                "autoWidth": true,
                                "processing": true,
                                "columns": [
                                    { "width": "10px" },   // 0. ID
                                    null,                  // 1. Nome
                                    null,                  // 2. Cargo/Função
                                    { "width": "10px" }    // 3. Controles
                                ],
                                "columnDefs": [
                                    {"orderable": false, "targets": 0}, // ID
                                    {"orderable": false, "targets": 3}  // Controles
                                ],
                                "order": [[1, 'asc']], // <-- Ordenação 
                                "language": {
                                        "paginate": {
                                            "first"   : "Primeira", //"<<", // Primeira página
                                            "last"    : "Útima",    //">>", // Última página
                                            "next"    : "Próxima",  //">",  // Próxima página
                                            "previous": "Anterior", //"<"   // Página anterior
                                        },
                                        "aria": {
                                            "sortAscending" : ": ativar para classificação ascendente na coluna",
                                            "sortDescending": ": ativar para classificação descendente na coluna"
                                        },
                                        "info": "Exibindo _PAGE_ / _PAGES_",
                                        "infoEmpty": "Sem dados para exibição",
                                        "infoFiltered":   "(Filtrado a partir de _MAX_ registros)",
                                        "zeroRecords": "Sem registro(s) para exibição",
                                        "lengthMenu": "Exibindo _MENU_ registro(s)",
                                        "loadingRecords": "Por favor, aguarde - carregando...",
                                        "processing": "Processando...",
                                        "search": "Localizar:"
                                }
                            });

                            $('.dataTables_filter input').attr("placeholder", "Localizar...");
                            $('.dataTables_filter input').focus();
                        }
                        
                        function pesquisarServidores() {
                            var id = $('#id_sessao').val();
                            var us = $('#lg_sessao').val();
                            
                            var hash  = id.split("_");
                            var email = us.split("_");
                            var params = {
                                'ac' : 'consultar_servidor-pesquisa',
                                'id' : hash[1],
                                'us' : email[1],
                                'to' : $('#cliente').val(),
                                'ug' : $('#id_unid_gestora').val()
                            };

                            if (parseInt(params.to) === 0) {
                                mensagem_alerta("Usuário <strong>não está associado ao cadastro de um cliente</strong>!<br>Favor, entre em contato com o suporte da plataforma.");
                            } else {
                                // Iniciamos o Ajax 
                                $.ajax({
                                    // Definimos a url
                                    url : './servidor_dao.php',
                                    // Definimos o tipo de requisição
                                    type: 'post',
                                    // Definimos o tipo de retorno
                                    dataType : 'html',
                                    // Dolocamos os valores a serem enviados
                                    data: params,
                                    // Antes de enviar ele alerta para esperar
                                    beforeSend : function(){
                                        $('#nm_servidor').val("Carregando tabela de servidores... Aguarde!");
                                        $('#tabela-servidores').html("<div class='remove-border glyph-icon demo-icon tooltip-button icon-spin-5 icon-spin' title='' data-original-title='icon-spin-5'></div><p>Carregando lista de professores.... aguarde!</p>");
                                    },
                                    // Colocamos o retorno na tela
                                    success : function(data){
                                        $('#tabela-servidores').html(data);
                                        $('#nm_servidor').val("");
                                        configurarTabelaServidorLocal();
                                        $('#box_pesquisa').trigger("click");
                                    },
                                    error: function (request, status, error) {
                                        $('#tabela-servidores').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
                                    }
                                });  
                                // Finalizamos o Ajax
                            }
                        }
                        
                        function selecionar_servidor(id) {
                            var referencia = id.replace("selecionar_servidor_", "");

                            $('#id_servidor').val( $('#id_servidor_' + referencia).val() );
                            $('#nm_servidor').val( $('#nome_' + referencia).val() + " (CPF : " + $('#cpf_' + referencia).val() + ")");
                            $('#dt_admissao').val( $('#dt_admissao_' + referencia).val() );
                            $('#cargo_funcao').val( $('#cargo_funcao_' + referencia).val() );
                            
                            $('#btn_pesquisa_fechar').trigger("click");
                            $('#qtde_hora_aula_normal').focus();
                        }
                        
                        function adicionar_servidor() {
                            var situacao = parseInt($('#situacao').val());
                            if (situacao === 0) {
                                var controle = parseFloat("0" + $('#controle').val());
                                if (controle === 0.0) {
                                    mensagem_alerta("Salve, primeiramente, os dados de controle do Evento Mensal.");
                                } else {
                                    //var qtde_servidores = parseInt($('#qtde_servidores').val());
                                    var tipo_lancamento = parseInt($('#tipo_lancamento_' + parseFloat("0" + $('#controle').val()) ).val());

                                    $('#sequencia').val("0");
                                    $('#id_servidor').val("");
                                    $('#nm_servidor').val("");
                                    $('#dt_admissao').val("");
                                    $('#cargo_funcao').val("");
                                    $('#quant').val("");
                                    $('#valor').val("");
                                    $('#obs').val("");
                                    
                                    $('#quant').prop('readonly', (tipo_lancamento !== 0));
                                    $('#valor').prop('readonly', (tipo_lancamento !== 1));
                                    
                                    $('#box_servidor').trigger("click");
                                }
                            } else {
                                var texto = $('#situacao option:selected').text();
                                mensagem_alerta("Este lançamento está <strong>" + texto + "</strong> e não poderá ser alterado.<br>Entre em contato com a direção.");
                            }
                        }

                        function confirmar_servidor() {
                            var situacao = parseInt($('#situacao').val());
                            if (situacao === 0) {
                                var controle = parseFloat("0" + $('#controle').val());
                                if (controle === 0.0) {
                                    mensagem_alerta("Salve, primeiramente, os dados de controle do Evento Mensal.");
                                } else {
                                    var qtde_servidores = parseInt($('#qtde_servidores').val());
                                    var tipo_lancamento = parseInt($('#tipo_lancamento_' + parseFloat("0" + $('#controle').val()) ).val());
                                    var msg = "";
                                    var mrc = "<i class='glyph-icon icon-edit'></i>&nbsp;";

                                    if ($('#nm_servidor').val()  === "") msg += mrc + "Servidor<br>";
                                    if ((tipo_lancamento === 0) && ($('#quant').val() === "")) msg += mrc + "Quantidade<br>";
                                    if ((tipo_lancamento === 1) && ($('#valor').val() === "")) msg += mrc + "Valor (R$)<br>";

                                    if (msg.trim() !== "") {
                                        mensagem_alerta( "<p><strong>Os campos listados têm seu preenchimento obrigatório:</strong> <br><br>" + msg + "</p>" );
                                    } else {
                                        var sequencia   = $('#sequencia').val();
                                        var id_servidor = $('#id_servidor').val();
                                        var obs   = $('#obs').val();
                                        var quant = null;
                                        var valor = null;

                                        verificar_lancamento_servidor(id_servidor, function(verifica) {
                                            if (verifica === "OK") {
                                                mensagem_alerta("Lançamento já realizado para este servidor!");
                                            } else {
                                                if (tipo_lancamento === 0) { // Lançamento pela Quantidade
                                                    quant = parseFloat("0" + $('#quant').val());
                                                } else
                                                if (tipo_lancamento === 1) { // Lançamento pela Valor (R$)
                                                    valor = parseFloat("0" + $('#valor').val());
                                                }

                                                sequencia = (qtde_servidores + 1);
                                                salvarServidorLancamento(sequencia, id_servidor, quant, valor, obs, function(retorno){
                                                    if (retorno !== "OK") {
                                                        $('#quant').focus();
                                                    } else {
                                                        qtde_servidores += 1;
                                                        $('#qtde_servidores').val( qtde_servidores );

                                                        if (qtde_servidores === 1) {
                                                            carregarServidoresLancamento();
                                                        } else {
                                                            var file_json = "../downloads/lanc_srv_" + $('#hs').val() + ".json";
                                                            $.getJSON(file_json, function(data){
                                                                $("#datatable-responsive_serv").append(data.form[0].table_tr);
                                                            });
                                                        }

                                                        $('#sequencia').val("0");
                                                        $('#id_servidor').val("");
                                                        $('#nm_servidor').val("");
                                                        $('#quant').val("");
                                                        $('#valor').val("");
                                                        $('#obs').val("");

                                                        $('#id_servidor').focus();
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }
                            } else {
                                var texto = $('#situacao option:selected').text();
                                mensagem_alerta("Este lançamento está <strong>" + texto + "</strong> e não poderá ser alterado.<br>Entre em contato com a direção.");
                            }
                        }
                        
                        function salvar_lancamento_servidor(id, tipo_lanc) {
                            var referencia  = id;
                            var quant = null;
                            var valor = null;
                            
                            if (tipo_lanc === 0) { // Lançamento pela Quantidade
                                referencia = referencia.replace("quant_", "");
                                quant      = parseFloat("0" + $('#' + id).val());
                            } else
                            if (tipo_lanc === 1) { // Lançamento pela Valor (R$)
                                referencia = referencia.replace("valor_", "");
                                valor      = parseFloat("0" + $('#' + id).val());
                            }
                        
                            //var controle   = $('#controle_' + referencia).val();
                            var sequencia  = $('#sequencia_' + referencia).val();
                            //var id_cliente = $('#id_cliente_' + referencia).val();
                            //var id_unid_gestora = $('#id_unid_gestora_' + referencia).val();
                            //var id_unid_lotacao = $('#id_unid_lotacao_' + referencia).val();
                            //var id_evento   = $('#id_evento_' + referencia).val();
                            //var ano_mes     = $('#ano_mes_' + referencia).val();
                            var id_servidor = $('#id_servidor_' + referencia).val();
                            
                            //salvarServidorLancamento(controle, sequencia, id_cliente, id_unid_gestora, id_unid_lotacao, id_evento, ano_mes, id_servidor, quant, valor, function(retorno){
                            salvarServidorLancamento(sequencia, id_servidor, quant, valor, "", function(retorno){
                                if (retorno !== "OK") {
                                    $('#' + id).focus();
                                }
                            });
                        }
                    </script>
                </div>
        
    </body>
        