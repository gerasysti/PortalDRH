<!DOCTYPE html>
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';

    $ac  = "";
    $hs  = "";
    $id  = date('d/m/Y');
    $msg = "";
    
    if (isset($_REQUEST['un'])) {
        $hs = trim($_REQUEST['un']); 
    }
    
    if (isset($_GET['ac'])) {
        $ac = $_GET["ac"];
        if ($ac === 'error_login') {
            $msg = "Usuário e/ou senha inválido!";
        }
        elseif ($ac === 'login_empty') {
            $msg = "Dados insuficientes para autenticação!";
        }
        elseif ($ac === 'invalide') {
            $msg = "E-mail inválido para login!";
        }
        elseif ($ac === 'remember_empty') {
            $msg = "Dados insuficientes para recuperação de senha!";
        }
        elseif ($ac === 'incorrect_data') {
            $msg = "<strong>Dados incorretos</strong> para recuperação de senha!";
        }
        elseif ($ac === 'clear') {
            error_reporting(E_NOTICE); 
            ini_set("display_errors", 0);
            session_start();
            
            $hash = (!isset($_SESSION['acesso'])?md5("Erro"):(!isset($_SESSION['acesso']['id'])?md5("Erro"):$_SESSION['acesso']['id']));
            excluirArquivosJson("../downloads/", $hash);
        }
    }
    
    session_start();
    session_unset();
    session_destroy();
    
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
        <title>Remuneratu$ | Administração | Efetuar Login</title>
        <link rel="shortcut icon" href="../gerasys.ico" >
        
        <?php
            $page_head_links = file_get_contents("../page_head_links.php");
            echo str_replace("./", "../", $page_head_links);
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
                
                <form action="./index_dao.php" id="login-validation" class="col-md-4 col-sm-5 col-xs-11 col-lg-3 center-margin" method="post" target="_self">
                    <input type="hidden" id="hash" name="hash" value="<?php echo md5($id);?>" form="login-validation"/>
                    <h3 class="text-center pad25B font-gray text-transform-upr font-size-23">Remuneratu$ <span class="opacity-80">v2.0</span></h3>
                    <div id="login-form" class="content-box bg-default <?php echo ($ac === 'remember_empty' || $ac === 'incorrect_data'?"hide":"");?>">
                        <div class="content-box-wrapper pad20A">
                            <a href="<?php echo ($hs === ""?"index.php":"controle.php?id=" . $hs);?>"><img class="mrg25B center-margin radius-all-100 display-block" height="60" src="../dist/img/remuneratus_logo.png" alt=""></a>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="id_login" name="id_login" placeholder="Login" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-unlock-alt"></i>
                                    </span>
                                    <input type="password" class="form-control" id="ds_senha" name="ds_senha" placeholder="Senha" required>
                                </div>
                            </div>
                            
                            <?php
                                if ( ($ac !== 'remember_empty') && ($ac !== 'incorrect_data') && ($msg !== "") ) {
                                    echo '<div class="help-block text-center">';
                                    echo '  <p class="label-warning">'.$msg.'</p>';
                                    echo '</div><br>';
                                }
                            ?>
                            <div class="form-group">
                                <button type="submit" class="btn btn-block btn-primary" id="ac" name="ac" value="login">Entrar</button>
                            </div>
                        </div>
                    </div>

                </form>
<!--
                <form action="./src/login_dao.php" id="login-remember" class="col-md-4 col-sm-5 col-xs-11 col-lg-3 center-margin" method="post" target="_self">
                    <input type="hidden" id="r_hash" name="r_hash" value="<?php // echo ($un !== ""?"?un=" . md5($un):"");?>" form="login-remember"/>
                    <div id="login-forgot" class="content-box bg-default <?php // echo ($ac !== 'remember_empty' && $ac !== 'incorrect_data'?"hide":"");?>">
                        <div class="content-box">
                            <h3 class="content-box-header bg-primary">
                                <i class="glyph-icon icon-key"></i>
                                Cadastrar Senha
                            </h3>
                        </div>
                        
                        <div class="content-box-wrapper pad20A">

                            <div class="form-group">
                                <label for="r_id_cliente">Unidade / Órgão:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-map-marker"></i>
                                    </span>
                                    <?php
//                                        if ($un !== "") {
//                                            echo "<input type='hidden' id='r_id_cliente' name='r_id_cliente' value='{$un}' form='login-remember'/>";
//                                        }
                                    ?>
                                    <select class="form-control chosen-select" <?php // echo ($un === ""?"id='r_id_cliente' name='r_id_cliente'":"");?> required form="login-remember" <?php echo ($un !== ""?"disabled":"");?>>
                                        <option value='0'>Selecionar a Unidade / Órgão</option>
                                        <?php
