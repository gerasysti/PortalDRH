/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function relembrar_senha() {
//    // Entrada da "div" de efeitos
//    setTimeout(function() {
//        $('#loading').fadeIn( 400, "linear" );
//    }, 300);
//    
    $('#pmResuperarSenha').html("");
    
    $('#login-form').addClass("hide");
    $('#login-forgot').removeClass("hide");
    
    //var clientes = document.getElementById("r_id_cliente");
    //clientes.selectedIndex = 0;
    //$('#r_id_cliente').val("0");
    $('#r_nr_matricula').val("");
    $('#r_nr_cpf').val("");
    $('#r_dt_nascimento').val("");
    $('#r_ds_email').val("");
//    // Saída da "div" de efeitos
//    setTimeout(function() {
//        $('#loading').fadeOut( 400, "linear" );
//    }, 300);
}

function validando_senha(etapa, documento) {
    var id_unidade  = $('#id_unidade').val();
    var id_servidor = $('#id_servidor').val();
    var ds_senha_atual = $('#ds_senha_atual').val();
    var ds_senha_nova  = $('#ds_senha_nova').val();
    var ds_senha_conf  = $('#ds_senha_confirma').val();

    if (etapa === 1) {
        var params = {
            'ac' : 'testar_senha_atual',
            'p0' : id_unidade + "_" + id_servidor,
            'p1' : ds_senha_atual
        };

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : './src/login_dao.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                $('#pmTesteSenha').html( "<p class='label-warning'><strong>" + loading_spinner() + "</strong></p>" );
                $("#ds_senha_nova").complexify({}, function (valid, complexity) {
                    $("#mtSenha").val(complexity);
                    $("#dvSenha").html("  " + complexity);
                });
            },
            // Colocamos o retorno na tela
            success : function(data){
                var retorno = data;
                if ( retorno !== "OK" ) {
                    $('#ac').attr('disabled', true);
                    $('#ds_senha_nova').attr('disabled', true);
                    $('#ds_senha_confirma').attr('disabled', true);
                    $('#pmTesteSenha').html("<p class='label-warning'><strong>" + data + "</strong></p>");
                } else {
                    $('#ac').attr('disabled', false);
                    $('#ds_senha_nova').attr('disabled', false);
                    $('#ds_senha_confirma').attr('disabled', false);
                    $('#pmTesteSenha').html("");
                }
            },
            error: function (request, status, error) {
                $('#pmTesteSenha').html("Erro na chamada da página!<br> (" + status + ")" + request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    } else {
        if (etapa === 2) {
            if (ds_senha_nova === ds_senha_atual) {
                $('#ac').attr('disabled', true);
                $('#pmTesteSenha').html("<p class='label-warning'><strong>A nova senha não pode ser igual a senha atual!</strong></p>");
            } 
            else if ($("#mtSenha").val() < 20) {
                $('#ac').attr('disabled', true);
                $('#pmTesteSenha').html("<p class='label-warning'><strong>Não é permitida senhas com segurança abaixo de 20%!</strong></p>");
            } else {
                $('#ac').attr('disabled', (ds_senha_nova !== ds_senha_conf));
                $('#pmTesteSenha').html("");
            }
        } else {
            if (etapa === 3) {
                ds_senha_conf = documento.value;
                if (ds_senha_nova !== ds_senha_conf) {
                    $('#ac').attr('disabled', true);
                    $('#pmTesteSenha').html("<p class='label-warning'><strong>Nova senha não confere!</strong></p>");
                } 
                else if ($("#mtSenha").val() < 20) {
                    $('#ac').attr('disabled', true);
                    $('#pmTesteSenha').html("<p class='label-warning'><strong>Não é permitida senhas com segurança abaixo de 20%!</strong></p>");
                } else {
                    $('#ac').attr('disabled', false);
                    $('#pmTesteSenha').html("");
                }
            } 
        }
    }
}

function cancelar_relembrar_senha() {
    $('#login-form').removeClass("hide");
    $('#login-forgot').addClass("hide");
}

function cadastrar_primeiro_acesso() {
    $('#pmPrimeiroAcesso').html("");
    
    $('#login-form').addClass("hide");
    $('#login-first').removeClass("hide");
    
    $('#f_nr_matricula').val("");
    $('#f_nr_cpf').val("");
    $('#f_dt_nascimento').val("");
    $('#f_ds_email').val("");
}

function cancelar_primeiro_acesso() {
    $('#login-form').removeClass("hide");
    $('#login-first').addClass("hide");
}
