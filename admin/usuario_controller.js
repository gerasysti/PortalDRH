/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//function imprimirCargoSalario(hash, id) {
//    var unidade = id.split("_");
//    var params = {
//        'ac' : 'imprimir_cargo_salario',
//        'un' : unidade[1],
//        'nr_ano' : $('#nr_ano').val(),
//        'nr_mes' : $('#nr_mes').val()
//    };
//    
//    var str = 
//        "?un="  + hash + 
//        "&ano=" + params.nr_ano + 
//        "&mes=" + params.nr_mes;
//    
//    window.open("src/cargo_salario_print.php" + str, "_blank");
//}
//
//function exportarCargoSalario(hash, id, extensao) {
//    var unidade = id.split("_");
//    var params = {
//        'ac' : 'consultar_cargo_salario',
//        'pr' : extensao,
//        'un' : unidade[1],
//        'nr_ano' : $('#nr_ano').val(),
//        'nr_mes' : $('#nr_mes').val()
//    };
//    var nm_arquivo = "TCS_" + params.nr_ano + params.nr_mes + "_" + hash + ".txt";
//    window.open("src/baixar.php?arquivo=../downloads/" + nm_arquivo, "_blank");
//}

function configurarTabelaUsuario(){
    // Configurando Tabela
    var table = $('#datatable-responsive').DataTable({
        "paging": true,
        "pageLength": 5, // Apenas 10 registros por paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            null, // 0. ID
            null, // 1. Nome
            null, // 2. E-mail
            null, // 3. Cliente
            null, // 4. CNPJ
            { "width": "175px" }, // 5. Último acesso
            null, // 6. Senha
            { "width": "120px" }  // 7. Controles
        ],
        "columnDefs": [
            {"orderable": false, "targets": 5}, 
            {"orderable": false, "targets": 6}, 
            {"orderable": false, "targets": 7}  
        ],
        "order": [[1, 'asc']], // "order": [] <-- Ordenação indefinida
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
    
//    var tt = new $.fn.dataTable.TableTools(table);
//    
//    $( tt.fnContainer() ).insertBefore('#datatable-responsive_wrapper div.dataTables_filter');
//    $('.DTTT_container').addClass('btn-group');
//    $('.DTTT_container a').addClass('btn btn-default btn-md');
    
    $('.dataTables_filter input').attr("placeholder", "Localizar...");
    $('.dataTables_filter input').focus();
}

function configurarTabUnidadeGestoraPermissao(){
    // Configurando Tabela
    var table = $('#datatable-responsive-ugt').DataTable({
        "paging": true,
        "pageLength": 10, // Registros por paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            { "width": "5px" }, // 0. ID
            null, // 1. Nome
            { "width": "5px" }  // 2. Acesso
        ],
        "columnDefs": [
            {"orderable": false, "targets": 0}, // ID
            {"orderable": false, "targets": 2}  // Acesso
        ],
        "order": [[1, 'asc']], 
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
}

function configurarTabUnidadeOrcamentPermissao(){
    // Configurando Tabela
    var table = $('#datatable-responsive-uoc').DataTable({
        "paging": true,
        "pageLength": 10, // Registros por paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            { "width": "5px" }, // 0. ID
            null, // 1. Nome
            { "width": "5px" }  // 2. Acesso
        ],
        "columnDefs": [
            {"orderable": false, "targets": 0}, // ID
            {"orderable": false, "targets": 2}  // Acesso
        ],
        "order": [[1, 'asc']], 
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
}

function configurarTabUnidadeLotacaoPermissao(){
    // Configurando Tabela
    var table = $('#datatable-responsive-ulo').DataTable({
        "paging": true,
        "pageLength": 10, // Registros por paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            { "width": "5px" }, // 0. ID
            null, // 1. Nome
            { "width": "5px" }  // 2. Acesso
        ],
        "columnDefs": [
            {"orderable": false, "targets": 0}, // ID
            {"orderable": false, "targets": 2}  // Acesso
        ],
        "order": [[1, 'asc']], 
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
}

function consultarUsuario(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'consultar_usuario',
        'id' : hash[1],
        'us' : email[1],
        'to' : $('#tipo').val(),
        'ps' : $('#pesquisa').val()
    };

    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#link_overlay').trigger("click");
            $('#btn_consultar').attr('disabled', true);
            if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeIn('fast');
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#tabela-usuarios').html(data);
            $('#btn_consultar').attr('disabled', false);
            if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
            configurarTabelaUsuario();
        },
        error: function (request, status, error) {
            $('#tabela-usuarios').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
            $('#btn_consultar').attr('disabled', false);
            if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
        }
    });  
    // Finalizamos o Ajax
}

