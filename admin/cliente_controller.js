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

function configurarTabelaCliente(){
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
            null, // ID
            null, // Nome
            null, // CNPJ
            null, // Município
            null, // UF
            null, // Servidores
            null, // Atualização
            { "width": "120px" }  // Controles
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
}

function consultarCliente(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'consultar_cliente',
        'id' : hash[1],
        'us' : email[1],
        'to' : $('#tipo').val(),
        'ps' : $('#pesquisa').val()
    };

    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './cliente_dao.php',
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
            $('#tabela-clientes').html("");
            
            $('#btn_consultar').attr('disabled', true);
            if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeIn('fast');
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#page-wait').html("");
            $('#tabela-clientes').html(data);
            
            $('#btn_consultar').attr('disabled', false);
            if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
            configurarTabelaCliente();
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#tabela-clientes').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
            
            $('#btn_consultar').attr('disabled', false);
            if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
        }
    });  
    // Finalizamos o Ajax
}

function editarCliente(id) {
    var id_cliente = id.replace("editar_cliente_", "");
    var i_linha = document.getElementById("linha_" + id_cliente);
    var colunas = i_linha.getElementsByTagName('td');
    
    $('#op').val("editar_cliente");
    $('#id').val( id_cliente );
    $('#tipo_orgao').val( $('#tipo_orgao_' + id_cliente).val() );
    $('#nome').val( colunas[1].firstChild.nodeValue );
    $('#cnpj').val( colunas[2].firstChild.nodeValue );
    $('#ender_lograd').val( $('#ender_lograd_' + id_cliente).val() );
    $('#ender_num').val( $('#ender_num_' + id_cliente).val() );
    $('#ender_bairro').val( $('#ender_bairro_' + id_cliente).val() );
    $('#ender_cep').val( $('#ender_cep_' + id_cliente).val() );
    $('#municipio_cod_ibge').val( $('#municipio_cod_ibge_' + id_cliente).val() );
    $('#municipio_nome').val( $('#municipio_nome_' + id_cliente).val() );
    $('#municipio_uf').val( $('#municipio_uf_' + id_cliente).val() );
    $('#telefones').val( $('#telefones_' + id_cliente).val() );
    $('#e_mail').val( $('#e_mail_' + id_cliente).val() );
    $('#dominio').val( $('#dominio_' + id_cliente).val() );
    $('#titulo_portal').val( $('#titulo_portal_' + id_cliente).val() );
    $('#sub_titulo_portal').val( $('#sub_titulo_portal_' + id_cliente).val() );
    $('#logo').val( $('#logo_' + id_cliente).val() );
    $('#brasao_nome').val( $('#brasao_nome_' + id_cliente).val() );

    $('#logo_img').prop('src', $('#logo_' + id_cliente).val());
    $('#logo_img').prop('title', $('#brasao_nome_' + id_cliente).val());
    $('#logo_upload').val("");
    
    $('#exibe_lista').prop("checked",        ($('#exibe_lista_' + id_cliente).val() === "1"));
    $('#enviar_senha_email').prop("checked", ($('#enviar_senha_email_' + id_cliente).val() === "1"));
    $('#contra_cheque').prop("checked",      ($('#contra_cheque_' + id_cliente).val() === "S"));
    $('#margem_consignavel').prop("checked", ($('#margem_consignavel_' + id_cliente).val() === "1"));
    
    $('#atualizacao').val( $('#atualizacao_' + id_cliente).val() );
    $('#situacao').val( $('#situacao_' + id_cliente).val() );
    
    $('#tipo_orgao').trigger('chosen:updated');
    $('#situacao').trigger('chosen:updated');
    $('#municipio_uf').trigger('chosen:updated');
            
    if ($('#tab_1').hasClass('active')) $('#tab_1').removeClass('active');
    if ($('#tab_2').hasClass('active')) $('#tab_2').removeClass('active');
    if ($('#tab_3').hasClass('active')) $('#tab_3').removeClass('active');
    
    if ($('#step-1').hasClass('active')) $('#step-1').removeClass('active');
    if ($('#step-2').hasClass('active')) $('#step-2').removeClass('active');
    if ($('#step-3').hasClass('active')) $('#step-3').removeClass('active');
    
    $('#tab_1').addClass('active');
    $('#step-1').addClass('active');
    
    $('input[type="checkbox"].custom-checkbox').uniform();
    
    //document.getElementById("panel_titulo").style.display    = 'none';
    document.getElementById("panel_pesquisa").style.display  = 'none';
    document.getElementById("panel_resultado").style.display = 'none';
    $('#panel_cadastro').fadeIn( 400, "linear" );
}

