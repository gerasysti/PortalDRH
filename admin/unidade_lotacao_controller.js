/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function configurarTabelaUnidadeLotacao(){
    // Configurando Tabela
    var table = $('#datatable-responsive').DataTable({
        "paging": true,
        "pageLength": 25, // Quantidade de registros na paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            null,                  // 0. ID
            null,                  // 1. Descrição
            null,                  // 2. Tipo
            null,                  // 3. Ativo
            { "width": "10px" }    // 4. Controles
        ],
        "columnDefs": [
            {"orderable": false, "targets": 0}, // ID
            {"orderable": false, "targets": 3}, // Ativa
            {"orderable": false, "targets": 4}  // Controles
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
}

function consultarUnidadeLotacao(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'consultar_unidade',
        'id' : hash[1],
        'us' : email[1],
        'to' : $('#id_cliente').val(),
        'ps' : $('#pesquisa').val()
    };
    
    if (parseInt(params.to) === 0) {
        mensagem_alerta("Selecione o cliente para que sua Tabela de Unidades de Lotação seja carregada");
    } else {
        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : './unidade_lotacao_dao.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                $('#link_overlay').trigger("click");
                $('#page-wait').html( loading_spinner() );
                $('#tabela-eventos').html("");

                $('#btn_consultar').attr('disabled', true);
            },
            // Colocamos o retorno na tela
            success : function(data){
                $('#page-wait').html("");
                $('#tabela-unidades_lotacao').html(data);

                $('#btn_consultar').attr('disabled', false);
                configurarTabelaUnidadeLotacao();
            },
            error: function (request, status, error) {
                $('#page-wait').html("");
                $('#tabela-unidades_lotacao').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());

                $('#btn_consultar').attr('disabled', false);
            }
        });  
        // Finalizamos o Ajax
    }
}

function editarUnidadeLotacao(id) {
    var id_unidade = id.replace("editar_unidade_", "");
    
    $('#op').val("editar_unidade");
    
    $('#id_lotacao').val( $('#id_lotacao_' + id_unidade).val() );
    $('#id_cliente_cadastro').val( $('#id_cliente_' + id_unidade).val() );
    $('#tipo').val( $('#tipo_' + id_unidade).val() );
    $('#descricao').val( $('#descricao_' + id_unidade).val() );
    $('#ativa').prop('checked', ($('#ativa_' + id_unidade).val() === 'S')); 

    $('#tipo').trigger('chosen:updated');
    $('#id_cliente_cadastro').trigger('chosen:updated');
    $('input[type="checkbox"].custom-checkbox').uniform();
    
    //document.getElementById("panel_titulo").style.display    = 'none';
    document.getElementById("panel_pesquisa").style.display  = 'none';
    document.getElementById("panel_resultado").style.display = 'none';
    $('#panel_cadastro').fadeIn( 400, "linear" );
}

function excluirUnidadeLotacao(id) {
    var id_unidade = id.replace("excluir_unidade_", "");
    var i_linha = document.getElementById("linha_" + id_unidade);
    mensagem_confirmar("Confirma a exclusão da Unidade de Lotação <strong>'" + $('#descricao_' + id_unidade).val() + "'</strong>?", function(){
        var link = document.getElementById("btnC_confirma_msg");
        link.onclick = function() {
            var params = {
                'ac' : 'excluir_unidade',
                'hs' : $('#hs').val(),
                'to' : $('#id_cliente_' + id_unidade).val(),
                'id' : $('#id_unidade_' + id_unidade).val()
            };
            
            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : './unidade_lotacao_dao.php',
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
                        mensagem_erro( "<p><strong>Erro ao tentar excluir a Unidade de Lotação selecionada:</strong> <br><br>" + retorno + "</p>" );
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

/*
function inserirEvento(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'inserir_evento',
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
    
    $('#id_cliente').trigger('chosen:updated');
    $('#situacao').trigger('chosen:updated');
            
    $('input[type="checkbox"].custom-checkbox').uniform();
    
    //document.getElementById("panel_titulo").style.display    = 'none';
    document.getElementById("panel_pesquisa").style.display  = 'none';
    document.getElementById("panel_resultado").style.display = 'none';
    $('#panel_cadastro').fadeIn( 400, "linear" );
}

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

(function($) {
    AddTableRowEvento = function() {

    var referencia = $('#id').val();
    var tabela = "";
    var input  = "";
    var ativo  = "";
    
    var administrar_portal = "0";
    var lancar_eventos     = "0";

    if ( $('#administrar_portal').is(":checked") ) administrar_portal = $('#administrar_portal').val();
    if ( $('#lancar_eventos').is(":checked") ) lancar_eventos = $('#lancar_eventos').val();

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
*/