function editarUsuario(id) {
    var id_usuario = id.replace("editar_usuario_", "");
    var id_cliente = $('#id_cliente_' + id_usuario).val();
    
    var i_linha = document.getElementById("linha_" + id_usuario);
    var colunas = i_linha.getElementsByTagName('td');
    
    $('#op').val("editar_usuario");
    $('#id').val( id_usuario );
    $('#id_cliente').val( $('#id_cliente_' + id_usuario).val() );
    $('#nome').val( colunas[1].firstChild.nodeValue );
    $('#email').val( colunas[2].firstChild.nodeValue );
    $('#ultimo_acesso').val( $('#ultimo_acesso_' + id_usuario).val() );
    $('#situacao').val( $('#situacao_' + id_usuario).val() );
    
    $('#administrar_portal').prop('checked', ($('#administrar_portal_' + id_usuario).val() === '1')).uniform();
    $('#lancar_eventos').prop('checked', ($('#lancar_eventos_' + id_usuario).val() === '1')).uniform();
    $('#finalizar_eventos').prop('checked', ($('#finalizar_eventos_' + id_usuario).val() === '1')).uniform();
    $('#lancar_ch_professores').prop('checked', ($('#lancar_ch_professores_' + id_usuario).val() === '1')).uniform();
    $('#finalizar_ch_professores').prop('checked', ($('#finalizar_ch_professores_' + id_usuario).val() === '1')).uniform();
    
    $('#senha').val("");
    $('#senha_confirme').val("");
    
    $('#id_cliente').trigger('chosen:updated');
    $('#situacao').trigger('chosen:updated');
    $('input[type="checkbox"].custom-checkbox').uniform();
    
    carregar_unidade_gestora_allows(id_cliente, id_usuario, function(retorno){
        $('#box-unidade_gestora').html(retorno);
        configurarTabUnidadeGestoraPermissao();
        $('input[type="checkbox"].custom-checkbox').uniform();
    });
    
    carregar_unidade_orcament_allows(id_cliente, id_usuario, function(retorno){
        $('#box-unidade_orcament').html(retorno);
        configurarTabUnidadeOrcamentPermissao();
        $('input[type="checkbox"].custom-checkbox').uniform();
    });
    
    carregar_unidade_lotacao_allows(id_cliente, id_usuario, function(retorno){
        $('#box-unidade_lotacao').html(retorno);
        configurarTabUnidadeLotacaoPermissao();
        $('input[type="checkbox"].custom-checkbox').uniform();
    });
    
    //document.getElementById("panel_titulo").style.display    = 'none';
    document.getElementById("panel_pesquisa").style.display  = 'none';
    document.getElementById("panel_resultado").style.display = 'none';
    $('#panel_cadastro').fadeIn( 400, "linear" );
    $('#panel_permissoes').fadeIn( 400, "linear" );
    
//    
//    $('input[type="checkbox"].custom-checkbox').uniform();
//    $('input[type="radio"].custom-radio').uniform();
//    $('.custom-select').uniform();
//
//    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');
//
//    $('.checker span').append('<i class="glyph-icon icon-check"></i>');
//    $('.radio span').append('<i class="glyph-icon icon-circle"></i>');
}

function inserirUsuario(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'inserir_usuario',
        'id' : hash[1]
    };
    
    $('#op').val("inserir_usuario");
    $('#id').val( "000" );
    $('#id_cliente').val( $('#id_cliente_padrao').val() );
    $('#nome').val("");
    $('#email').val("");
    $('#senha').val("");
    $('#senha_confirme').val("");
    $('#ultimo_acesso').val("");
    $('#situacao').val("1");

    $('#administrar_portal').prop('checked', false);
    $('#lancar_eventos').prop('checked', false);
    $('#finalizar_eventos').prop('checked', false);
    $('#lancar_ch_professores').prop('checked', false);
    $('#finalizar_ch_professores').prop('checked', false);
    
    $('#id_cliente').trigger('chosen:updated');
    $('#situacao').trigger('chosen:updated');
            
    $('input[type="checkbox"].custom-checkbox').uniform();
    
    //document.getElementById("panel_titulo").style.display    = 'none';
    document.getElementById("panel_pesquisa").style.display  = 'none';
    document.getElementById("panel_resultado").style.display = 'none';
    $('#panel_cadastro').fadeIn( 400, "linear" );
    $('#panel_permissoes').fadeIn( 400, "linear" );
}