function inserirCliente(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'inserir_cliente',
        'id' : hash[1]
    };
    
    $('#op').val("inserir_cliente");
    $('#id').val( "00000" );
    $('#tipo_orgao').val("0");
    $('#nome').val("");
    $('#cnpj').val("");
    $('#telefones').val("");
    $('#e_mail').val("");
    $('#ender_lograd').val("");
    $('#ender_num').val("");
    $('#ender_bairro').val("");
    $('#ender_cep').val("");
    $('#municipio_cod_ibge').val("");
    $('#municipio_nome').val("");
    $('#municipio_uf').val("XX");
    $('#dominio').val("");
    $('#titulo_portal').val("");
    $('#sub_titulo_portal').val("");
    $('#logo').val("");
    $('#brasao_nome').val("");

    $('#logo_img').prop('src', ""); // '../dist/img/brasoe/sssbv.png'
    $('#logo_img').prop('title', "");
    $('#logo_upload').val("");
    
    $('#exibe_lista').prop("checked", false);
    $('#enviar_senha_email').prop("checked", false);
    $('#contra_cheque').prop("checked", false);
    $('#margem_consignavel').prop("checked", false);
    
    $('#atualizacao').val("");
    $('#situacao').val("0");
    
    $('#tipo_orgao').trigger('chosen:updated');
    $('#situacao').trigger('chosen:updated');
    $('#municipio_uf').trigger('chosen:updated');
            
    if ($('#tab_1').hasClass('active')) $('#tab_1').removeClass('active');
    if ($('#tab_2').hasClass('active')) $('#tab_2').removeClass('active');
    if ($('#tab_3').hasClass('active')) $('#tab_3').removeClass('active');
    
    if ($('#step-1').hasClass('active')) $('#step-1').removeClass('active');
    if ($('#step-2').hasClass('active')) $('#step-2').removeClass('active');
    if ($('#step-3').hasClass('active')) $('#step-3').removeClass('active');
    
    $('#tab_1').addClass('active');
    $('#step-1').addClass('active');
    
    $('input[type="checkbox"].custom-checkbox').uniform();
    
    //document.getElementById("panel_titulo").style.display    = 'none';
    document.getElementById("panel_pesquisa").style.display  = 'none';
    document.getElementById("panel_resultado").style.display = 'none';
    $('#panel_cadastro').fadeIn( 400, "linear" );
}

