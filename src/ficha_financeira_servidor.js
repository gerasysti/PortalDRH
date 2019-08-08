/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function consultarFichaFinanceira(hash, id, sv) {
    var unidade  = id.split("_");
    var servidor = sv.split("_");
    var params = {
        'ac' : 'consultar_fichafinanceira',
        'pr' : 'tabela',
        'un' : unidade[1],
        'sv' : servidor[1],
        'cp' : $('#id_servidor').val(),
        'nr_exercicio' : $('#nr_exercicio').val()
    };
    var nm_arquivo = "FFS_" + params.sv + params.nr_exercicio + "_" + hash + ".txt";
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'src/ficha_financeira_servidor_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#page-wait').html( loading_spinner() );
            $('#tabela-fichafinanceira').html("");
            
            $('#btn_consultar').attr('disabled', true);
            $('#btn_imprimir').attr ('disabled', true);
            $('#btn_exportar').attr ('disabled', true);
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#page-wait').html("");
            $('#tabela-fichafinanceira').html(data);
            
            $('#btn_consultar').attr('disabled', false);
            $('#btn_imprimir').attr ('disabled', false);
            $('#btn_exportar').attr ('disabled', false);
            //configurarFichaFinanceiraPF();
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#tabela-fichafinanceira').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
            
            $('#btn_consultar').attr('disabled', false);
            $('#btn_imprimir').attr ('disabled', true);
            $('#btn_exportar').attr ('disabled', true);
        }
    });  
    // Finalizamos o Ajax
}

function configurarFichaFinanceiraPF(){
    // Configurando Tabela
    $('#datatable-responsive').DataTable({
        "paging": false,
        "pageLength": 50, // Apenas 50 registros por paginação
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": true,
        "processing": true,
//        "columns": [
//            null, // Jan
//            null, // Fev
//            null, // Mar
//            null, // Abr
//            null, // Jun
//            null, // Jul
//            null, // Ago
//            null, // Set
//            null, // Out
//            null, // Nov
//            null, // Dez
//            null, // 1p13o.
//            null, // 2p13o.
//            null, // Abono
//            null  // Total
//        ],
        "order": [], // "order": [] <-- Ordenação indefinida; "order": [[0, 'desc']] <-- Ordem decrescente na 1a. coluna
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

function imprimirFichaFinanceira(hash, id, sv) {
    var unidade  = id.split("_");
    var servidor = sv.split("_");
    var params = {
        'ac' : 'consultar_fichafinanceira',
        'pr' : 'tabela',
        'un' : unidade[1],
        'sv' : servidor[1],
        'nr_ano' : $('#nr_exercicio').val()
    };
    
    var str = 
        "?un="  + hash      + 
        "&srv=" + params.sv + 
        "&ano=" + params.nr_ano;
    
    window.open("src/ficha_financeira_servidor_print.php" + str, "_blank");
}

function exportarFichaFinanceira(hash, un, sv, extensao) {
    var unidade  = un.split("_");
    var servidor = sv.split("_");
    var params = {
        'ac' : 'consultar_fichafinanceira',
        'un' : unidade[1],
        'sv' : servidor[1],
        'nr_ano' : $('#nr_exercicio').val()
    };
    var nm_arquivo = "FFS_" + params.sv + params.nr_ano + "_" + hash + ".txt";
    window.open("src/baixar.php?arquivo=../downloads/" + nm_arquivo, "_blank");
}