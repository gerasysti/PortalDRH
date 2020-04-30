/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function listar_unidades_orcamentarias(id_sessao, lg_sessao, ug, callback) {
    var sessao = id_sessao.split("_");
    var login  = lg_sessao.split("_");
    
    var params = {
        'ac' : 'listar_unidades_orcamentarias',
        'hs' : $('#hs').val(),
        'id' : sessao[1],
        'us' : login[1],
        'ug' : ug
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './unidade_orcament_dao.php',
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
                var ls = "";
                var hs = $('#hs').val();
                var file_json = "../downloads/uoc_" + hs + ".json"; 
                $.getJSON(file_json, function(data){
                    this.qtd = data.lista.length;
                    for (var i = 0; i < this.qtd; i++) {
                        ls += "    <option value='" + data.lista[i].id + "' class='optionChild'>" + data.lista[i].descricao + "</option>";
                    }
                    
                    if (callback && typeof(callback) === "function") {
                        callback(ls);
                    }
                });
            }
        },
        error: function (request, status, error) {
            $('#btnF_confirma_msg').trigger("click");
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

/*
function salvarEvento() {
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
            'situacao' : $('#situacao').val()
        };
        // alert( JSON.stringify(params) );
        // $('#box_confirme').trigger("click");
        // mensagem_confirmar( JSON.stringify(params) );
        
        if ( $('#administrar_portal').is(":checked") ) params.administrar_portal = $('#administrar_portal').val();
        if ( $('#lancar_eventos').is(":checked") ) params.lancar_eventos = $('#lancar_eventos').val();
        
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
                        }
                        
                        $('#btn_form_fechar').attr('disabled', false);
                        $('#btn_form_salvar').attr('disabled', false);
                        if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
                        
                        fechar_cadastro();
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
*/