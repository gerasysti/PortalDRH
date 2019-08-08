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
    $lista_clientes  = "";
    $lista_unidades  = "";
    $lista_lotacoes  = "";
    $lista_cargos    = "";
    $lista_situacoes = "";
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    
    $sql = 
         "Select "
       . "    u.id_cliente       "
       . "  , u.id as id_unidade "
       . "  , u.descricao   "
       . "  , u.cnpj        "
       . "  , coalesce(g.acesso, 0) as acesso "
       . "  , coalesce(g.lancar_eventos, 0) as lancar "
       . "from REMUN_UNID_GESTORA u "
       . "  inner join ADM_USUARIO_UNID_GESTORA g on (g.id_cliente = u.id_cliente and g.id_unid_gestora = u.id and g.id_usuario = {$usuario['id']} and g.acesso = 1) "
       . "where (u.id_cliente = {$usuario['cliente']}) "
       . "order by "
       . "    u.descricao ";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $lista_unidades .= "<option value='{$obj->id_unidade}' class='optionChild'>{$obj->descricao}</option>";
    }
    
    $sql = 
         "Select "
       . "    u.id_cliente "
       . "  , u.id_lotacao "
       . "  , u.descricao "
       . "  , coalesce(g.acesso, 0) as acesso "
       . "  , coalesce(g.lancar_eventos, 0) as lancar "
       . "from REMUN_UNID_LOTACAO u "
       . "  inner join ADM_USUARIO_UNID_LOTACAO g on (g.id_cliente = u.id_cliente and g.id_unid_lotacao = u.id_lotacao and g.id_usuario = {$usuario['id']} and g.acesso = 1) "
       . "where (u.id_cliente = {$usuario['cliente']}) "
       . "order by "
       . "    u.descricao ";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $lista_lotacoes .= "<option value='{$obj->id_lotacao}' class='optionChild'>{$obj->descricao}</option>";
    }
    
    $sql = 
         "Select "
       . "    c.id_cliente "
       . "  , c.id_cargo   "
       . "  , c.descricao  "
       . "from REMUN_CARGO_FUNCAO c "
       . "where (c.id_cliente = {$usuario['cliente']}) "
       . "order by "
       . "    c.descricao ";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $lista_cargos .= "<option value='{$obj->id_cargo}' class='optionChild'>{$obj->descricao}</option>";
    }
    
    $sql = 
         "Select distinct "
       . "    s.id_est_funcional as codigo "
       . "  , s.descr_est_funcional as descricao "
       . "from REMUN_BASE_CALC_MES s "
       . "where (s.id_cliente = {$usuario['cliente']}) ";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $lista_situacoes .= "<option value='{$obj->codigo}' class='optionChild'>{$obj->descricao}</option>";
    }
    
    // Fechar conexão PDO
    unset($qry);
    unset($pdo);
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
                            <h2><strong>Servidores Cadastrados</strong></h2>
                            <p><strong>Lista de servidores cadastrados para o cliente</strong></p>
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
                                            <!--
                                            <label for="id_cliente" class="col-sm-1 control-label padding-label">Cliente</label>
                                            <div class="col-sm-3 padding-field">
                                                <select class="form-control chosen-select" id="id_cliente" <?php // echo (intval($_SESSION['acesso']['id_cliente']) !== 0?"disabled":"");?>>
                                                    <option value="0" class="optionChild">Selecione o cliente</option>
                                                    <?php // echo $lista_clientes;?>
                                                </select>
                                            </div>
                                            -->
                                            <label for="id_unidade" class="col-sm-1 control-label padding-label">UG</label>
                                            <div class="col-sm-3 padding-field">
                                                <select class="form-control chosen-select" id="id_unidade">
                                                    <option value="0" class="optionChild">Todas</option>
                                                    <?php echo $lista_unidades;?>
                                                </select>
                                            </div>
                                            
                                            <label for="id_lotacao" class="col-sm-1 control-label padding-label">Lotação</label>
                                            <div class="col-sm-3 padding-field">
                                                <select class="form-control chosen-select" id="id_lotacao">
                                                    <option value="0" class="optionChild">Todas</option>
                                                    <?php echo $lista_lotacoes;?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-sm-4 padding-field">
                                                <div class="input-group">
                                                    <input type="text" class="form-control text lg-text" id="pesquisa" value=""/>
                                                    <div class="input-group-addon" style="padding: 0px; padding-left : 4px;">
                                                        <button id="btn_consultar" class="btn ra-round btn-primary lg-text" onclick="consultarServidor('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')" title="Executar Consulta"><i class="glyph-icon icon-search"></i></button>
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
                                
                                <div class="box-wrapper" id="tabela-servidores">
                                    &nbsp;
                                </div>
                                
                                <button id="btn_home" class="btn btn-round btn-primary lg-button botao-orbit-top" onclick="home_controle()" title="Fechar"><i class="glyph-icon icon-close"></i></button>
                                <!--<button id="btn_inserir" class="btn btn-round btn-primary lg-button botao-orbit-bottom" onclick="inserirUsuarioXXX('<?php // echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php // echo 'lg_' . $_SESSION['acesso']['us']?>')" title="Inserir Registro"><i class="glyph-icon icon-plus"></i></button>-->
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12" id="panel_cadastro">
                        <a href="#" class="btn btn-md btn-black overlay-button" data-style="light" data-theme="bg-black" data-opacity="60" id="link_overlay"></a>
                        <div class="panel">
                            <div class="panel-body">
                                <h2 class="title-hero">
                                    <strong>Cadastro</strong>
                                    <input type="hidden" id="op" value="inserir_servidor">
                                    <input type="hidden" id="hs" value="<?php echo $_SESSION['acesso']['id'];?>">
                                    <input type="hidden" id="cliente" value="<?php echo $_SESSION['acesso']['id_cliente'];?>">
                                </h2>
                                
                                <div class="box-wrapper">
                                    
                                    <div class="form-horizontal bordered-row">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_servidor" class="col-sm-3 control-label padding-label">ID Servidor</label>
                                                    <div class="col-sm-1 padding-field">
                                                        <input type="text" class="form-control" maxlength="4" id="id_servidor" readonly>
                                                    </div>
                                                    <label for="matricula" class="col-sm-1 control-label padding-label">Matrícula</label>
                                                    <div class="col-sm-1 padding-field">
                                                        <input type="text" class="form-control" maxlength="11" id="matricula" readonly>
                                                    </div>
                                                    <label for="dt_admissao" class="col-sm-1 control-label padding-label">Admissão</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <input type="text" class="form-control" maxlength="10" id="dt_admissao" value="01/01/2019" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="nome" class="col-sm-3 control-label padding-label">Nome do Servidor</label>
                                                    <div class="col-sm-9 padding-field">
                                                        <input type="text" data-parsley-maxlength="60" placeholder="Nome completo..." required class="form-control" id="nome" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="rg" class="col-sm-3 control-label padding-label">RG</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <input type="text" class="form-control" maxlength="11" id="rg" readonly>
                                                    </div>
                                                    <div class="col-sm-1 padding-field">
                                                        <input type="text" class="form-control" maxlength="10" id="dt_nascimento" value="01/01/2019" readonly>
                                                    </div>
                                                    <!--
                                                    <label for="dt_nascimento" class="col-sm-3 control-label padding-label">Data Nascimento</label>
                                                    <div class="col-sm-1 padding-field">
                                                        <input type="text" class="form-control" maxlength="10" id="dt_nascimento" value="01/01/2019" readonly>
                                                    </div>
                                                    -->
                                                    <label for="cpf" class="col-sm-1 control-label padding-label">CPF</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <input type="text" class="form-control" maxlength="11" id="cpf" readonly>
                                                    </div>
                                                    <label for="pis_pasep" class="col-sm-1 control-label padding-label">PIS/PASEP</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <input type="text" class="form-control" maxlength="11" id="pis_pasep" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_cliente_cadastro" class="col-sm-3 control-label padding-label">Cliente</label>
                                                    <div class="col-sm-9 padding-field">
                                                        <select class="form-control chosen-select" id="id_cliente_cadastro" disabled>
                                                            <option value="0" class="optionChild">Selecione o cliente</option>
                                                            <?php echo $lista_clientes;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_unid_gestora" class="col-sm-3 control-label padding-label">Unidade Gestora</label>
                                                    <div class="col-sm-9 padding-field">
                                                        <select class="form-control chosen-select" id="id_unid_gestora" style="width: 100%;" disabled>
                                                            <option value="0" class="optionChild">* UG NÃO INFORMADA</option>
                                                            <?php echo $lista_unidades;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_unid_lotacao" class="col-sm-3 control-label padding-label">Unidade de Lotação</label>
                                                    <div class="col-sm-9 padding-field">
                                                        <select class="form-control chosen-select" id="id_unid_lotacao" style="width: 100%;" disabled>
                                                            <option value="0" class="optionChild">* LOTAÇÃO NÃO INFORMADA</option>
                                                            <?php echo $lista_lotacoes;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_cargo_atual" class="col-sm-3 control-label padding-label">Cargo atual</label>
                                                    <div class="col-sm-5 padding-field">
                                                        <select class="form-control chosen-select" id="id_cargo_atual" style="width: 100%;" disabled>
                                                            <option value="0" class="optionChild">* CARGO/FUNÇÃO NÃO INFORMADO</option>
                                                            <?php echo $lista_cargos;?>
                                                        </select>
                                                    </div>
                                                    <label for="situacao" class="col-sm-1 control-label padding-label">Situação</label>
                                                    <div class="col-sm-3 padding-field">
                                                        <select class="form-control chosen-select" id="situacao" style="width: 100%;" disabled>
                                                            <option value="0" class="optionChild">A Definir</option>
                                                            <?php echo $lista_situacoes;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <div class="col-sm-3 padding-field">&nbsp;</div>
                                                    <div class="col-sm-6 padding-field">
                                                        <div class="checkbox checkbox-primary">
                                                            <label>
                                                                <input class="custom-checkbox" type="checkbox" name="sem_uso" id="ativo" value="1" disabled>
                                                                Cadastro Ativo
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>    
                                            </div> 
                                        </div>

                                        <div class="bg-default">
                                            <button class="btn btn-primary" onclick="fechar_cadastro()" id="btn_form_fechar">Fechar</button>
                                            <button class="btn btn-primary pull-right" onclick="salvarUsuarioXXX()" id="btn_form_salvar" disabled>Salvar</button>
                                        </div>
                                    </div>
                                    <!--</form>-->
                                    
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-default" data-toggle="modal" data-target=".box_confirme" id="box_confirme"></button>
                        <button class="btn btn-default" data-toggle="modal" data-target=".box_alerta"   id="box_alerta"></button>
                        <button class="btn btn-default" data-toggle="modal" data-target=".box_erro"     id="box_erro"></button>
                    </div>

                    <script type="text/javascript">
                        $(function () {
                            $('#id_cliente').val(<?php echo $_SESSION['acesso']['id_cliente']?>);
                            $('#id_cliente').trigger('chosen:updated');
                            $('#pesquisa').focus();
                        });
                        
                        $('#link_overlay').fadeOut();
                        
                        $('#box_confirme').fadeOut();
                        $('#box_alerta').fadeOut();
                        $('#box_erro').fadeOut();
                        
                        $(".input-mask").inputmask();
                        $('input[type="checkbox"].custom-checkbox').uniform();
                        
                        
                        fechar_cadastro();
                        //formatar_checkbox();
                        
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
                    </script>
                </div>
        
    </body>
        