function excluirUsuario(id) {
    var id_usuario = id.replace("excluir_usuario_", "");
    var i_linha = document.getElementById("linha_" + id_usuario);
    mensagem_confirmar("Confirma a exclusão do usuário <strong>'" + $('#nome_' + id_usuario).val() + " (" + $('#email_' + id_usuario).val() + ")'</strong>?", function(){
        var link = document.getElementById("btnC_confirma_msg");
        link.onclick = function() {
            var params = {
                'ac' : 'excluir_usuario',
                'hs' : $('#hs').val(),
                'id' : $('#id_' + id_usuario).val(),
                'id_cliente' : $('#id_cliente_' + id_usuario).val()
            };
            
            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : './usuario_dao.php',
                // Definimos o tipo de requisição
                type: 'post',
                // Definimos o tipo de retorno
                dataType : 'html',
                // Dolocamos os valores a serem enviados
                data: params,
                // Antes de enviar ele alerta para esperar
                beforeSend : function(){
                    ;
                },
                // Colocamos o retorno na tela
                success : function(data){
                    var retorno = data;
                    if (retorno === "OK") {
                        $('#btnF_confirma_msg').trigger("click");
                        RemoveTableRow(i_linha);
                    } else {
                        $('#btnF_confirma_msg').trigger("click");
                        mensagem_erro( "<p><strong>Erro ao tentar excluir o usuário selecionado:</strong> <br><br>" + retorno + "</p>" );
                    }
                },
                error: function (request, status, error) {
                    $('#btnF_confirma_msg').trigger("click");
                    mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
                }
            });  
            // Finalizamos o Ajax
        }
    });
}