//                                            echo $lista_unidades;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="r_nr_matricula">Matrícula:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-archive"></i>
                                    </span>
                                    <input type="text" class="form-control" id="r_nr_matricula" name="r_nr_matricula" placeholder="Número da Matrícula" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="r_nr_cpf">CPF:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-barcode"></i>
                                    </span>
                                    <input type="text" class="form-control" id="r_nr_cpf" name="r_nr_cpf" placeholder="Número do CPF" required maxlength="14" onkeypress="return formatar_numero('###.###.###-##', this, event)">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="r_dt_nascimento">Data de Nascimento:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-calendar"></i>
                                    </span>
                                    <input type="date" class="form-control" id="r_dt_nascimento" name="r_dt_nascimento" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="ds_email">E-mail:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="r_ds_email" name="r_ds_email" placeholder="Entre com seu e-mail" required>
                                </div>
                            </div>
                            
                            <div class="help-block text-center" id="pmResuperarSenha">
                                <p class="label-warning">Teste</p>
                                <?php
//                                    if ( ($ac === 'remember_empty' || $ac === 'incorrect_data') && ($msg !== "") ) {
//                                        echo '  <p class="label-warning">'.$msg.'</p>';
//                                    }
                                ?>
                            </div>
                            
                        </div>
                        
                        <div class="button-pane text-center">
                            <button type="submit" class="btn btn-md btn-primary" id="ac" name="ac" value="login_lembrar">Recuperar Senha</button>
                            <button type="submit" class="btn btn-md btn-primary" id="ac" name="ac" value="login_lembrar">Gerar Senha</button>
                            <a href="javascript:void(0);" class="btn btn-md btn-link switch-button" title="Cancelar" onclick="cancelar_relembrar_senha()">Cancelar</a>
                        </div>
                        
                    </div>

                </form>

                <form action="./src/login_dao.php" id="login-validation-first" class="col-md-4 col-sm-5 col-xs-11 col-lg-3 center-margin" method="post" target="_self">
                    <input type="hidden" id="f_hash" name="f_hash" value="<?php // echo ($un !== ""?"?un=" . md5($un):"");?>" form="login-validation-first"/>
                    <div id="login-first" class="content-box bg-default hide">
                        <div class="content-box">
                            <h3 class="content-box-header bg-primary">
                                <i class="glyph-icon icon-user"></i>
                                Primeiro Acesso
                            </h3>
                        </div>
                        
                        <div class="content-box-wrapper pad20A">

                            <a href="index.php"><img class="mrg25B center-margin radius-all-100 display-block" height="60" src="../dist/img/remuneratus_logo.png" alt=""></a>
                            <div class="form-group">
                                <label for="f_id_cliente">Unidade / Órgão:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-map-marker"></i>
                                    </span>
                                    <?php
//                                        if ($un !== "") {
//                                            echo "<input type='hidden' id='f_id_cliente' name='f_id_cliente' value='{$un}' form='login-validation-first'/>";
//                                        }
                                    ?>
                                    <select class="form-control chosen-select" <?php // echo ($un === ""?"id='f_id_cliente' name='f_id_cliente'":"");?> required form="login-validation-first" <?php echo ($un !== ""?"disabled":"");?>>
                                        <option value=''>Selecionar a Unidade / Órgão</option>
                                        <?php
//                                            echo $lista_unidades;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="f_nr_matricula">Matrícula:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-archive"></i>
                                    </span>
                                    <input type="text" class="form-control" id="f_nr_matricula" name="f_nr_matricula" placeholder="Número da Matrícula" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="f_nr_cpf">CPF:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-barcode"></i>
                                    </span>
                                    <input type="text" class="form-control" id="f_nr_cpf" name="f_nr_cpf" placeholder="Número do CPF" required maxlength="14" onkeypress="return formatar_numero('###.###.###-##', this, event)">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="f_dt_nascimento">Data de Nascimento:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-calendar"></i>
                                    </span>
                                    <input type="date" class="form-control" id="f_dt_nascimento" name="f_dt_nascimento" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="f_ds_email">E-mail:</label>
                                <div class="input-group">
                                    <span class="input-group-addon add-on bg-gray">
                                        <i class="glyph-icon icon-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="f_ds_email" name="f_ds_email" placeholder="Entre com seu e-mail" required>
                                </div>
                            </div>
                            
                            <div class="help-block text-center" id="pmPrimeiroAcesso">
                                <p class="label-warning">Teste</p>
                            </div>
                            
                        </div>
                        
                        <div class="button-pane text-center">
                            <button type="submit" class="btn btn-md btn-primary" id="ac" name="ac" value="login_cadastrar">Cadastrar</button>
                            <a href="javascript:void(0);" class="btn btn-md btn-primary switch-button" title="Cancelar" onclick="cancelar_primeiro_acesso()">Cancelar</a>
                        </div>
                        
                    </div>
                    
                </form>
                -->
            </div>
        </div>

        <?php
            $page_script_gerais = file_get_contents("../page_script_gerais.php");
            echo str_replace("./", "../", $page_script_gerais);
        ?>

        <script type="text/javascript" src="../login.js"></script>
        <script type="text/javascript" src="../lib/funcoes.js"></script>
        
        <script type="text/javascript">
            $(window).load(function(){
                setTimeout(function() {
                    $('#loading').fadeOut( 400, "linear" );
                }, 300);
            });
        </script>        
    </body>
</html>