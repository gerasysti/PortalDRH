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
    
    // Montar lista de Unidades/Órgãos
    $lista_clientes = "";
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 
          "Select "
        . "    u.* "
        . "  , coalesce(nullif(trim(c.titulo_portal), ''), c.nome, 'Administração do Sistema') as nome_cliente "
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
                            <h2><strong>Unidades de Lotação</strong></h2>
                            <p><strong>Lista de unidades de lotações cadastrados para o cliente</strong></p>
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
                                            <label for="id_cliente" class="col-sm-1 control-label padding-label">Cliente</label>
                                            <div class="col-sm-4 padding-field">
                                                <select class="form-control chosen-select" id="id_cliente" <?php echo (intval($_SESSION['acesso']['id_cliente']) !== 0?"disabled":"");?>>
                                                    <option value="0" class="optionChild">Selecione o cliente</option>
                                                    <?php echo $lista_clientes;?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-sm-4 padding-field">
                                                <div class="input-group">
                                                    <input type="text" class="form-control text lg-text" id="pesquisa" value=""/>
                                                    <div class="input-group-addon" style="padding: 0px; padding-left : 4px;">
                                                        <button id="btn_consultar" class="btn ra-round btn-primary lg-text" onclick="consultarUnidadeLotacao('<?php echo 'id_' . $_SESSION['acesso']['id'];?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')" title="Executar Consulta"><i class="glyph-icon icon-search"></i></button>
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
                                
                                <div class="box-wrapper" id="tabela-unidades_lotacao">
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
                                    <input type="hidden" id="op" value="inserir_ul">
                                    <input type="hidden" id="hs" value="<?php echo $_SESSION['acesso']['id'];?>">
                                </h2>
                                
                                <div class="box-wrapper">
                                    
                                    <div class="form-horizontal bordered-row">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="id_lotacao" class="col-sm-3 control-label padding-label">ID</label>
                                                    <div class="col-sm-2 padding-field">
                                                        <input type="text" class="form-control" style="width: 100px;" maxlength="4" id="id_lotacao" readonly>
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
                                                    <label for="tipo" class="col-sm-3 control-label padding-label">Tipo</label>
                                                    <div class="col-sm-9 padding-field">
                                                        <select class="form-control chosen-select" id="tipo" disabled>
                                                            <option value="0" class="optionChild">Selecione o tipo</option>
                                                            <option value="1" class="optionChild">SEDE DO ÓRGÃO</option>
                                                            <option value="2" class="optionChild">SECRETARIA MUNICIPAL</option>
                                                            <option value="3" class="optionChild">HOSPITAL / POSTO DE SAÚDE</option>
                                                            <option value="4" class="optionChild">ESCOLA MUNICIPAL</option>
                                                            <option value="99" class="optionChild">OUTROS</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <label for="descricao" class="col-sm-3 control-label padding-label">Descrição</label>
                                                    <div class="col-sm-9 padding-field">
                                                        <input type="text" data-parsley-maxlength="60" placeholder="Descrição..." required class="form-control" id="descricao" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group" style="margin: 2px;">
                                                    <div class="col-sm-3 padding-field">&nbsp;</div>
                                                    <div class="col-sm-6 padding-field">
                                                        <div class="checkbox checkbox-primary">
                                                            <label>
                                                                <input class="custom-checkbox" type="checkbox" name="ativa" id="ativa" value="S" disabled>
                                                                Ativa
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
                        
                        if ( $('#id_cliente').val() !== "0" ) {
                            $('#btn_consultar').trigger("click");
                        }
                    </script>
                </div>
        
    </body>
        