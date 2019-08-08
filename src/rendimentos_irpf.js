/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function consultarRendimentosIRPF(hash, id, sv) {
    var unidade  = id.split("_");
    var servidor = sv.split("_");
    var params = {
        'ac' : 'consultar_rendimentos',
        'pr' : 'tabela',
        'un' : unidade[1],
        'sv' : servidor[1],
        'nr_calendario' : $('#nr_calendario').val()
    };
    var nm_arquivo = "IRPF_" + params.sv + params.nr_calendario + "_" + hash + ".txt";
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'src/rendimentos_irpf_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#page-wait').html( loading_spinner() );
            $('#tabela-rendimentos').html("");
            
            $('#btn_consultar').attr('disabled', true);
//            $('#btn_imprimir').attr ('disabled', true);
//            $('#btn_exportar').attr ('disabled', true);
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#page-wait').html("");
            $('#tabela-rendimentos').html(data);
            
            $('#btn_consultar').attr('disabled', false);
//            $('#btn_imprimir').attr ('disabled', false);
//            $('#btn_exportar').attr ('disabled', false);
            configurarTabelaContraCheque();
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#tabela-rendimentos').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
            
            $('#btn_consultar').attr('disabled', false);
//            $('#btn_imprimir').attr ('disabled', true);
//            $('#btn_exportar').attr ('disabled', true);
        }
    });  
    // Finalizamos o Ajax
}

function configurarTabelaRendimentosIRPF(){
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
        "order": [[0, 'desc']], // "order": [] <-- Ordenação indefinida
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

function pdfRendimentosIRPF(obj) {
    var parametros  = obj.split("_");
    var params = {
        'ac' : 'pdf_rendimentos_irpf',
        'un' : parametros[0]     ,
        'sv' : parametros[2]     ,
        'nr_cal' : parametros[3] ,
        'nr_exe' : parametros[4]
    };
    
    var str = 
        "?un="  + parametros[0] + 
        "&ser=" + params.sv     + 
        "&cal=" + params.nr_cal + 
        "&exe=" + params.nr_exe;
    
    window.open("src/rendimentos_irpf_print.php" + str, "_blank");
}