function excluirCliente(id) {
    var id_cliente = id.replace("excluir_cliente_", "");
    var i_linha = document.getElementById("linha_" + id_cliente);
    var colunas = i_linha.getElementsByTagName('td');
    mensagem_confirmar("Confirma a exclusão do cliente <strong>'" + $('#nome_' + id_cliente).val() + " (" + colunas[2].firstChild.nodeValue + ")'</strong>?", function(){
        var link = document.getElementById("btnC_confirma_msg");
        link.onclick = function() {
            var params = {
                'ac' : 'excluir_cliente',
                'hs' : $('#hs').val(),
                'id' : $('#id_' + id_cliente).val(),
                'fn' : $('#funcoes_' + id_cliente).val(),
                'sv' : $('#servidores_' + id_cliente).val(),
                'id_cliente' : $('#id_cliente_' + id_cliente).val()
            };
            
            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : './cliente_dao.php',
                // Definimos o tipo de requisição
                type: 'post',
                // Definimos o tipo de retorno
                dataType : 'html',
                // Dolocamos os valores a serem enviados
                data: params,
                // Antes de enviar ele alerta para esperar
                beforeSend : function(){

                },
                // Colocamos o retorno na tela
                success : function(data){
                    var retorno = data;
                    if (retorno === "OK") {
                        $('#btnF_confirma_msg').trigger("click");
                        RemoveTableRow(i_linha);
                    } else {
                        $('#btnF_confirma_msg').trigger("click");
                        mensagem_erro( "<p><strong>Erro ao tentar excluir o cliente selecionado:</strong> <br><br>" + retorno + "</p>" );
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

function salvarCliente() {
    var retorno = false;
    try {
        var params = {
            'ac' : 'gravar_cliente',
            'op' : $('#op').val(),
            'hs' : $('#hs').val(),
            'id' : $('#id').val(),
            'id_cliente' : $('#id_cliente_padrao').val(),
            'tipo_orgao' : $('#tipo_orgao').val(),
            'nome' : $('#nome').val().trim(),
            'cnpj' : $('#cnpj').val().trim(),
            'telefones' : $('#telefones').val().trim(),
            'e_mail'    : $('#e_mail').val().trim(),
            'ender_lograd' : $('#ender_lograd').val().trim(),
            'ender_num'    : $('#ender_num').val().trim(),
            'ender_bairro' : $('#ender_bairro').val().trim(),
            'ender_cep'    : $('#ender_cep').val(),
            'municipio_cod_ibge' : $('#municipio_cod_ibge').val().trim(),
            'municipio_nome'     : $('#municipio_nome').val().trim(),
            'municipio_uf'       : $('#municipio_uf').val(),
            'dominio'            : $('#dominio').val().trim(),
            'titulo_portal'      : $('#titulo_portal').val().trim(),
            'sub_titulo_portal'  : $('#sub_titulo_portal').val().trim(),
            'exibe_lista'        : '0',
            'enviar_senha_email' : '0',
            'contra_cheque'      : 'N',
            'margem_consignavel' : '0',
            'situacao' : $('#situacao').val()
        };
        // alert( JSON.stringify(params) );
        // $('#box_confirme').trigger("click");
        // mensagem_confirmar( JSON.stringify(params) );
        
        if ( $('#exibe_lista').is(":checked") ) params.exibe_lista = $('#exibe_lista').val();
        if ( $('#enviar_senha_email').is(":checked") ) params.enviar_senha_email = $('#enviar_senha_email').val();
        if ( $('#contra_cheque').is(":checked") ) params.contra_cheque = $('#contra_cheque').val();
        if ( $('#margem_consignavel').is(":checked") ) params.margem_consignavel = $('#margem_consignavel').val();
        
        var logo = {
            'ac' : 'upload_logo_cliente',
            'op' : $('#op').val(),
            'hs' : $('#hs').val(),
            'id' : $('#id').val(),
            'logo_upload' : $('#logo_upload').val()
        }
        
        var arquivo = logo.logo_upload.split('.'); 
        var msg = "";
        var mrc = "<i class='glyph-icon icon-edit'></i>&nbsp;";
        
        if (params.nome === "") msg += mrc + "Identificação : Nome<br>";
        if (params.cnpj === "") msg += mrc + "Identificação : CNPJ<br>";
        if (params.municipio_nome === "") msg += mrc + "Endereço : Município<br>";
        if ((params.municipio_uf === "") || (params.municipio_uf === "XX")) msg += mrc + "Endereço : UF<br>";
        if ((logo.logo_upload !== "") && (arquivo.length > 0) && (arquivo[1] !== "png")) msg += mrc + "Configurar : <strong>Arquivo '" + arquivo[1] + "' inválido</strong><br>";
        
        if (msg.trim() !== "") {
            mensagem_alerta( "<p><strong>Os campos listados têm seu preenchimento obrigatório:</strong> <br><br>" + msg + "</p>" );
        } else {
            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : './cliente_dao.php',
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
                    var file_json = "../downloads/cliente_" + params.hs + ".json"; 
                    var retorno   = data;
                    if (retorno === "OK") {
                        if (params.op === "inserir_cliente") {
                            $.getJSON(file_json, function(data){
                                this.qtd = data.form.length;
                                $('#id').val(data.form[0].id);
                                AddTableRowCliente();
                            });
                        } else 
                        if (params.op === "editar_cliente") {
                            var i_linha = document.getElementById("linha_" + params.id);
                            var colunas = i_linha.getElementsByTagName('td');

                            colunas[1].firstChild.nodeValue = params.nome;
                            colunas[2].firstChild.nodeValue = params.cnpj;
                            colunas[3].firstChild.nodeValue = params.municipio_nome;
                            colunas[4].firstChild.nodeValue = params.municipio_uf; //$('#municipio_uf option:selected').text();

                            $('#id_' + params.id).val( params.id );
                            $('#nome_' + params.id).val( params.id );
                            $('#cnpj_' + params.id).val( params.cnpj );
                            $('#ender_lograd_' + params.id).val( params.ender_lograd );
                            $('#ender_num_' + params.id).val( params.ender_num );
                            $('#ender_bairro_' + params.id).val( params.ender_bairro );
                            $('#ender_cep_' + params.id).val( params.ender_cep );
                            $('#municipio_cod_ibge_' + params.id).val( params.municipio_cod_ibge );
                            $('#municipio_nome_' + params.id).val( params.municipio_nome );
                            $('#municipio_uf_' + params.id).val( params.municipio_uf );
                            $('#telefones_' + params.id).val( params.telefones );
                            $('#e_mail_' + params.id).val( params.e_mail );
                            $('#dominio_' + params.id).val( params.dominio );
                            $('#titulo_portal_' + params.id).val( params.titulo_portal );
                            $('#sub_titulo_portal_' + params.id).val( params.sub_titulo_portal );
//                            $('#logo_' + params.id).val( params.logo );
//                            $('#brasao_nome_' + params.id).val( params.brasao_nome );
                            $('#exibe_lista_' + params.id).val( params.exibe_lista );
                            $('#enviar_senha_email_' + params.id).val( params.enviar_senha_email );
                            $('#contra_cheque_' + params.id).val( params.contra_cheque );
                            $('#margem_consignavel_' + params.id).val( params.margem_consignavel );
                            $('#atualizacao_' + params.id).val( params.atualizacao );
                            $('#situacao_' + params.id).val( params.situacao );
                        }
                        
                        // FAZER UPLOAD DO ARQUIVO DEPOIS DE SALVO O REGISTROS
                        if ((logo.logo_upload !== "") && (arquivo.length > 0) && (arquivo[1] === "png")) {
                            var formData = new FormData();
                            formData.append('arquivo', $('#logo_upload').prop('files')[0]);

                            // Iniciamos o Ajax 
                            $.ajax({
                                // Definimos a url
                                url : "../src/upload.php",
                                enctype: 'multipart/form-data',
                                // Definimos o tipo de requisição
                                type: "post",
                                // Definimos o tipo de retorno
                                dataType : "html",
                                // Dolocamos os valores a serem enviados
                                data: formData,
                                // Parâmetros importantes para upload de arquivos
                                contentType: false,
                                processData: false,
                                // Antes de enviar ele alerta para esperar
                                beforeSend : function(){ 
                                    $('#btn_form_fechar').attr('disabled', true);
                                    $('#btn_form_salvar').attr('disabled', true);
                                },
                                uploadProgress: function(event, position, total, percentComplete) {                      
                                        ;//$("#painel_arquivo_retorno").html("Percentual completado : " + percentComplete + "%");  
                                },
                                // Colocamos o retorno na tela
                                success : function(data){
                                    var retorno = data;
                                    if (retorno === "OK") {
                                        var file_json = "../downloads/arquivo_" + params.hs + ".json";
                                        $.getJSON(file_json, function(data){
                                            var logotipo = {
                                                'ac' : 'logotipo_cliente',
                                                'id' : $('#id').val(),
                                                'hs' : $('#hs').val(),
                                                'logo'        : data.arquivo[0].url,
                                                'brasao_nome' : data.arquivo[0].nome
                                            };
                                            
                                            // Iniciamos o Ajax 
                                            $.ajax({
                                                // Definimos a url
                                                url : './cliente_dao.php',
                                                // Definimos o tipo de requisição
                                                type: 'post',
                                                // Definimos o tipo de retorno
                                                dataType : 'html',
                                                // Dolocamos os valores a serem enviados
                                                data: logotipo,
                                                // Antes de enviar ele alerta para esperar
                                                beforeSend : function(){
                                                    ;
                                                },
                                                // Colocamos o retorno na tela
                                                success : function(data){
                                                    var retorno = data;
                                                    if (retorno === "OK") {
                                                        $('#logo_' + logotipo.id).val( logotipo.logo );
                                                        $('#brasao_nome_' + logotipo.id).val( logotipo.brasao_nome );

                                                        $('#logo_img').prop('src', $('#logo_' + logotipo.id).val());
                                                        $('#logo_img').prop('title', $('#brasao_nome_' + logotipo.id).val());
                                                        $('#logo_upload').val("");
                                                        if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
                                                    } else {
                                                        if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
                                                    }
                                                },
                                                error: function (request, status, error) {
                                                    if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
                                                    mensagem_erro( "<p><strong>Erro ao tentar executar script de atualização do logotipo!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
                                                    return;
                                                }
                                            });  
                                            // Finalizamos o Ajax
                                        });  
                                    } else {
                                        if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
                                        mensagem_erro( "<p><strong>Erro ao tentar executar upload da imagem selecionada:</strong> <br><br>" + retorno + "</p>" );
                                        return;
                                    }
                                },
                                error: function (request, status, error) {
                                    if (typeof($('#loader-overlay')) !== 'undefined') $('#loader-overlay').fadeOut('fast');
                                    mensagem_erro( "<p><strong>Erro ao tentar executar script de upload!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
                                    return;
                                }
                            });  
                            // Finalizamos o Ajax
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
    AddTableRowCliente = function() {

    var referencia = $('#id').val();
    var tabela = "";
    var input  = "";
    var ativo  = "";

    // Verifica se exite tabela para exibição nos dados na página atual,
    // e caso não exista, construí-la.
    var pagina = document.getElementById("tabela-clientes"); 
    
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
    /*
    input = 
          "<input type='hidden' id='id_"            + referencia + "' value='" + $('#id').val() + "'>"
        + "<input type='hidden' id='id_cliente_"    + referencia + "' value='" + $('#id_cliente').val() + "'>"
        + "<input type='hidden' id='nome_"          + referencia + "' value='" + $('#nome').val() + "'>"
        + "<input type='hidden' id='email_"         + referencia + "' value='" + $('#email').val() + "'>"
        + "<input type='hidden' id='ultimo_acesso_" + referencia + "' value='&nbsp;'>"
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
    if ( tabela !== "" ) configurarTabelaCliente();
    */
    return false;
  };
})(jQuery);