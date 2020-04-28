<!DOCTYPE html>
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
    
    // Montar lista de Unidades/Órgãos
    $lista_clientes = "";
    
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
        ."  , trim(coalesce(u.titulo_portal, u.nome)) as titulo_portal "
        ."from ADM_CLIENTE u "
        ."order by "
        ."    trim(coalesce(u.titulo_portal, u.nome))";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $lista_clientes .= "<option value='{$obj->id}' class='optionChild'>{$obj->titulo_portal}</option>";
    }
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
                            <h2><strong>Tabela de Usuários</strong></h2>
                            <p><strong>Lista de usuários cadastrados</strong></p>
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
                                            <label class="col-sm-1 control-label padding-label">Tipo de pesquisa</label>
                                            <div class="col-sm-2 padding-field">
                                                <select class="form-control chosen-select" id="tipo"> <!--style="width: 150px;"-->
                                                    <optgroup label="Tipo">
                                                        <!-- Este teste de agrupamento de dados funciona perfeiramente:-->
                                                        <!--<option value="0" class="optionGroup" disabled>Exercício</option>-->
                                                        <option value="0" class="optionChild">Todos</option>
                                                        <option value="1" class="optionChild">Nome do usuário</option>
                                                        <option value="2" class="optionChild">E-mail</option>
                                                    </optgroup>    
                                                </select>
                                                <div>&nbsp;</div>
                                            </div>
                                            
                                            <div class="col-sm-3 padding-field">
                                                <div class="input-group">
                                                    <input type="text" class="form-control text lg-text" id="pesquisa" value=""/>
                                                    <div class="input-group-addon" style="padding: 0px; padding-left : 4px;">
                                                        <button id="btn_consultar" class="btn ra-round btn-primary lg-text" onclick="consultarUsuario('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')" title="Executar Consulta"><i class="glyph-icon icon-search"></i></button>
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
                                <div class="box-wrapper" id="tabela-usuarios">
<!--                                    
                                    <table id='tb_remunecacao' cellspacing='0' width='100%' class="table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" align="center">Cargo / Função</th>
                                                <th colspan="16" align="center">Referências</th>
                                            </tr>
                                            <tr>
                                                <th align="center">0</th>
                                                <th align="center">1</th>
                                                <th align="center">2</th>
                                                <th align="center">3</th>
                                                <th align="center">4</th>
                                                <th align="center">5</th>
                                                <th align="center">6</th>
                                                <th align="center">7</th>
                                                <th align="center">8</th>
                                                <th align="center">9</th>
                                                <th align="center">10</th>
                                                <th align="center">11</th>
                                                <th align="center">12</th>
                                                <th align="center">13</th>
                                                <th align="center">14</th>
                                                <th align="center">15</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    -->
                                </div>
                                <button id="btn_home" class="btn btn-round btn-primary lg-button botao-orbit-top" onclick="home_controle()" title="Fechar"><i class="glyph-icon icon-close"></i></button>
                                <button id="btn_inserir" class="btn btn-round btn-primary lg-button botao-orbit-bottom" onclick="inserirUsuario('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')" title="Inserir Registro"><i class="glyph-icon icon-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12" id="panel_cadastro">
                        <a href="#" class="btn btn-md btn-black overlay-button" data-style="light" data-theme="bg-black" data-opacity="60" id="link_overlay"></a>
                        <div class="panel">
                            <div class="panel-body">
                                <h2 class="title-hero">
                                    <strong>Cadastro</strong>
                                    <input type="hidden" id="op" value="inserir_usuario">
                                    <input type="hidden" id="hs" value="<?php echo $_SESSION['acesso']['id'];?>">
                                </h2>
                                
                                <div class="box-wrapper">
                                    
                                    <!--<form class="form-horizontal bordered-row" id="demo-form" data-parsley-validate="" action="./usuario_dao.php">-->
                                    <div class="form-horizontal bordered-row">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group" style="margin: 2px;">
                                                    <label class="col-sm-3 control-label padding-label">ID</label>
                                                    <div class="col-sm-6 padding-field">
                                                        <input type="hidden" id="id_cliente_padrao" value="<?php echo $_SESSION['acesso']['id_cliente'];?>">
                                                        <input type="text" class="form-control" style="width: 70px;" maxlength="6" readonly id="id">
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label class="col-sm-3 control-label padding-label">Cliente</label>
                                                    <div class="col-sm-6 padding-field">
                                                        <!--<input type="text" placeholder="Cliente" required class="form-control" id="id_cliente">-->
                                                        <select class="form-control chosen-select" id="id_cliente" onchange="carregar_unidades_permissao('0')" <?php echo (intval($_SESSION['acesso']['id_cliente']) !== 0?"disabled":"");?>>
                                                            <option value="0" class="optionChild">Administração do Sistema</option>
                                                            <?php echo $lista_clientes;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label class="col-sm-3 control-label padding-label">Nome</label>
                                                    <div class="col-sm-6 padding-field">
                                                        <input type="text" data-parsley-maxlength="150" placeholder="Nome completo" required class="form-control" id="nome">
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label class="col-sm-3 control-label padding-label">Email</label>
                                                    <div class="col-sm-6 padding-field">
                                                        <input type="text" data-parsley-type="email" placeholder="Email address" required class="form-control" id="email">
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <div class="col-sm-3 padding-field">&nbsp;</div>
                                                    <div class="col-sm-6 padding-field">
                                                        <div class="checkbox checkbox-primary">
                                                            <label>
                                                                <input class="custom-checkbox" type="checkbox" name="lancar_eventos" id="lancar_eventos" value="1">
                                                                Lançar Eventos pelo Portal
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>    
                                                <div class="form-group" style="margin: 2px;">
                                                    <div class="col-sm-3 padding-field">&nbsp;</div>
                                                    <div class="col-sm-6 padding-field">
                                                        <div class="checkbox checkbox-primary">
                                                            <label>
                                                                <input class="custom-checkbox" type="checkbox" name="lancar_ch_professores" id="lancar_ch_professores" value="1">
                                                                Lançar Cargar Horária de Professores pelo Portal
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>    
                                            </div> 
                                            
                                            <div class="col-md-6">
                                                <div class="form-group" style="margin: 2px;">
                                                    <label class="col-sm-3 control-label padding-label">Último acesso</label>
                                                    <div class="col-sm-6 padding-field">
                                                        <input type="text" class="form-control" readonly id="ultimo_acesso">
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label class="col-sm-3 control-label padding-label">Senha</label>
                                                    <div class="col-sm-6 padding-field">
                                                        <input type="password" class="form-control" maxlength="20" id="senha">
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label class="col-sm-3 control-label padding-label">Confirmar senha</label>
                                                    <div class="col-sm-6 padding-field">
                                                        <input type="password" data-parsley-equalto="#senha" class="form-control" id="senha_confirme" maxlength="20">
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label class="col-sm-3 control-label padding-label">Situação</label>
                                                    <div class="col-sm-6 padding-field">