function salvarUsuario() {
    var retorno = false;
    try {
        var params = {
            'ac' : 'gravar_usuario',
            'op' : $('#op').val(),
            'hs' : $('#hs').val(),
            'id' : $('#id').val(),
            'id_cliente' : $('#id_cliente').val(),
            'nome'  : $('#nome').val().trim(),
            'email' : $('#email').val().trim(),
            'senha' : $('#senha').val(),
            'senha_confirme' : $('#senha_confirme').val(),
            'administrar_portal' : '0',
            'lancar_eventos'     : '0',
            'finalizar_eventos'  : '0',
            'lancar_ch_professores'    : '0',
            'finalizar_ch_professores' : '0',
            'situacao' : $('#situacao').val()
        };
        // alert( JSON.stringify(params) );
        // $('#box_confirme').trigger("click");
        // mensagem_confirmar( JSON.stringify(params) );
        
        if ( $('#administrar_portal').is(":checked") ) params.administrar_portal = $('#administrar_portal').val();
        if ( $('#lancar_eventos').is(":checked") ) params.lancar_eventos = $('#lancar_eventos').val();
        if ( $('#finalizar_eventos').is(":checked") ) params.finalizar_eventos = $('#finalizar_eventos').val();
        if ( $('#lancar_ch_professores').is(":checked") ) params.lancar_ch_professores = $('#lancar_ch_professores').val();
        if ( $('#finalizar_ch_professores').is(":checked") ) params.finalizar_ch_professores = $('#finalizar_ch_professores').val();
        
        var msg = "";
        var mrc = "<i class='glyph-icon icon-edit'></i>&nbsp;";
        
        if (params.nome  === "") msg += mrc + "Nome<br>";
        if (params.email === "") msg += mrc + "E-mail<br>";
        if ((params.op === "inserir_usuario") && (params.senha === "")) msg += mrc + "Senha<br>";
        if ((params.op === "inserir_usuario") && (params.senha_confirme === "")) msg += mrc + "Confirmar Senha<br>";
        
        if (msg.trim() !== "") {
            mensagem_alerta( "<p><strong>Os campos listados têm seu preenchimento obrigatório:</strong> <br><br>" + msg + "</p>" );
        } else {
            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : './usuario_dao.php',
                // Definimos o tipo de requisição
                type: 'post',
                // Definimos o tipo de retorno
                dataType : 'html',
                // Dolocamos os valores a serem enviados
                data: params,
                // Antes de enviar ele alerta para esperar
                beforeSend : function(){
                    $('#link_overlay').trigger("click");
                    $('#btn_form_fechar').attr('disabled', true);
                    $('#btn_form_salvar').attr('disabled', true);
                    if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeIn('fast');
                },
                // Colocamos o retorno na tela
                success : function(data){
                    var file_json = "../downloads/user_" + params.hs + ".json"; 
                    var retorno   = data;
                    if (retorno === "OK") {
                        if (params.op === "inserir_usuario") {
                            $.getJSON(file_json, function(data){
                                this.qtd = data.form.length;
                                $('#id').val(data.form[0].id);
                                AddTableRowUsuario();
                            });
                        } else 
                        if (params.op === "editar_usuario") {
                            var i_linha = document.getElementById("linha_" + params.id);
                            var colunas = i_linha.getElementsByTagName('td');

                            colunas[1].firstChild.nodeValue = params.nome;
                            colunas[2].firstChild.nodeValue = params.email;
                            colunas[3].firstChild.nodeValue = $('#id_cliente option:selected').text();
                            colunas[4].firstChild.nodeValue = "...";

                            $('#id_' + params.id).val( params.id );
                            $('#id_cliente_' + params.id).val( params.id_cliente );
                            $('#nome_' + params.id).val( params.nome );
                            $('#email_' + params.id).val( params.email );
                            $('#situacao_' + params.id).val( params.situacao );
                            $('#administrar_portal_' + params.id).val( params.administrar_portal );
                            $('#lancar_eventos_' + params.id).val( params.lancar_eventos );
                            $('#finalizar_eventos_' + params.id).val( params.finalizar_eventos );
                            $('#lancar_ch_professores_' + params.id).val( params.lancar_ch_professores );
                            $('#finalizar_ch_professores_' + params.id).val( params.finalizar_ch_professores );
                        }
                        
                        $('#btn_form_fechar').attr('disabled', false);
                        $('#btn_form_salvar').attr('disabled', false);
                        if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
                        /*
                        document.getElementById("panel_permissoes").style.display  = 'none';
                        fechar_cadastro();
                        */
                    } else {
                        $('#btn_form_fechar').attr('disabled', false);
                        $('#btn_form_salvar').attr('disabled', false);
                        if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
                        mensagem_erro( "<p><strong>Erro ao tentar gravar as alterações na base:</strong> <br><br>" + retorno + "</p>" );
                    }
                },
                error: function (request, status, error) {
                    $('#btn_form_fechar').attr('disabled', false);
                    $('#btn_form_salvar').attr('disabled', false);
                    if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
                    mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
                }
            });  
            // Finalizamos o Ajax
        }
    } 
    catch (e) {
        alert(e);
    }
    finally {
        return retorno;
    }
}

