/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function display_system_version_v204() {
    var str = "";
    
    str += "Versão <b>2.0.4</b><br>";
    str += "Copyright &copy; 2021 <strong>Gerasys TI / M Cruz Consultoria.</strong> &nbsp;&nbsp;<br>";
    str += "Todos os direitos reservados. &nbsp;&nbsp;";
    
    $('#system_version').html(str);
}

function body_sizer_controle() {
    var windowHeight = $(window).height();
    var headerHeight = $('#page-header').height();
    var contentHeight = windowHeight - headerHeight - 12;

    $('#page-sidebar').css('height', contentHeight);
    $('.scroll-sidebar').css('height', contentHeight);
    $('#page-content').css('min-height', contentHeight);
};

function overlay_home() {
    var loadertheme   = $(this).attr('data-theme');
    var loaderopacity = $(this).attr('data-opacity');
    var loaderstyle   = $(this).attr('data-style');

    var loader = '<div id="loader-overlay" class="ui-front loader ui-widget-overlay ' + loadertheme + ' opacity-' + loaderopacity + '"><img src="../../assets/images/spinner/loader-' + loaderstyle + '.gif" alt="" /></div>';

    if ( $('#loader-overlay').length ) {
            $('#loader-overlay').remove();
    }

    $('body').append(loader);
}

function home_controle() {
    var str = "";

    str += "<div id='page-content'>";
    str += "    <div class='col-md-12'>";
    str += "        <div id='page-title'>";
    str += "            <h2><strong></strong></h2>";
    str += "            <p><strong></strong></p>";
    str += "            <br>";
    str += "            <h2><strong></strong></h2>";
    str += "            <p><strong></strong></p>";
    str += "            <p><strong></strong></p>";
    str += "        </div>";
    str += "        <div id='page-wait'>";
    str += "            <a href='#' class='btn btn-md btn-default overlay-button hide' data-style='dark' data-theme='bg-default' data-opacity='60' id='link_wait'>";
    str += "            </a>";
    str += "        </div>";
    str += "    </div>";
    str += "</div>";
    
    $('#descktop').html(str);
    body_sizer_controle();
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
    str += "    <i class='bg-green'></i>";
    str += "</div>";
    
    return str;
}

function fechar_cadastro() {
    //document.getElementById("panel_titulo").style.display    = 'block';
    //document.getElementById("panel_pesquisa").style.display  = 'block';
    //document.getElementById("panel_resultado").style.display = 'block';
    document.getElementById("panel_cadastro").style.display  = 'none';
    
    $('#panel_pesquisa').fadeIn( 400, "linear" );
    $('#panel_resultado').fadeIn( 400, "linear" );
}

function formatar_checkbox() {
    $('input[type="checkbox"].custom-checkbox').uniform();
    $('input[type="radio"].custom-radio').uniform();
    $('.custom-select').uniform();

    $(".selector").append('<i class="glyph-icon icon-caret-down"></i>');

    $('.checker span').append('<i class="glyph-icon icon-check"></i>');
    $('.radio span').append('<i class="glyph-icon icon-circle"></i>');
}

function mensagem_confirmar(mensagem, callback) {
    $('#box_confirme_msg').html(mensagem);
    $('#box_confirme').trigger("click");
    // verifica se o parâmetro callback é realmente uma função antes de executá-lo
    if(callback && typeof(callback) === "function") {
        callback();
    }
}

function mensagem_informe(mensagem) {
    $('#box_informe_msg').html(mensagem);
    $('#box_informe').trigger("click");
}

function mensagem_alerta(mensagem) {
    $('#box_alerta_msg').html(mensagem);
    $('#box_alerta').trigger("click");
}

function mensagem_erro(mensagem) {
    $('#box_erro_msg').html(mensagem);
    $('#box_erro').trigger("click");
}

/*
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
*/
function controle_usuario(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'usuarios',
        'id' : hash[1],
        'us' : email[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './usuario_view.php',
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
            body_sizer_controle();
                        
            $(".chosen-select").chosen();
            $(".chosen-search").append('<i class="glyph-icon icon-search"></i>');
            $(".chosen-single div").html('<i class="glyph-icon icon-caret-down"></i>');
            
            consultarUsuario(id, us);
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#descktop').html("Erro na chamada da página!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function controle_cliente(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'clentes',
        'id' : hash[1],
        'us' : email[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './cliente_view.php',
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
            body_sizer_controle();
                        
            $(".chosen-select").chosen();
            $(".chosen-search").append('<i class="glyph-icon icon-search"></i>');
            $(".chosen-single div").html('<i class="glyph-icon icon-caret-down"></i>');
            
            consultarCliente(id, us);
        },
        error: function (request, status, error) {
            $('#page-wait').html("");
            $('#descktop').html("Erro na chamada da página!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function controle_unidade_gestora(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'unidades_gestoras',
        'id' : hash[1],
        'us' : email[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './unidade_gestora_view.php',
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
            body_sizer_controle();
                        
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

function controle_unidade_lotacao(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'unidades_lotacao',
        'id' : hash[1],
        'us' : email[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './unidade_lotacao_view.php',
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
            body_sizer_controle();
                        
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

function controle_tabela_eventos(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'tabela_eventos',
        'id' : hash[1],
        'us' : email[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './evento_view.php',
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
            body_sizer_controle();
                        
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

function controle_tabela_servidores(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'tabela_servidores',
        'id' : hash[1],
        'us' : email[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './servidor_view.php',
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
            body_sizer_controle();
                        
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

function controle_lancar_eventos_mensais(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'lancar_eventos_mensais',
        'id' : hash[1],
        'us' : email[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './lancar_eventos_view.php',
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
            body_sizer_controle();
                        
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

function controle_cargar_horaria_prof(id, us) {
    var hash  = id.split("_");
    var email = us.split("_");
    var params = {
        'ac' : 'lancar_ch_professores',
        'id' : hash[1],
        'us' : email[1]
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : './lancar_chprofessores_view.php',
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
            body_sizer_controle();
                        
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

// Escopo da Declaração de Funções jQuery
(function($) {
  RemoveTableRow = function(handler) {
    var tr = $(handler).closest('tr');

    tr.fadeOut(400, function(){ 
      tr.remove(); 
    }); 

    return false;
  };
})(jQuery);
