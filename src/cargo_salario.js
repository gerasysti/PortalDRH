/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function consultarCargoSalario(hash, id) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'consultar_cargo_salario',
        'pr' : 'tabela',
        'un' : unidade[1],
        'nr_ano' : $('#nr_ano').val(),
        'nr_mes' : $('#nr_mes').val()
    };
    var nm_arquivo = "Tabela_CargoSalario_" + params.nr_ano + params.nr_mes + "_" + hash + ".txt";
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'src/cargo_salario_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#page-wait').html( loading_spinner() );
            $('#tabela-cargos-salarios').html("");
            
            $('#btn_consultar').attr('disabled', true);
            $('#btn_imprimir').attr ('disabled', true);
            $('#btn_exportar').attr ('disabled', true);
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#page-wait').html("");
            $('#tabela-cargos-salarios').html(data);
            
            $('#btn_consultar').attr('disabled', false);
            $('#btn_imprimir').attr ('disabled', false);
            $('#btn_exportar').attr ('disabled', false);
            configurarTabelaCargoSalario();
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#tabela-cargos-salarios').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
            
            $('#btn_consultar').attr('disabled', false);
            $('#btn_imprimir').attr ('disabled', true);
            $('#btn_exportar').attr ('disabled', true);
        }
    });  
    // Finalizamos o Ajax
}

function configurarTabelaCargoSalario(){
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
//        "columns": [
//            null, // Vínculo
//            null, // Cargo/Função
//            null, // Servidores
//            null, // Base
//            null, // Total Vencimento
//            null, // Descontos
//            null  // Salários
//        ],
        "order": [[0, 'asc']], // "order": [] <-- Ordenação indefinida
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

function imprimirCargoSalario(hash, id) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'imprimir_cargo_salario',
        'un' : unidade[1],
        'nr_ano' : $('#nr_ano').val(),
        'nr_mes' : $('#nr_mes').val()
    };
    
    var str = 
        "?un="  + hash + 
        "&ano=" + params.nr_ano + 
        "&mes=" + params.nr_mes;
    
    window.open("src/cargo_salario_print.php" + str, "_blank");
}

function exportarCargoSalario(hash, id, extensao) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'consultar_cargo_salario',
        'pr' : extensao,
        'un' : unidade[1],
        'nr_ano' : $('#nr_ano').val(),
        'nr_mes' : $('#nr_mes').val()
    };
    var nm_arquivo = "TCS_" + params.nr_ano + params.nr_mes + "_" + hash + ".txt";
    window.open("src/baixar.php?arquivo=../downloads/" + nm_arquivo, "_blank");
}