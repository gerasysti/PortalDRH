/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function consultarContraCheque(hash, id, sv) {
    var unidade  = id.split("_");
    var servidor = sv.split("_");
    var params = {
        'ac' : 'consultar_contra_cheque',
        'pr' : 'tabela',
        'un' : unidade[1],
        'sv' : servidor[1],
        'cp' : $('#id_servidor').val(), // Lista de vários vínculos do funcionário por Função
        'nr_ano' : $('#nr_ano').val(),
        'nr_mes' : $('#nr_mes').val(),
        'nr_par' : $('#nr_par').val()
    };
    var nm_arquivo = "CCH_" + params.sv + params.nr_ano + params.nr_mes + params.nr_par + "_" + hash + ".txt";
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'src/contra_cheque_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#page-wait').html( loading_spinner() );
            $('#tabela-contracheques').html("");
            
            $('#btn_consultar').attr('disabled', true);
//            $('#btn_imprimir').attr ('disabled', true);
//            $('#btn_exportar').attr ('disabled', true);
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#page-wait').html("");
            $('#tabela-contracheques').html(data);
            
            $('#btn_consultar').attr('disabled', false);
//            $('#btn_imprimir').attr ('disabled', false);
//            $('#btn_exportar').attr ('disabled', false);
            configurarTabelaContraCheque();
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#tabela-contracheques').html("Erro na execução da pesquisa!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
            
            $('#btn_consultar').attr('disabled', false);
//            $('#btn_imprimir').attr ('disabled', true);
//            $('#btn_exportar').attr ('disabled', true);
        }
    });  
    // Finalizamos o Ajax
}

function configurarTabelaContraCheque(){
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

function pdfContraCheque(obj) {
    var parametros  = obj.split("_");
    var params = {
        'ac' : 'pdf_contra_cheque',
        'un' : parametros[0]      ,
        'sv' : parametros[2]      ,
        'nr_ano' : parametros[3]  ,
        'nr_mes' : parametros[4]  ,
        'nr_par' : parametros[5]
    };
    
    var str = 
        "?un="  + parametros[0] + 
        "&ser=" + params.sv     + 
        "&ano=" + params.nr_ano + 
        "&mes=" + params.nr_mes +
        "&par=" + params.nr_par;
    
    //window.open("src/contra_cheque_print.php" + str, "_blank");
    window.open("src/contra_cheque_pdf.php" + str, "_blank");
}

function pdfContraChequeNovo(obj) {
    var parametros  = obj.split("_");
    var params = {
        'ac' : 'pdf_contra_cheque',
        'un' : parametros[0]      ,
        'sv' : parametros[2]      ,
        'nr_ano' : parametros[3]  ,
        'nr_mes' : parametros[4]  ,
        'nr_par' : parametros[5]
    };
    
    var str = 
        "?un="  + parametros[0] + 
        "&ser=" + params.sv     + 
        "&ano=" + params.nr_ano + 
        "&mes=" + params.nr_mes +
        "&par=" + params.nr_par;
    
    window.open("src/contra_cheque_pdf.php" + str, "_blank");
}
