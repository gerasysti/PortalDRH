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
    $lista_ufs = "";
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 
          "Select     "
        . "    e.id   "
        . "  , e.nome "
        . "  , e.uf   "
        . "from ADM_ESTADO e "
        . "where e.id <> 99  "
        . "order by "
        . "    e.uf ";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $lista_ufs .= "<option value='{$obj->uf}' class='optionChild'>{$obj->nome}</option>";
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
                            <h2><strong>Tabela de Clientes</strong></h2>
                            <p><strong>Lista de clientes cadastrados</strong></p>
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
                                                        <option value="1" class="optionChild">Nome do cliente</option>
                                                        <option value="2" class="optionChild">CNPJ</option>
                                                    </optgroup>    
                                                </select>
                                                <div>&nbsp;</div>
                                            </div>
                                            
                                            <div class="col-sm-3 padding-field">
                                                <div class="input-group">
                                                    <input type="text" class="form-control text lg-text" id="pesquisa" value=""/>
                                                    <div class="input-group-addon" style="padding: 0px; padding-left : 4px;">
                                                        <button id="btn_consultar" class="btn ra-round btn-primary lg-text" onclick="consultarCliente('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')" title="Executar Consulta"><i class="glyph-icon icon-search"></i></button>
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
                                <div class="box-wrapper" id="tabela-clientes">
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
                                <button id="btn_inserir" class="btn btn-round btn-primary lg-button botao-orbit-bottom" onclick="inserirCliente('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')" title="Inserir Registro"><i class="glyph-icon icon-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12" id="panel_cadastro">
                        <a href="#" class="btn btn-md btn-black overlay-button" data-style="light" data-theme="bg-black" data-opacity="60" id="link_overlay"></a>
                        <div class="panel">
                            <div class="panel-body">
                                <h2 class="title-hero">
                                    <strong>Cadastro</strong>
                                    <input type="hidden" id="op" value="inserir_cliente">
                                    <input type="hidden" id="hs" value="<?php echo $_SESSION['acesso']['id'];?>">
                                </h2>
                                
<!--                                
                                <div class="panel-title example-box-wrapper">
                                    <div class="content-box">
                                        <h3 class="content-box-header bg-blue">
                                            <i class="glyph-icon icon-users"></i>
                                            Cadastro de Usuários
                                        </h3>
                                        <div class="header-buttons">
                                            <a href="#" class="btn btn-xs btn-link">X</a>
                                        </div>    
                                    </div>
                                </div>
                                -->
                                
                                
                                
                                
                                
