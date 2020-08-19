/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function consultarRemuneracaoServidor(hash, id) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'consultar_remuneracao_servidor',
        'pr' : 'tabela',
        'un' : unidade[1],
        'nr_ano' : $('#nr_ano').val(),
        'nr_mes' : $('#nr_mes').val(),
        'nr_par' : $('#nr_par').val(),
        'id_vin' : $('#id_vinculo').val()
    };
    var nm_arquivo = "Renumeracao_Servidor_" + params.nr_ano + params.nr_mes + "_" + hash + ".txt";
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'src/remuneracao_servidor_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#page-wait').html( loading_spinner() );
            $('#tabela-servidores').html("");
            
            $('#btn_consultar').attr('disabled', true);
            $('#btn_imprimir').attr ('disabled', true);
            $('#btn_exportar').attr ('disabled', true);
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#page-wait').html("");
            $('#tabela-servidores').html(data);
            
            $('#btn_consultar').attr('disabled', false);
            $('#btn_imprimir').attr ('disabled', false);
            $('#btn_exportar').attr ('disabled', false);
            configurarTabela();
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#tabela-servidores').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
            
            $('#btn_consultar').attr('disabled', false);
            $('#btn_imprimir').attr ('disabled', true);
            $('#btn_exportar').attr ('disabled', true);
        }
    });  
    // Finalizamos o Ajax
}

function configurarTabela(){
    // Configurando Tabela
    $('#datatable-responsive').DataTable({
        "paging": true,
        "pageLength": 10, // Apenas 10 registros por paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            null, // Matricula
            null, // Servidor
            null, // Cargo/Função
            null, // Vínculo
            null, // Admissão
            null, // Dias Trabalhados
            null, // Base
            null, // Total Vencimento
            null, // Descontos
            null, // Salário
            null  // Situação
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
}

function imprimir_remuneracao_servidor(hash, id) {
    var params = {
        'ac' : 'imprimir_remuneracao_servidor',
        'un' : hash,
        'id' : id,
        'hs' : $('#hash_arquivo').val(),
        'ano' : $('#nr_ano').val(),
        'mes' : $('#nr_mes').val(),
        'par' : $('#nr_par').val(),
        'vin' : $('#id_vinculo').val()
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'src/remuneracao_servidor_print.php',
        // Definimos o tipo de requisição
        type: 'get',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#page-wait').html( loading_spinner() );
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#page-wait').html("");
            if (data.indexOf("OK") !== -1) {
                window.open("../downloads/" + params.hs + ".pdf", "_blank");
            } else {
                $('#page-wait').html("<p><strong>Erro ao tentar gerar arquivo PDF</strong> <br><br>" + data + "</p>");
            }
        },
        error: function (request, status, error) {
            $('#page-wait').html( "<p><strong>Erro ao tentar gerar o arquivo pdf!</strong> <br><br>(" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function imprimirRemuneracaoServidor(hash, id) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'imprimir_remuneracao_servidor',
        'un' : unidade[1],
        'nr_ano' : $('#nr_ano').val(),
        'nr_mes' : $('#nr_mes').val(),
        'nr_par' : $('#nr_par').val(),
        'id_vin' : $('#id_vinculo').val()
    };
    
    var str = 
        "?un="  + hash + 
        "&ano=" + params.nr_ano + 
        "&mes=" + params.nr_mes + 
        "&par=" + params.nr_par + 
        "&vin=" + params.id_vin;
    
    window.open("src/remuneracao_servidor_print.php" + str, "_blank");
}

function exportarRemuneracaoServidor(hash, id, extensao) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'consultar_remuneracao_servidor',
        'pr' : extensao,
        'un' : unidade[1],
        'nr_ano' : $('#nr_ano').val(),
        'nr_mes' : $('#nr_mes').val(),
        'nr_par' : $('#nr_par').val(),
        'id_vin' : $('#id_vinculo').val()
    };
    var nm_arquivo = "Renumeracao_Servidor_" + params.nr_ano + params.nr_mes + "_" + hash + ".txt";
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'src/remuneracao_servidor_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#page-wait').html( loading_spinner() );
            document.getElementById("tabela-servidores").style.display = 'none';
        },
        // Colocamos o retorno na tela
        success : function(data){
            var retorno = data;
            $('#page-wait').html("");
            document.getElementById("tabela-servidores").style.display = 'block';
            if (retorno === "OK") {
                window.open("src/baixar.php?arquivo=../downloads/" + nm_arquivo, "_blank");
            }
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            //$('#tabela-servidores').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}