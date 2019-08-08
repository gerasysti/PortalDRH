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

    session_start();
    $ac       = (empty($_SESSION['acao'])?"":$_SESSION['acao']);
    $titulo   = "Confirmação de Cadastro";
    $servidor = (empty($_SESSION['servidor'])?"":$_SESSION['servidor']['nome']);
    $email    = (empty($_SESSION['servidor'])?"":$_SESSION['servidor']['email']);
    $login    = (empty($_SESSION['servidor'])?"":$_SESSION['servidor']['login']);
    $senha    = (empty($_SESSION['servidor'])?"":$_SESSION['servidor']['senha']);
    $id_link  = "login.php";
    $ds_link  = "Ir para Área Exclusiva do Servidor";
    $mensagem = 
              "Cadastro de senha realizado com "
            . "sucesso para o servidor <strong>'{$servidor}'</strong>. "
            . "Sua senha de acesso será enviada para o e-mail <strong>'{$email}'</strong>.";
    
    if ($ac === 'reenviar_senha') {
        $unidade  = (empty($_SESSION['servidor'])?"":$_SESSION['servidor']['unid']);
        $id_link  = "./login.php?un=" . $unidade;
        $ds_link  = "Ir para Área Exclusiva do Servidor";
        $titulo   = "Envio de Senha";
        $mensagem = 
                  "Geração de uma nova senha realizada com "
                . "sucesso para o servidor <strong>'{$servidor}'</strong>. "
                . "Sua nova senha de acesso será enviada para o e-mail <strong>'{$email}'</strong>.";
    }
    elseif ($ac === 'senha_alterara') {
        $unidade  = (empty($_SESSION['servidor'])?"":$_SESSION['servidor']['unid']);
        $id_link  = "./servidor.php?un=" . $unidade;
        $ds_link  = "Voltar para Área Exclusiva do Servidor";
        $titulo   = "Alteraçao de Senha";
        $mensagem = 
                  "Alteração de senha realizada com "
                . "sucesso para o servidor <strong>'{$servidor}'</strong>. "
                . "Sua nova senha de acesso será enviada para o e-mail <strong>'{$email}'</strong>.";
    }            
    elseif ($ac === 'exibir_senha') {
        $unidade  = (empty($_SESSION['servidor'])?"":$_SESSION['servidor']['unid']);
        $id_link  = "./login.php?un=" . $unidade;
        $ds_link  = "Ir para Área Exclusiva do Servidor";
        $titulo   = "Exibição de Senha";
        $mensagem = 
                  "Geração de uma nova senha realizada com "
                . "sucesso para o servidor <strong>'{$servidor}'</strong>.<br><br>"
                . "Favor anotar seus dados para acesso:<br><br>"
                . "Login: <strong>{$login}</strong><br>"
                . "Senha: <strong>{$senha}</strong>";
    }
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
        <title>Remuneratu$ | DRH Transparência | Confirmação </title>
        
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
                <form class="col-md-4 col-sm-5 col-xs-11 col-lg-3 center-margin">
                    
                    <h3 class="text-center pad25B font-gray text-transform-upr font-size-23">Remuneratu$ <span class="opacity-80">v1.0</span></h3>
                    <div id="login-form" class="content-box bg-default">
                        <div class="content-box-wrapper pad25B20A">
                            <a href="index.php"><img class="mrg25B center-margin radius-all-100 display-block" height="60" src="./dist/img/remuneratus_logo.png" alt=""></a>
                            <div class="content-box">
                                <h3 class="content-box-header bg-primary">
                                    <i class="glyph-icon icon-tasks"></i>
                                    <?php echo $titulo;?>
                                </h3>
                            </div>
                            <div class="content-box-wrapper">
                                <p align='justify'><?php echo $mensagem;?></p>
                            </div>
                            <div class="content-box-wrapper">
                                <a href="<?php echo $id_link;?>" title="Área Exclusiva do Servidor">
                                    <i class="glyph-icon icon-users"></i>
                                    <span><?php echo $ds_link;?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
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