<!--                                
                                <div class="col-sm-2">
                                    <h3 class="title-hero">
                                        Cadastro de Usuários
                                    </h3>
                                </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-round btn-primary right">
                                        <i class="glyph-icon icon-close"></i>
                                    </button>
                                </div>

                                <h3 class="title-hero">
                                    Cadastro de Usuários
                                </h3>
                                -->
                                
                                <div class="box-wrapper">
                                    
                                    <!--<form class="form-horizontal bordered-row" id="demo-form" data-parsley-validate="" action="./usuario_dao.php">-->
                                    <div class="form-horizontal bordered-row form-wizard">
                                        <div id="form-wizard-3" class="form-wizard">
                                            <ul>
                                                <li id="tab_1">
                                                    <a href="#step-1" data-toggle="tab">
                                                        <label class="wizard-step">1</label>
                                                        <span class="wizard-description">
                                                            Identificação
                                                            <small>ID, Nome, Cnpj...</small>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li id="tab_2">
                                                    <a href="#step-2" data-toggle="tab">
                                                        <label class="wizard-step">2</label>
                                                        <span class="wizard-description">
                                                            Endereço
                                                            <small>Informe endereço completo</small>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li id="tab_3">
                                                    <a href="#step-3" data-toggle="tab">
                                                        <label class="wizard-step">3</label>
                                                        <span class="wizard-description">
                                                            Configurar
                                                            <small>Funcionalidades do portal</small>
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="step-1">
                                                <div class="content-box">
                                                    <h3 class="content-box-header bg-default">
                                                        Identificação
                                                    </h3>
                                                    <div class="content-box-wrapper">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">ID</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="hidden" id="id_cliente_padrao" value="<?php echo $_SESSION['acesso']['id_cliente'];?>">
                                                                        <input type="text" class="form-control" style="width: 70px;" maxlength="6" readonly id="id">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Tipo</label>
                                                                    <div class="col-sm-6">
                                                                        <select class="form-control chosen-select" id="tipo_orgao" <?php echo (intval($_SESSION['acesso']['id_cliente']) !== 0?"disabled":"");?>>
                                                                            <option value="0" class="optionChild">Selecionar tipo do órgão</option>
                                                                            <option value="1" class="optionChild">PREFEITURA MUNICIPAL</option>
                                                                            <option value="2" class="optionChild">SECRETARIA DE EDUCAÇÃO</option>
                                                                            <option value="3" class="optionChild">CÂMARA DE VEREADORES</option>
                                                                            <option value="4" class="optionChild">AUTARQUIA MUNICIPAL</option>
                                                                            <option value="5" class="optionChild">FUNDO MUNICIPAL</option>
                                                                            <option value="6" class="optionChild">INSTITUTO DE PREV. MUNICIPAL</option>
                                                                            <?php echo $lista_clientes;?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Nome</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-maxlength="100" placeholder="Nome do Cliente" required class="form-control" id="nome">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">CNPJ</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-type="cnpj" placeholder="Número do Cnpj" class="form-control input-mask" data-inputmask="&apos;mask&apos;:&apos;99.999.999/9999-99&apos;" id="cnpj">
                                                                        <div class="help-block">99.999.999/9999-99</div>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Última atualização</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" class="form-control" maxlength="6" readonly id="atualizacao">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Telefone(s)</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-maxlength="100" maxlength="100" placeholder="Números de telefones" class="form-control" id="telefones">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">E-mail</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-type="email" data-parsley-maxlength="100" maxlength="100" placeholder="E-mail padrão do cliente" class="form-control" id="e_mail">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Situação</label>
                                                                    <div class="col-sm-6">
                                                                        <select class="form-control chosen-select" id="situacao">
                                                                            <option value="0" class="optionChild">Inativo</option>
                                                                            <option value="1" class="optionChild">Ativo</option>
                                                                            <option value="2" class="optionChild">Suspenso</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane" id="step-2">
                                                <div class="content-box">
                                                    <h3 class="content-box-header bg-default">
                                                        Endereço
                                                    </h3>
                                                    <div class="content-box-wrapper">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Endereço</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-maxlength="60" maxlength="60" placeholder="Logradouro" class="form-control" id="ender_lograd">
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <input type="text" data-parsley-maxlength="5" maxlength="5" placeholder="Número" class="form-control" style="width: 70px;" id="ender_num">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Bairro</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-maxlength="40" maxlength="40" placeholder="Nome do Bairro" class="form-control" id="ender_bairro">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Cep</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-type="ender_cep" placeholder="Número do Cep" required class="form-control input-mask" data-inputmask="&apos;mask&apos;:&apos;99.999-999&apos;" id="ender_cep">
                                                                        <div class="help-block">99.999-999</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">IBGE</label>
                                                                    <div class="col-sm-3">
                                                                        <input type="text" data-parsley-maxlength="7" maxlength="7" placeholder="Código IBGE" class="form-control" style="width: 100px;" id="municipio_cod_ibge">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Municípo</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-maxlength="40" maxlength="40" placeholder="Nome do município" class="form-control" id="municipio_nome">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">UF</label>
                                                                    <div class="col-sm-6">
                                                                        <select class="form-control chosen-select" id="municipio_uf">
                                                                            <option value="XX" class="optionChild">Selecionar UF</option>
                                                                            <?php echo $lista_ufs;?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane" id="step-3">
                                                <div class="content-box">
                                                    <h3 class="content-box-header bg-default">
                                                        Configurar
                                                    </h3>
                                                    <div class="content-box-wrapper">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Logotipo</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="hidden" id="logo" value="">
                                                                        <input type="hidden" id="brasao_nome" value="">
                                                                        <!--<input type="text" data-parsley-maxlength="100" maxlength="100" placeholder="URL da imagem de logotipo" required class="form-control" id="logo">-->
                                                                        <!--<input type="text" data-parsley-maxlength="60" maxlength="60" placeholder="Nome do Brasão" required class="form-control" id="brasao_nome">-->
                                                                        <div class="fileinput-preview thumbnail" data-trigger="fileinput"> <!--  style="width: 210px; height: 157px;" -->
                                                                            <img src="" title="Brasão" id="logo_img"/> <!--  style="width: 210px; height: 157px;" -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Upload de arquivo</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="file" class="form-control" id="logo_upload">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Domínio</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-maxlength="100" maxlength="100" placeholder="Título do Portal" class="form-control" id="dominio">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Título</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-maxlength="100" maxlength="100" placeholder="Título do Portal" class="form-control" id="titulo_portal">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-sm-3 control-label">Sub-título</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" data-parsley-maxlength="100" maxlength="100" placeholder="Sub-título do Portal" class="form-control" id="sub_titulo_portal">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <div class="checkbox checkbox-primary">
                                                                        <label>
                                                                            <input class="custom-checkbox" type="checkbox" name="exibe_lista" id="exibe_lista" value="1">
                                                                            Exibir listagem de servidores (Despesas com Folha / Por Servidores)
                                                                        </label>
                                                                    </div>
                                                                </div>    
                                                                <div class="form-group">
                                                                    <div class="checkbox checkbox-primary">
                                                                        <label>
                                                                            <input class="custom-checkbox" type="checkbox" name="enviar_senha_email" id="enviar_senha_email" value="1">
                                                                            Enviar para e-mail senha de acesso dos servidores
                                                                        </label>
                                                                    </div>
                                                                </div>    
                                                                <div class="form-group">
                                                                    <div class="checkbox checkbox-primary">
                                                                        <label>
                                                                            <input class="custom-checkbox" type="checkbox" name="contra_cheque" id="contra_cheque" value="S">
                                                                            Disponibilizar contra-cheques para servidores
                                                                        </label>
                                                                    </div>
                                                                </div>    
                                                                <div class="form-group">
                                                                    <div class="checkbox checkbox-primary">
                                                                        <label>
                                                                            <input class="custom-checkbox" type="checkbox" name="margem_consignavel" id="margem_consignavel" value="1">
                                                                            Exibir margem consignável no Contra-cheque do Servidor
                                                                        </label>
                                                                    </div>
                                                                </div>    
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-default">
                                            <button class="btn btn-primary" onclick="fechar_cadastro()" id="btn_form_fechar">Fechar</button>
                                            <button class="btn btn-primary pull-right" onclick="salvarCliente()" id="btn_form_salvar">Salvar</button>
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
                        $('#link_overlay').fadeOut();
                        
                        $('#box_confirme').fadeOut();
                        $('#box_alerta').fadeOut();
                        $('#box_erro').fadeOut();
                        
                        $(".input-mask").inputmask();
                        $('input[type="checkbox"].custom-checkbox').uniform();
                        
                        fechar_cadastro();
                        //formatar_checkbox();
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
                    </script>
                </div>
        
    </body>
        