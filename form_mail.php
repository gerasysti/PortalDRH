<!DOCTYPE html>
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once './lib/classes/configuracao.php';
    require_once './lib/Constantes.php';
    require_once './lib/funcoes.php';

?>
<html lang ="en">
    <head>
        <style>
            /* Loading Spinner */
            .spinner{
                margin:0;
                width:70px;
                height:18px;
                margin:-35px 0 0 -9px;
                position:absolute;
                top:50%;
                left:50%;
                text-align:center
            }
            .spinner > div{
                width:18px;
                height:18px;
                background-color:#333;
                border-radius:100%;
                display:inline-block;
                -webkit-animation:bouncedelay 1.4s infinite ease-in-out;
                animation:bouncedelay 1.4s infinite ease-in-out;
                -webkit-animation-fill-mode:both;
                animation-fill-mode:both
            }
            .spinner .bounce1{
                -webkit-animation-delay:-.32s;
                animation-delay:-.32s
            }
            .spinner .bounce2{
                -webkit-animation-delay:-.16s;
                animation-delay:-.16s
            }
            @-webkit-keyframes bouncedelay{
                0%,80%,100%{
                    -webkit-transform:scale(0.0)
                }40%{
                    -webkit-transform:scale(1.0)
                }
            }
            @keyframes bouncedelay{
                0%,80%,100%{
                    transform:scale(0.0);
                    -webkit-transform:scale(0.0)
                }40%{
                    transform:scale(1.0);
                    -webkit-transform:scale(1.0)
                }
            }
        </style>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <title>Remuneratu$ | DRH Transparência | Form Mail</title>
        
        <?php
            include './page_head_links.php';
        ?>
    </head>
    <body>
        <style type="text/css">
            html,body {
                height: 100%;
                background: #fff;
            }
        </style>

        <div id="loading">
            <div class="spinner">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>

        <div class="center-vertical">
            <div class="center-content row">
                
                <form method="post" action="http://www18.locaweb.com.br/scripts/FormMail.pl" id="login-validation" class="col-md-4 col-sm-5 col-xs-11 col-lg-3 center-margin" target="_self">
                    <input type="hidden" name="email" value="notificacao@drhtransparencia.com.br"/>
                    <input type="hidden" name="recipient" value="notificacao@drhtransparencia.com.br"/>
                    <input type="hidden" name="subject" value="Form Mail Locaweb"/>
                    <input type="hidden" name="redirect" value="http://lwwebinar.com.br/obrigado.html"/>
                    
                    <table width="30%" border="1">
                        <tr>
                            <td heigth="19" width="25%">Nome:</td>
                            <td heigth="19" width="75%"><input type="text" name="nome" size="40" value=""></td>
                        </tr>
                        <tr>
                            <td heigth="19" width="25%">E-mail:</td>
                            <td heigth="19" width="75%"><input type="text" name="replyto" size="40" value=""></td>
                        </tr>
                        <tr>
                            <td heigth="19" width="25%">Telefone:</td>
                            <td heigth="19" width="75%"><input type="text" name="telefone" size="40" value=""></td>
                        </tr>
                        <tr>
                            <td heigth="19" width="25%">Mensagem:</td>
                            <td heigth="19" width="75%"><textarea name="mensagem"></textarea></td>
                        </tr>
                        <tr>
                            <td heigth="19" width="25%">&nbsp;</td>
                            <td heigth="19" width="75%"><input type="submit" name="submit" value="Enviar Dados"></td>
                        </tr>
                    </table>
                </form>
                
            </div>
        </div>

        <?php
            include './page_script_gerais.php';
        ?>

        <script type="text/javascript">
            $(window).load(function(){
                setTimeout(function() {
                    $('#loading').fadeOut( 400, "linear" );
                }, 300);
            });
        </script>        
    </body>
</html>