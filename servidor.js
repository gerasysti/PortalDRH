/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var nome_servidor = "";
var cpf_servidor  = "";
var adm_servidor  = "";

function setNomeServidor(value) {
    nome_servidor = value;
}

function setCpfServidor(value) {
    cpf_servidor = value;
}

function setAdmissaoServidor(value) {
    adm_servidor = value;
}

function body_sizer_servidor() {
    var windowHeight = $(window).height();
    var headerHeight = $('#page-header').height();
    var contentHeight = windowHeight - headerHeight - 12;

    $('#page-sidebar').css('height', contentHeight);
    $('.scroll-sidebar').css('height', contentHeight);
    $('#page-content').css('min-height', contentHeight);
};

function home_servidor() {
    var str = "";

    str += "<div id='page-content'>";
    str += "    <div class='col-md-12'>";
    str += "        <div id='page-title'>";
    str += "            <h2><strong>" + getNomeUnidade() + "</strong></h2>";
    str += "            <p><strong>"  + getCnpjUnidade() + "</strong></p>";
    str += "            <br>";
    str += "            <h2><strong>" + nome_servidor + "</strong></h2>";
    str += "            <p><strong>"  + cpf_servidor + "</strong></p>";
    str += "            <p><strong>"  + adm_servidor + "</strong></p>";
    str += "        </div>";
    str += "        <div id='page-wait'>";
    str += "            <a href='#' class='btn btn-md btn-default overlay-button hide' data-style='dark' data-theme='bg-default' data-opacity='60' id='link_wait'>";
    str += "            </a>";
    str += "        </div>";
    str += "    </div>";
    str += "</div>";
    
    $('#descktop').html(str);
    body_sizer_servidor();
}

function wait() {
    var str = "";
    str += "<div id='loading'>";
    str += "    <div class='spinner'>";
    str += "        <div class='bounce1'></div>";
    str += "        <div class='bounce2'></div>";
    str += "        <div class='bounce3'></div>";
    str += "    </div>";
    str += "</div>";
    
    $('#descktop').html(str);
    setTimeout(function() {
        $('#loading').fadeOut( 400, "linear" );
    }, 300);
}

function loading_spinner() {
    var str = "";
    
    str += "<div class='loading-spinner'>";
    str += "    <i class='bg-green'></i>";
    str += "    <i class='bg-green'></i>";
    str += "    <i class='bg-green'></i>";
    str += "    <i class='bg-green'></i>";
    str += "    <i class='bg-green'></i>";
    str += "    <i class='bg-green'></i>";
    str += "</div>";
    
    return str;
}

function alterarSenha() {
    var params = {
        'ac' : 'alterar_senha',
        'un' : 0,
        'sv' : 0
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './src/login_formulario.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            //$('#link_wait').trigger('click');
            $('#page-wait').html( loading_spinner() );
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#descktop').html(data);
            body_sizer_servidor();
//            $(".spinner-input").spinner();
//            $('.input-switch').bootstrapSwitch();
//            $(".multi-select").multiSelect();
//            $(".ms-container").append('<i class="glyph-icon icon-exchange"></i>');
            $(".chosen-select").chosen();
            $(".chosen-search").append('<i class="glyph-icon icon-search"></i>');
            $(".chosen-single div").html('<i class="glyph-icon icon-caret-down"></i>');
            
            //$('#datatable-responsive').DataTable( {
            //    responsive: true
            //} );
            //$('.dataTables_filter input').attr("placeholder", "Search...");
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#descktop').html("Erro na chamada da página!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function contracheque(id, sv) {
    var unidade  = id.split("_");
    var servidor = sv.split("_");
    var params = {
        'ac' : 'servidores',
        'un' : unidade[1],
        'sv' : servidor[1],
        'cp' : servidor[2]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './src/contra_cheque.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            //$('#link_wait').trigger('click');
            $('#page-wait').html( loading_spinner() );
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#descktop').html(data);
            body_sizer_servidor();
//            $(".spinner-input").spinner();
//            $('.input-switch').bootstrapSwitch();
//            $(".multi-select").multiSelect();
//            $(".ms-container").append('<i class="glyph-icon icon-exchange"></i>');
            $(".chosen-select").chosen();
            $(".chosen-search").append('<i class="glyph-icon icon-search"></i>');
            $(".chosen-single div").html('<i class="glyph-icon icon-caret-down"></i>');
            
            //$('#datatable-responsive').DataTable( {
            //    responsive: true
            //} );
            //$('.dataTables_filter input').attr("placeholder", "Search...");
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#descktop').html("Erro na chamada da página!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function rendimentos_irpf(id, sv) {
    var unidade  = id.split("_");
    var servidor = sv.split("_");
    var params = {
        'ac' : 'servidores',
        'un' : unidade[1],
        'sv' : servidor[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './src/rendimentos_irpf.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            //$('#link_wait').trigger('click');
            $('#page-wait').html( loading_spinner() );
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#descktop').html(data);
            body_sizer_servidor();
            $(".chosen-select").chosen();
            $(".chosen-search").append('<i class="glyph-icon icon-search"></i>');
            $(".chosen-single div").html('<i class="glyph-icon icon-caret-down"></i>');
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#descktop').html("Erro na chamada da página!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function ficha_financeira_servidor(id, sv) {
    var unidade  = id.split("_");
    var servidor = sv.split("_");
    var params = {
        'ac' : 'servidores',
        'un' : unidade[1],
        'sv' : servidor[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './src/ficha_financeira_servidor.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            //$('#link_wait').trigger('click');
            $('#page-wait').html( loading_spinner() );
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#descktop').html(data);
            body_sizer_servidor();
            $(".chosen-select").chosen();
            $(".chosen-search").append('<i class="glyph-icon icon-search"></i>');
            $(".chosen-single div").html('<i class="glyph-icon icon-caret-down"></i>');
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#descktop').html("Erro na chamada da página!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}