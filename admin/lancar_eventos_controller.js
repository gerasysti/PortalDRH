/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function zero_esquerda(str, qtde) {
    var foo = "";
    var qte = qtde * (-1);
    while (foo.length < qtde) {
        foo = "0" + foo;
    }
    
    return (foo + str).slice(qte);
} 

function configurarTabelaEventosLancados(){
    // Configurando Tabela
    // https://datatables.net/manual/styling/classes#nowrap
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
            { "width": "10px" },   // 0. #
            null,                  // 1. Unidade Gestora
            null,                  // 2. Unidade de Lotação
            { "width": "10px" },   // 3. Rubrica
            null,                  // 4. Evento
            { "width": "10px" },   // 5. Tipo
            { "width": "10px" },   // 6. Serviores
            { "width": "10px" },   // 7. Valores
            { "width": "10px" },   // 8. Importado
            { "width": "120px" }    // 9. Controles
        ],
        "columnDefs": [
            {"orderable": false, "targets": 0}, // #
            {"orderable": false, "targets": 3}, // Rubrica
            {"orderable": true,  "targets": 4, "className": 'nowrap'}, // Evento
            {"orderable": false, "targets": 5}, // Tipo
            {"orderable": false, "targets": 6}, // Servidores
            {"orderable": false, "targets": 7}, // Total
            {"orderable": false, "targets": 8}, // Importado
            {"orderable": false, "targets": 9}  // Controles
        ],
        "order": [], // <-- Ordenação 
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

function configurarTabelaServidoresLancados(){
    // Configurando Tabela
    // https://datatables.net/manual/styling/classes#nowrap
    var table = $('#datatable-responsive_serv').DataTable({
        "paging": true,
        "pageLength": 25, // Quantidade de registros na paginação
        "lengthChange": false,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            { "width": "10px" },   // 0. #
            null,                  // 1. ID
            null,                  // 2. Nome
            null,                  // 3. Cargo*Função
            { "width": "140px" },  // 4. Quantidade
            { "width": "140px" },  // 5. Valor
            { "width": "10px" }    // 6. Controles
        ],
        "columnDefs": [
            {"orderable": false, "targets": 0}, // #
            {"orderable": false, "targets": 1}, // ID
            {"orderable": false, "targets": 2, "className": 'nowrap'}, // Nome
            {"orderable": false, "targets": 3, "className": 'nowrap'}, // Cargo/Função
            {"orderable": false, "targets": 4}, // Quantidade
            {"orderable": false, "targets": 5}, // Valor
            {"orderable": false, "targets": 6}  // Controles
        ],
        "order": [], // <-- Ordenação 
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
                "infoEmpty": "", //"Sem dados para exibição",
                "infoFiltered":   "(Filtrado a partir de _MAX_ registros)",
                "zeroRecords": "Sem registro(s) para exibição",
                "lengthMenu": "Exibindo _MENU_ registro(s)",
                "loadingRecords": "Por favor, aguarde - carregando...",
                "processing": "Processando...",
                "search": "Localizar:"
        }
    });
//    
//    $('.dataTables_filter input').attr("placeholder", "Localizar...");
}

function consultarEventosLancados(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'consultar_eventos_lancados',
        'id' : hash[1],
        'us' : email[1],
        'to' : $('#cliente').val(),
        'ug' : $('#id_unidade').val(),
        'lo' : $('#id_lotacao').val(),
        'cp' : $('#ano_mes_pesquisa').val()
    };
    
    if (parseInt(params.to) === 0) {
        mensagem_alerta("Usuário <strong>não está associado ao cadastro de um cliente</strong>!<br>Favor, entre em contato com o suporte da plataforma.");
    } else {
        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : './lancar_eventos_dao.php',
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
                $('#tabela-servidores').html("");

                $('#btn_consultar').attr('disabled', true);
            },
            // Colocamos o retorno na tela
            success : function(data){
                $('#page-wait').html("");
                $('#tabela-lancamentos').html(data);

                $('#btn_consultar').attr('disabled', false);
                configurarTabelaEventosLancados();
            },
            error: function (request, status, error) {
                $('#page-wait').html("");
                $('#tabela-lancamentos').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());

                $('#btn_consultar').attr('disabled', false);
            }
        });  
        // Finalizamos o Ajax
    }
}

