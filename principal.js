/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var nome_unidade = "";
var cnpj_unidade = "";

function setNomeUnidade(value) {
    nome_unidade = value;
}

function setCnpjUnidade(value) {
    cnpj_unidade = value;
}

function getNomeUnidade() {
    return nome_unidade;
}

function getCnpjUnidade() {
    return cnpj_unidade;
}

function body_sizer_principal() {
    var windowHeight = $(window).height();
    var headerHeight = $('#page-header').height();
    var contentHeight = windowHeight - headerHeight - 12;

    $('#page-sidebar').css('height', contentHeight);
    $('.scroll-sidebar').css('height', contentHeight);
    $('#page-content').css('min-height', contentHeight);
};

function home() {
    /*
    var str = "";
    
    str += "<div id='page-content'>";
    str += "    <div class='col-md-12'>";
    str += "        <div id='page-title'>";
    str += "            <h2><strong>" + nome_unidade + "</strong></h2>";
    str += "            <p><strong>"  + cnpj_unidade + "</strong></p>";
    str += "        </div>";
    str += "        <div class='panel ng-scope'>";
    str += "            <div class='panel-body'>";
    str += "                <h3 class='title-hero'>Informações</h3>";
    str += "                <div class='example-box-wrapper'>";
    str += "                    <div class='row'>";
    str += "                        <div class='col-md-4'>";
    str += "                            <div class='tile-box bg-primary'>";
    str += "                                <div class='tile-header'>";
    str += "                                    Vencimento Base";
    str += "                                    <div class='float-right'>";
    str += "                                        <i class='glyph-icon icon-caret-up'></i>";
    str += "                                        0%";
    str += "                                    </div>";
    str += "                                </div>";
    str += "                                <div class='tile-content-wrapper'>";
    str += "                                    <i class='glyph-icon icon-bullhorn'></i>";
    str += "                                    <div class='tile-content'>";
    str += "                                        <span>R$</span> 0,00";
    str += "                                    </div>";
    str += "                                </div>";
    str += "                            </div>";
    str += "                        </div>";
    str += "                    </div>";
    str += "                </div>";
    str += "            </div>";
    str += "        </div>";
    str += "        <div id='page-wait'>";
    str += "            <a href='#' class='btn btn-md btn-default overlay-button hide' data-style='dark' data-theme='bg-default' data-opacity='60' id='link_wait'>";
    str += "            </a>";
    str += "        </div>";
    str += "    </div>";
    str += "</div>";
    
    $('#descktop').html(str);
    body_sizer_principal();
    */    
    var params = {
        'ac' : 'dashboard',
        'id' : $('#id_cliente').val()
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        //url : './dashboard.php',
        url : './src/_home.php',
        // Definimos o tipo de requisição
        type: 'post',
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
            $('#descktop').html(data);
            body_sizer_principal();
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#descktop').html("Erro na chamada da página!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function lei_acesso_informacao() {
    var params = {
        'inc' : 'http://www.planalto.gov.br/ccivil_03/_Ato2011-2014/2011/Lei/L12527.htm',
        'id'  : $('#id_cliente').val()
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './lei.php',
        // Definimos o tipo de requisição
        type: 'post',
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
            $('#descktop').html(data);
            body_sizer_principal();
            $('#panel-body').css('height', ($('#page-content').height() - 60));
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#descktop').html("Erro na chamada da página!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
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
                
function cargos_salarios(id) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'cargos_salarios',
        'un' : unidade[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './src/cargo_salario.php',
        // Definimos o tipo de requisição
        type: 'post',
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
            $('#descktop').html(data);
            body_sizer_principal();
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

function servidores(id) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'servidores',
        'un' : unidade[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './src/remuneracao_servidor.php',
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
            body_sizer_principal();
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

function vinculos(id) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'vinculos',
        'un' : unidade[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './src/remuneracao_vinculo.php',
        // Definimos o tipo de requisição
        type: 'post',
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
            $('#descktop').html(data);
            body_sizer_principal();
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

function cargos(id) {
    var unidade = id.split("_");
    var params = {
        'ac' : 'cargos',
        'un' : unidade[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './src/remuneracao_cargo.php',
        // Definimos o tipo de requisição
        type: 'post',
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
            $('#descktop').html(data);
            body_sizer_principal();
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