<!--                                                        
                                                        <div class="checkbox checkbox-primary">
                                                            <label>
                                                                <input class="custom-checkbox" type="checkbox" name="situacao" id="situacao" value="1" checked>
                                                                Usuário ativo
                                                            </label>
                                                        </div>
                                                        -->
                                                        <select class="form-control chosen-select" id="situacao">
                                                            <option value="0" class="optionChild">Inativo</option>
                                                            <option value="1" class="optionChild">Ativo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <div class="col-sm-3 padding-field">&nbsp;</div>
                                                    <div class="col-sm-6 padding-field">
                                                        <div class="checkbox checkbox-primary">
                                                            <label>
                                                                <input class="custom-checkbox" type="checkbox" name="administrar_portal" id="administrar_portal" value="1">
                                                                Administrar Portal da Instituição
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>    
                                            </div>  
                                            
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p style="font-size: 5px;">&nbsp;</p>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-default">
                                            <button class="btn btn-primary" onclick="document.getElementById('panel_permissoes').style.display = 'none';fechar_cadastro();" id="btn_form_fechar">Fechar</button>
                                            <button class="btn btn-primary pull-right" onclick="salvarUsuario()" id="btn_form_salvar">Salvar</button>
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

                    <div class="col-md-12" id="panel_permissoes">
                        <div class="panel">
                            <div class="panel-body">
                                <h2 class="title-hero">
                                    <strong>Permissões</strong>
                                </h2>
                                <div class="box-wrapper">
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="content-box">
                                                <h3 class="content-box-header bg-blue">
                                                    Acesso às Unidades Gestoras
                                                    <div class='header-buttons-separator'>
                                                        <a href='javascript:preventDefault();' class='icon-separator' title="Atualizar" onclick="carregar_unidades_permissao('1')">
                                                            <i class='glyph-icon icon-refresh'></i>
                                                        </a>
                                                    </div>
                                                </h3>
                                                <div class="content-box-wrapper" id="box-unidade_gestora">
                                                    Relação de unidades gestoras de acordo com o cliente selecionado.
                                                    <!--This content boxes has the header with .bg-primary class.-->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="content-box">
                                                <h3 class="content-box-header bg-blue">
                                                    Acesso às Unidades Orçamentárias
                                                    <div class='header-buttons-separator'>
                                                        <a href='javascript:preventDefault();' class='icon-separator' title="Atualizar" onclick="carregar_unidades_permissao('2')">
                                                            <i class='glyph-icon icon-refresh'></i>
                                                        </a>
                                                    </div>
                                                </h3>
                                                <div class="content-box-wrapper" id="box-unidade_orcament">
                                                    Relação de unidades orçamentárias de acordo com o cliente selecionado.
                                                    <!--This content boxes has the header with .bg-primary class.-->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="content-box">
                                                <h3 class="content-box-header bg-blue">
                                                    Acesso às Unidades de Lotação
                                                    <div class='header-buttons-separator'>
                                                        <a href='javascript:preventDefault();' class='icon-separator' title="Atualizar" onclick="carregar_unidades_permissao('3')">
                                                            <i class='glyph-icon icon-refresh'></i>
                                                        </a>
                                                    </div>
                                                </h3>
                                                <div class="content-box-wrapper" id="box-unidade_lotacao">
                                                    Relação de unidades de lotação de acordo com o cliente selecionado.
                                                    <!--This content boxes has the header with .bg-primary class.-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script type="text/javascript">
                        $('#link_overlay').fadeOut();
                        
                        $('#box_confirme').fadeOut();
                        $('#box_alerta').fadeOut();
                        $('#box_erro').fadeOut();
                        
                        $(".input-mask").inputmask();
                        $('input[type="checkbox"].custom-checkbox').uniform();
                        
                        document.getElementById("panel_permissoes").style.display  = 'none';
                        fechar_cadastro();
                        formatar_checkbox();