function carregarServidoresLancamento() {
    var id   = $('#id_sessao').val();
    var hash = id.split("_");
    var params = {
        'ac' : 'carregar_servidores_lancamento',
        'id' : hash[1],
        'us' : $('#us').val(),
        'to' : $('#cliente').val(),
        'id_cliente': $('#id_cliente').val(),
        'controle'  : $('#controle').val()
    };

    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './lancar_eventos_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#tabela-lancamento_servidores').html("<p style='margin: 10px; padding: 10px;'>Carregando servidores/lançamentos, <strong>aguarde</strong>!</p>");
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#tabela-lancamento_servidores').html(data);
            $('.excluir_servidor').blur();
            configurarTabelaServidoresLancados();
        },
        error: function (request, status, error) {
            $('#tabela-lancamento_servidores').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function salvarServidoresLancamento(ids, qts, vls, callback) {
    var id   = $('#id_sessao').val();
    var hash = id.split("_");
    var params = {
        'ac' : 'gravar_lancamentos_servidores',
        'id' : hash[1],
        'hs' : $('#hs').val(),
        'to' : $('#cliente').val(),
        'controle'   : parseFloat("0" + $('#controle').val()),
        'id_cliente' : $('#id_cliente').val(),
        'ano_mes'    : $('#ano_mes').val(),
        'id_unid_gestora' : $('#id_unid_gestora').val(),
        'id_unid_lotacao' : $('#id_unid_lotacao').val(),
        'id_evento'       : $('#id_evento').val(),
        'servidores'  : '',
        'quantidades' : '',
        'valores'     : ''
    };

    if (ids !== null) params.servidores  = ids;
    if (qts !== null) params.quantidades = qts;
    if (vls !== null) params.valores     = vls;
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './lancar_eventos_dao.php',
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
                if(callback && typeof(callback) === "function") {
                    callback();
                }
            } else {
                mensagem_erro(retorno);
            }
        },
        error: function (request, status, error) {
            mensagem_erro("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function salvarServidorLancamento(sequencia, id_servidor, quant, valor, obs, callback) {
    var id   = $('#id_sessao').val();
    var hash = id.split("_");
    var params = {
        'ac' : 'grava_lancamento_servidor',
        'id' : hash[1],
        'hs' : $('#hs').val(),
        'to' : $('#cliente').val(),
        'controle'   : parseFloat("0" + $('#controle').val()),
        'id_cliente' : $('#id_cliente').val(),
        'id_unid_gestora' : $('#id_unid_gestora').val(),
        'id_unid_lotacao' : $('#id_unid_lotacao').val(),
        'id_evento'       : $('#id_evento').val(),
        'ano_mes'    : $('#ano_mes').val(),
        'sequencia'  : sequencia,
        'id_servidor': id_servidor,
        'quant'      : '',
        'valor'      : '',
        'obs'        : obs
    };

    if (quant !== null) params.quant = quant;
    if (valor !== null) params.valor = valor;
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './lancar_eventos_dao.php',
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
                if(callback && typeof(callback) === "function") {
                    callback(retorno);
                }
            } else {
                mensagem_erro(retorno);
            }
        },
        error: function (request, status, error) {
            mensagem_erro("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function editarLancamentoEvento(id) {
    var controle = id.replace("editar_evento_lancamento_", "");
    
    $('#op').val("editar_lancamento");
    
    $('#controle').val( $('#controle_' + controle).val() );
    $('#id_cliente').val( $('#id_cliente_' + controle).val() );
    $('#ano_mes').val( $('#ano_mes_' + controle).val() );
    $('#id_unid_gestora').val( $('#id_unid_gestora_' + controle).val() );
    $('#id_unid_lotacao').val( $('#id_unid_lotacao_' + controle).val() );
    $('#id_evento').val( $('#id_evento_' + controle).val() );
    $('#data').val( $('#data_' + controle).val() );
    $('#situacao').val( $('#situacao_' + controle).val() );
    $('#importado').prop('checked', ($('#importado_' + controle).val() === '1')); 
    
    $('#ano_mes').prop('disabled', true); 
    $('#id_unid_gestora').prop('disabled', true); 
    $('#id_unid_lotacao').prop('disabled', true); 
    $('#id_evento').prop('disabled', true); 
    
    $('#ano_mes').trigger('chosen:updated');
    $('#id_unid_gestora').trigger('chosen:updated');
    $('#id_unid_lotacao').trigger('chosen:updated');
    $('#id_evento').trigger('chosen:updated');
    $('#situacao').trigger('chosen:updated');
    
    $('input[type="checkbox"].custom-checkbox').uniform();
    
    //document.getElementById("panel_titulo").style.display    = 'none';
    document.getElementById("panel_pesquisa").style.display  = 'none';
    document.getElementById("panel_resultado").style.display = 'none';
    $('#panel_cadastro').fadeIn( 400, "linear" );
    $('#panel_lancamentos').fadeIn( 400, "linear" );

    var id_sessao = $('#id_sessao').val();
    var lg_sessao = $('#lg_sessao').val();
    carregarServidoresLancamento(id_sessao, lg_sessao);
}

function excluirLancamentoEvento(id) {
    var controle = id.replace("excluir_evento_lancamento_", "");
    var i_linha = document.getElementById("linha_" + controle);
    var colunas = i_linha.getElementsByTagName('td');
    if ( parseInt($('#importado_' + controle).val()) === 1 ) {
        mensagem_alerta("Este lançamento já foi <strong>importado</strong> para o sistema de folha Remuneratus$ na central e não poderá ser excluído.<br>Entre em contato com a direção.");
    } else
    if ( parseInt($('#situacao_' + controle).val()) !== 0 ) {
        var situacao = "";
        if ( parseInt($('#situacao_' + controle).val()) === 1 ) {
            situacao = "finalizado";
        } else {
            situacao = "cancelado";
        }
        mensagem_alerta("Este lançamento está <strong>" + situacao + "</strong> e não poderá ser excluído.<br>Entre em contato com a direção.");
    } else {
        mensagem_confirmar("Confirma a exclusão do lançamento <strong><code>" + colunas[0].firstChild.nodeValue + " (" + colunas[1].firstChild.nodeValue + " - " + colunas[2].firstChild.nodeValue + ")</code></strong>?", function(){
            var link = document.getElementById("btnC_confirma_msg");
            link.onclick = function() {
                var params = {
                    'ac' : 'excluir_lancamento_evento',
                    'hs' : $('#hs').val(),
                    'to' : $('#id_cliente_' + controle).val(),
                    'ug' : $('#id_unid_gestora_' + controle).val(),
                    'lo' : $('#id_unid_lotacao_' + controle).val(),
                    'ev' : $('#id_evento_' + controle).val(),
                    'cp' : $('#ano_mes_' + controle).val(),
                    'id' : controle
                };

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : './lancar_eventos_dao.php',
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
                            
                            if (typeof($('#qtde_servidores')) !== "undefined") {
                                var qtde_servidores = (parseInt($('#qtde_servidores').val()) - 1);
                                $('#qtde_servidores').val( qtde_servidores );
                            }
                        } else {
                            $('#btnF_confirma_msg').trigger("click");
                            mensagem_erro( "<p><strong>Erro ao tentar excluir o lançamento selecionado:</strong> <br><br>" + retorno + "</p>" );
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
}

function verificar_lancamento_servidor(servidor, callback) {
    var params = {
        'ac' : 'lancamento_servidor',
        'hs' : $('#hs').val(),
        'to' : $('#id_cliente').val(),
        'ug' : $('#id_unid_gestora').val(),
        'lo' : $('#id_unid_lotacao').val(),
        'ev' : $('#id_evento').val(),
        'cp' : $('#ano_mes').val(),
        'id' : $('#controle').val(),
        'sv' : servidor
    };

    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './lancar_eventos_dao.php',
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
            if (callback && typeof(callback) === "function") {
                callback(retorno);
            }
        },
        error: function (request, status, error) {
            mensagem_erro( "<p><strong>Erro ao tentar executar script!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function excluirLancamentoServidor(id) {
    var referencia = id.replace("excluir_servidor_lancamento_", "");
    var i_linha = document.getElementById("linha_servidor_" + referencia);
    var colunas = i_linha.getElementsByTagName('td');
    if ( $('#importado').is(":checked") ) {
        mensagem_alerta("Este lançamento já foi <strong>importado</strong> para o sistema de folha Remuneratus$ na central e não poderá ser excluído.<br>Entre em contato com a direção.");
    } else
    if ( parseInt($('#situacao').val()) !== 0 ) {
        var situacao = "";
        if ( parseInt($('#situacao').val()) === 1 ) {
            situacao = "finalizado";
        } else {
            situacao = "cancelado";
        }
        mensagem_alerta("Este lançamento está <strong>" + situacao + "</strong> e não poderá ser excluído.<br>Entre em contato com a direção.");
    } else {
        mensagem_confirmar("Confirma a exclusão do lançamento para o servidor <strong><code>" + colunas[2].firstChild.nodeValue + "</code></strong>?", function(){
            var link = document.getElementById("btnC_confirma_msg");
            link.onclick = function() {
                var params = {
                    'ac' : 'excluir_lancamento_servidor',
                    'hs' : $('#hs').val(),
                    'to' : $('#id_cliente').val(),
                    'ug' : $('#id_unid_gestora').val(),
                    'lo' : $('#id_unid_lotacao').val(),
                    'ev' : $('#id_evento').val(),
                    'cp' : $('#ano_mes').val(),
                    'id' : $('#controle').val(),
                    'sv' : $('#id_servidor_' + referencia).val()
                };

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : './lancar_eventos_dao.php',
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
                            var qtde_servidores = parseInt("0" + $('#qtde_servidores').val()) - 1;
                            $('#qtde_servidores').val(qtde_servidores);
                            
                            $('#btnF_confirma_msg').trigger("click");
                            RemoveTableRow(i_linha);
                        } else {
                            $('#btnF_confirma_msg').trigger("click");
                            mensagem_erro( "<p><strong>Erro ao tentar excluir o lançamento do servidor selecionado:</strong> <br><br>" + retorno + "</p>" );
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
}

function situacao_lancamento_evento(situacao) {
    if ( $('#importado').is(":checked") ) {
        mensagem_alerta("Este lançamento já foi <strong>importado</strong> para o sistema de folha Remuneratus$ na central e não poderá ser alterado.<br>Entre em contato com a direção.");
    } else
    if ( parseInt($('#situacao').val()) === 2 ) {
        mensagem_alerta("Este lançamento está <strong>cancelado</strong> e não poderá ser excluído.<br>Entre em contato com a direção.");
    } else {
        var pergunta = "";
        if ( parseInt(situacao) === 1 ) {
            pergunta = "Confirma a <strong>finalização</strong> deste lançamento?";
        } else {
            pergunta = "Confirma a <strong>reabertura</strong> deste lançamento?";
        }
        mensagem_confirmar(pergunta, function(){
            var link = document.getElementById("btnC_confirma_msg");
            link.onclick = function() {
                var params = {
                    'ac' : 'situacao_lancamento_evento',
                    'hs' : $('#hs').val(),
                    'to' : $('#id_cliente').val(),
                    'ug' : $('#id_unid_gestora').val(),
                    'lo' : $('#id_unid_lotacao').val(),
                    'ev' : $('#id_evento').val(),
                    'cp' : $('#ano_mes').val(),
                    'id' : $('#controle').val(),
                    'st' : situacao
                };

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : './lancar_eventos_dao.php',
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
                            
                            $('#situacao_' +  + parseInt($('#controle').val())).val(situacao);
                            $('#situacao').val(situacao);
                            $('#situacao').trigger('chosen:updated');
                            
                            $('#ano_mes').prop('disabled', (parseInt(situacao) === 1)); 
                            $('#id_unid_gestora').prop('disabled', (parseInt(situacao) === 1)); 
                            $('#id_unid_lotacao').prop('disabled', (parseInt(situacao) === 1)); 
                            $('#id_evento').prop('disabled', (parseInt(situacao) === 1)); 
                            
                            var id_sessao = $('#id_sessao').val();
                            var lg_sessao = $('#lg_sessao').val();
                            carregarServidoresLancamento(id_sessao, lg_sessao);
                        } else {
                            $('#btnF_confirma_msg').trigger("click");
                            mensagem_erro( "<p><strong>Erro ao tentar excluir o lançamento do servidor selecionado:</strong> <br><br>" + retorno + "</p>" );
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
}

function finalizar_lancamento() {
    if ($('#situacao').val() === "0") {
        situacao_lancamento_evento("1");
    }
}

function reabrir_lancamento() {
    if ($('#situacao').val() === "1") {
        situacao_lancamento_evento("0");
    }
}

function lancarEventos(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'novo_lancamento',
        'id' : hash[1]
    };

    if (parseInt("0" + $('#cliente').val()) === 0) {
        mensagem_alerta("Usuário <strong>não está associado ao cadastro de um cliente</strong>!<br>Favor, entre em contato com o suporte da plataforma.");
    } else {
        $('#op').val("novo_lancamento");
        $('#controle').val( "00000" );
        $('#id_cliente').val( $('#cliente').val() );
        $('#data').val( $('#hoje').val() );
        $('#ano_mes').val( $('#competencia_atual').val());
        $('#id_unid_gestora').val("0");
        $('#id_unid_lotacao').val("0");
        $('#id_evento').val("0");
        $('#situacao').val("0");

        $('#importado').prop('checked', false); 

        $('#ano_mes').prop('disabled', false); 
        $('#id_unid_gestora').prop('disabled', false); 
        $('#id_unid_lotacao').prop('disabled', false); 
        $('#id_evento').prop('disabled', false); 

        $('#ano_mes').trigger('chosen:updated');
        $('#id_unid_gestora').trigger('chosen:updated');
        $('#id_unid_lotacao').trigger('chosen:updated');
        $('#id_evento').trigger('chosen:updated');
        $('#situacao').trigger('chosen:updated');

        $('input[type="checkbox"].custom-checkbox').uniform();

        $('#btn_form_salvar').prop('disabled', false); 

        document.getElementById("panel_pesquisa").style.display  = 'none';
        document.getElementById("panel_resultado").style.display = 'none';
        $('#panel_cadastro').fadeIn( 400, "linear" );
        $('#panel_lancamentos').fadeIn( 400, "linear" );

        carregarServidoresLancamento(id, us);
    }
}

function salvarControleEventoMensal(id, us) {
    var retorno = false;
    try {
        var agora = new Date();
        $('#data').val(zero_esquerda(agora.getDate(), 2)  + "/" + zero_esquerda(agora.getMonth()+1, 2) + "/" + agora.getFullYear());
        $('#hora').val(zero_esquerda(agora.getHours(), 2) + ":" + zero_esquerda(agora.getMinutes(), 2) + ":" + zero_esquerda(agora.getSeconds(), 2));
        
        var params = {
            'ac' : 'gravar_lancamento_evento',
            'op' : $('#op').val(),
            'hs' : $('#hs').val(),
            'id' : $('#id').val(),
            'id_cliente' : $('#id_cliente').val(),
            'controle' : parseFloat("0" + $('#controle').val()),
            'ano_mes'  : $('#ano_mes').val(),
            'id_unid_gestora' : $('#id_unid_gestora').val(),
            'id_unid_lotacao' : $('#id_unid_lotacao').val(),
            'id_evento'       : $('#id_evento').val(),
            'data'     : $('#data').val(),
            'hora'     : $('#hora').val(),
            'situacao' : $('#situacao').val()
        };
        
        var msg = "";
        var mrc = "<i class='glyph-icon icon-edit'></i>&nbsp;";
        
        if (parseInt("0" + params.id_cliente)       === 0) msg += mrc + "Cliente<br>";
        if (parseInt("0" + params.ano_mes)          === 0) msg += mrc + "Competência<br>";
        if (parseInt("0" + params.id_unid_gestora)  === 0) msg += mrc + "Unidade Gestora<br>";
        if (parseInt("0" + params.id_unid_lotacao)  === 0) msg += mrc + "Unidade de Lotação<br>";
        if (parseInt("0" + params.id_evento)        === 0) msg += mrc + "Evento de Lançamento<br>";
        
        if (msg.trim() !== "") {
            mensagem_alerta( "<p><strong>Os campos listados têm seu preenchimento obrigatório:</strong> <br><br>" + msg + "</p>" );
        } else {
            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : './lancar_eventos_dao.php',
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
                    var file_json = "../downloads/lanc_evento_" + params.hs + ".json"; 
                    var retorno   = data;
                    if (retorno === "OK") {
                        $.getJSON(file_json, function(data){
                            this.qtd = data.form.length;
                            if (params.op === "novo_lancamento") {
                                $('#controle').val(data.form[0].controle);
                                AddTableRowLancamentoEvento(data.form[0].table_tr);
                            } else 
                            if (params.op === "editar_lancamento") {
                                //var referencia = params.id_unid_gestora + "_" + params.id_unid_lotcao + "_" + params.id_evento + "_" + params.ano_mes;
                                var referencia = params.controle;
                                var i_linha = document.getElementById("linha_" + referencia);
                                var colunas = i_linha.getElementsByTagName('td');

                                colunas[1].firstChild.nodeValue = $('#id_unid_gestora option:selected').text();
                                colunas[2].firstChild.nodeValue = $('#id_unid_lotacao option:selected').text();
                                colunas[3].firstChild.nodeValue = data.form[0].rubrica;
                                colunas[4].firstChild.nodeValue = $('#id_evento option:selected').text();
                                colunas[5].firstChild.nodeValue = data.form[0].tipo;
                                colunas[6].firstChild.nodeValue = data.form[0].servidores;
                                colunas[7].firstChild.nodeValue = "...";

                                $('#controle_' + referencia).val( params.controle );
                                $('#id_cliente_' + referencia).val( params.id_cliente );
                                $('#id_unid_gestora_' + referencia).val( params.id_unid_gestora );
                                $('#id_unid_lotacao_' + referencia).val( params.id_unid_lotacao );
                                $('#id_evento_' + referencia).val( params.id_evento );
                                $('#data_' + referencia).val( params.data );
                                $('#situacao_' + referencia).val( params.situacao );
                            }

                            $('#btn_form_fechar').attr('disabled', false);
                            $('#btn_form_salvar').attr('disabled', false);
                            if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');

                            carregarServidoresLancamento(id, us);
                        });
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
    AddTableRowLancamentoEvento = function(table_tr) {

    // Verifica se exite tabela para exibição nos dados na página atual,
    // e caso não exista, construí-la.
    var pagina = document.getElementById("tabela-lancamentos"); 
    var tabela = "";
    
    if ( pagina.innerHTML.indexOf("datatable-responsive") === -1 ) {
        tabela  = "<a id='ancora_datatable-responsive'></a>";
        tabela += "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
        tabela += "    <thead>";
        tabela += "        <tr class='custom-font-size-12'>";
        tabela += "            <th data-orderable='false' style='text-align: center;'>#</th>";
        tabela += "            <th>UG</th>";
        tabela += "            <th>Lotação</th>";
        tabela += "            <th>Rubrica</th>";
        tabela += "            <th>Evento</th>";
        tabela += "            <th data-orderable='false' style='text-align: center;'>Tipo</th>";
        tabela += "            <th class='numeric' data-orderable='false' style='text-align: center;'>Servidores</th>";
        tabela += "            <th class='numeric' data-orderable='false' style='text-align: right;'>Total</th>";
        tabela += "            <th data-orderable='false' style='text-align: center;'></th>";
        tabela += "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
        tabela += "        </tr>";
        tabela += "    </thead>";
        tabela += "    <tbody>";
        tabela += "    </tbody>";
        tabela += "</table>";
        
        $('#tabela-lancamentos').html(tabela);
    }
    
    $("#datatable-responsive").append(table_tr);
    if ( tabela !== "" ) configurarTabelaEventosLancados();

    return false;
  };
})(jQuery);