function carregar_unidade_gestora_allows(cliente, usuario, callback) {
    var params = {
        'ac' : 'carregar_unidade_gestora_permissao',
        'hs' : $('#hs').val(),
        'id_cliente' : cliente,
        'id_usuario' : usuario
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            ;
        },
        // Colocamos o retorno na tela
        success : function(data){
            if (callback && typeof(callback) === "function") {
                callback(data);
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function carregar_unidade_orcament_allows(cliente, usuario, callback) {
    var params = {
        'ac' : 'carregar_unidade_orcament_permissao',
        'hs' : $('#hs').val(),
        'id_cliente' : cliente,
        'id_usuario' : usuario
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            ;
        },
        // Colocamos o retorno na tela
        success : function(data){
            if (callback && typeof(callback) === "function") {
                callback(data);
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function carregar_unidade_lotacao_allows(cliente, usuario, callback) {
    var params = {
        'ac' : 'carregar_unidade_lotacao_permissao',
        'hs' : $('#hs').val(),
        'id_cliente' : cliente,
        'id_usuario' : usuario
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            ;
        },
        // Colocamos o retorno na tela
        success : function(data){
            if (callback && typeof(callback) === "function") {
                callback(data);
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function marcar_unidade_gestora_allows(cliente, usuario, acesso, callback) {
    var params = {
        'ac' : 'marcar_permissao_ugt',
        'hs' : $('#hs').val(),
        'id_cliente' : cliente,
        'id_usuario' : usuario,
        'acesso' : acesso,
        'lancar' : '0'
    };
    
    if ( $('#lancar_eventos').is(":checked") ) params.lancar = $('#lancar_eventos').val();
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            ;
        },
        // Colocamos o retorno na tela
        success : function(data){
            var retorno = data;
            if (retorno !== "OK") {
                mensagem_erro(retorno);
            } else {
                if (callback && typeof(callback) === "function") {
                    callback();
                }
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function marcar_unidade_orcament_allows(cliente, usuario, acesso, callback) {
    var params = {
        'ac' : 'marcar_permissao_uoc',
        'hs' : $('#hs').val(),
        'id_cliente' : cliente,
        'id_usuario' : usuario,
        'acesso' : acesso,
        'lancar' : '0'
    };
    
    if ( $('#lancar_eventos').is(":checked") ) params.lancar = $('#lancar_eventos').val();
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            ;
        },
        // Colocamos o retorno na tela
        success : function(data){
            var retorno = data;
            if (retorno !== "OK") {
                mensagem_erro(retorno);
            } else {
                if (callback && typeof(callback) === "function") {
                    callback();
                }
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function marcar_unidade_lotacao_allows(cliente, usuario, acesso, callback) {
    var params = {
        'ac' : 'marcar_permissao_ulo',
        'hs' : $('#hs').val(),
        'id_cliente' : cliente,
        'id_usuario' : usuario,
        'acesso' : acesso,
        'lancar' : '0'
    };
    
    if ( $('#lancar_eventos').is(":checked") ) params.lancar = $('#lancar_eventos').val();
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            ;
        },
        // Colocamos o retorno na tela
        success : function(data){
            var retorno = data;
            if (retorno !== "OK") {
                mensagem_erro(retorno);
            } else {
                if (callback && typeof(callback) === "function") {
                    callback();
                }
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function gravar_permissao_ugt(cliente, unidade, usuario, permissao) {
    var params = {
        'ac' : 'gravar_permissao_ugt',
        'hs' : $('#hs').val(),
        'id_cliente' : cliente,
        'id_unidade' : unidade,
        'id_usuario' : usuario,
        'acesso'     : permissao,
        'lancar'     : '0',
        'lancar_chp' : '0'
    };
    
    if ( $('#lancar_eventos').is(":checked") ) params.lancar = $('#lancar_eventos').val();
    if ( $('#lancar_ch_professores').is(":checked") ) params.lancar_chp = $('#lancar_ch_professores').val();
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            ;
        },
        // Colocamos o retorno na tela
        success : function(data){
            var retorno = data;
            if (retorno !== "OK") {
                mensagem_erro(retorno);
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function gravar_permissao_uoc(cliente, unidade, usuario, permissao) {
    var params = {
        'ac' : 'gravar_permissao_uoc',
        'hs' : $('#hs').val(),
        'id_cliente' : cliente,
        'id_unidade' : unidade,
        'id_usuario' : usuario,
        'acesso'     : permissao,
        'lancar'     : '0'
    };
    
    if ( $('#lancar_eventos').is(":checked") ) params.lancar = $('#lancar_eventos').val();
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            ;
        },
        // Colocamos o retorno na tela
        success : function(data){
            var retorno = data;
            if (retorno !== "OK") {
                mensagem_erro(retorno);
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function gravar_permissao_ulo(cliente, unidade, usuario, permissao) {
    var params = {
        'ac' : 'gravar_permissao_ulo',
        'hs' : $('#hs').val(),
        'id_cliente' : cliente,
        'id_unidade' : unidade,
        'id_usuario' : usuario,
        'acesso'     : permissao,
        'lancar'     : '0',
        'lancar_chp' : '0'
    };
    
    if ( $('#lancar_eventos').is(":checked") ) params.lancar = $('#lancar_eventos').val();
    if ( $('#lancar_ch_professores').is(":checked") ) params.lancar_chp = $('#lancar_ch_professores').val();
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            ;
        },
        // Colocamos o retorno na tela
        success : function(data){
            var retorno = data;
            if (retorno !== "OK") {
                mensagem_erro(retorno);
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

(function($) {
    AddTableRowUsuario = function() {

    var referencia = $('#id').val();
    var tabela = "";
    var input  = "";
    var ativo  = "";
    
    var administrar_portal = "0";
    var lancar_eventos     = "0";
    var finalizar_eventos  = "0";
    var lancar_chprofessores    = "0";
    var finalizar_chprofessores = "0";

    if ( $('#administrar_portal').is(":checked") ) administrar_portal = $('#administrar_portal').val();
    if ( $('#lancar_eventos').is(":checked") ) lancar_eventos = $('#lancar_eventos').val();
    if ( $('#finalizar_eventos').is(":checked") ) finalizar_eventos = $('#finalizar_eventos').val();
    if ( $('#lancar_ch_professores').is(":checked") ) lancar_chprofessores = $('#lancar_ch_professores').val();
    if ( $('#finalizar_ch_professores').is(":checked") ) finalizar_chprofessores = $('#finalizar_ch_professores').val();

    // Verifica se exite tabela para exibição nos dados na página atual,
    // e caso não exista, construí-la.
    var pagina = document.getElementById("tabela-usuarios"); 
    
    if ( pagina.innerHTML.indexOf("datatable-responsive") === -1 ) {
        tabela += "<a id='ancora_datatable-responsive'></a>";
        tabela += "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
        tabela += "    <thead>";
        tabela += "        <tr class='custom-font-size-12'>";
        tabela += "            <th>ID</th>";
        tabela += "            <th>Nome</th>";
        tabela += "            <th>E-mail</th>";
        tabela += "            <th>Cliente</th>";
        tabela += "            <th>CNPJ</th>";
        //$tabela += "            <th class='numeric' data-orderable='false' style='text-align: center;'>Último acesso</th>";
        tabela += "            <th data-orderable='false' style='text-align: left;'>Último acesso</th>";
        tabela += "            <th data-orderable='false' style='text-align: center;'></th>";
        tabela += "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
        tabela += "        </tr>";
        tabela += "    </thead>";
        tabela += "    <tbody>";
        tabela += "    </tbody>";
        tabela += "</table>";
        
        $('#tabela-usuarios').html(tabela);
    }
    
    input = 
          "<input type='hidden' id='id_"            + referencia + "' value='" + $('#id').val() + "'>"
        + "<input type='hidden' id='id_cliente_"    + referencia + "' value='" + $('#id_cliente').val() + "'>"
        + "<input type='hidden' id='nome_"          + referencia + "' value='" + $('#nome').val() + "'>"
        + "<input type='hidden' id='email_"         + referencia + "' value='" + $('#email').val() + "'>"
        + "<input type='hidden' id='ultimo_acesso_" + referencia + "' value='&nbsp;'>"
        + "<input type='hidden' id='administrar_portal_" + referencia + "' value='" + administrar_portal + "'>"
        + "<input type='hidden' id='lancar_eventos_"     + referencia + "' value='" + lancar_eventos + "'>"
        + "<input type='hidden' id='finalizar_eventos_"  + referencia + "' value='" + finalizar_eventos + "'>"
        + "<input type='hidden' id='lancar_ch_professores_"     + referencia + "' value='" + lancar_chprofessores + "'>"
        + "<input type='hidden' id='finalizar_ch_professores_"  + referencia + "' value='" + finalizar_chprofessores + "'>"
        + "<input type='hidden' id='situacao_"      + referencia + "' value='" + $('#situacao').val() + "'>";
    
    var icon_ed = "<button id='editar_usuario_"  + referencia + "' class='btn btn-round btn-primary' title='Editar Registro'  onclick='editarUsuario(this.id)'><i class='glyph-icon icon-edit'></i></button>";
    var icon_ex = "<button id='excluir_usuario_" + referencia + "' class='btn btn-round btn-primary' title='Excluir Registro' onclick='excluirUsuario(this.id)'><i class='glyph-icon icon-trash'></i></button>";
    var icon_pw = "&nbsp;";

    if ($('#email').val() === "") {
        icon_pw = "<i class='glyph-icon icon-key'></i>";
    } else {
        icon_pw = "<i class='glyph-icon icon-check-square-o'></i>";
    }
    
    var newRow = $("<tr class='custom-font-size-10' id='linha_" + referencia + "'>");
    var cols = "";

    cols += "<td>" + $('#id').val() + "</td>";
    cols += "<td>" + $('#nome').val() + "</td>";
    cols += "<td>" + $('#email').val() + "</td>";
    cols += "<td>" + $('#id_cliente option:selected').text() + "</td>";
    cols += "<td>...</td>";
    cols += "<td style='text-align: left;'>&nbsp;</td>";
    cols += "<td style='text-align: center;'>" + icon_pw + "</td>";
    cols += "<td style='text-align: center;'>" + icon_ed + "&nbsp;" + icon_ex + " " + input + "</td>";

    newRow.append(cols);
    
    $("#datatable-responsive").append(newRow);
    if ( tabela !== "" ) configurarTabelaUsuario();

    return false;
  };
})(jQuery);