/*
    $('input[type="checkbox"].custom-checkbox').uniform();
    $('input[type="radio"].custom-radio').uniform();
    $('.custom-select').uniform();

    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');

    $('.checker span').append('<i class="glyph-icon icon-check"></i>');
    $('.radio span').append('<i class="glyph-icon icon-circle"></i>');
 
*/                        
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
                        
                        function carregar_unidades_permissao(lista) {
                            var id_cliente = $('#id_cliente').val();
                            var id_usuario = $('#id').val();
                            
                            if ((lista === '0') || (lista === '1')) {
                                $('#box-unidade_gestora').html("<strong>Carregando unidades gestoras...</strong>");
                                carregar_unidade_gestora_allows(id_cliente, id_usuario, function(retorno){
                                    $('#box-unidade_gestora').html(retorno);
                                    configurarTabUnidadeGestoraPermissao();
                                    $('input[type="checkbox"].custom-checkbox').uniform();
                                });
                            }
                            
                            if ((lista === '0') || (lista === '2')) {
                                $('#box-unidade_orcament').html("<strong>Carregando unidades orçamentárias...</strong>");
                                carregar_unidade_orcament_allows(id_cliente, id_usuario, function(retorno){
                                    $('#box-unidade_orcament').html(retorno);
                                    configurarTabUnidadeOrcamentPermissao();
                                    $('input[type="checkbox"].custom-checkbox').uniform();
                                });
                            }
                            
                            if ((lista === '0') || (lista === '3')) {
                                $('#box-unidade_lotacao').html("<strong>Carregando unidades de lotação...</strong>");
                                carregar_unidade_lotacao_allows(id_cliente, id_usuario, function(retorno){
                                    $('#box-unidade_lotacao').html(retorno);
                                    configurarTabUnidadeLotacaoPermissao();
                                    $('input[type="checkbox"].custom-checkbox').uniform();
                                });
                            }
                        }
                        
                        function des_marcar_acesso(id) {
                            var referencia = id.replace("marcar_acesso_", "");
                            var acesso = parseInt("0" + $('#acesso_' + referencia).val());
                            
                            if ($('#img_acesso_' + referencia).hasClass('icon-check-square-o')) $('#img_acesso_' + referencia).removeClass('icon-check-square-o');
                            if ($('#img_acesso_' + referencia).hasClass('icon-square-o')) $('#img_acesso_' + referencia).removeClass('icon-square-o');
                            
                            if (acesso === 1) {
                                $('#acesso_' + referencia).val("0");
                                $('#img_acesso_' + referencia).addClass('icon-square-o');
                            } else {
                                $('#acesso_' + referencia).val("1");
                                $('#img_acesso_' + referencia).addClass('icon-check-square-o');
                            }
                            
                            // Gravar na base de dados
                            if (parseInt("0" + $('#id').val()) !== 0) {
                                var controles = referencia.split("_");
                                if (controles[0] === "ugt") {
                                    gravar_permissao_ugt(controles[1], controles[2], $('#id').val(), $('#acesso_' + referencia).val());
                                } else
                                if (controles[0] === "uoc") {
                                    gravar_permissao_uoc(controles[1], controles[2], $('#id').val(), $('#acesso_' + referencia).val());
                                } else
                                if (controles[0] === "ulo") {
                                    gravar_permissao_ulo(controles[1], controles[2], $('#id').val(), $('#acesso_' + referencia).val());
                                } 
                            }
                        }
                        
                        if ( $('#id_cliente_padrao').val() !== "0" ) {
                            $('#btn_consultar').trigger("click");
                        }
                    </script>
                </div>
        
    </body>
        