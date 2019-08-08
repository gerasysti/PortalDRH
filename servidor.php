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

    $unidade     = "0";
    $md5_unidade = md5("0000000");
    $des_unidade = "Título da Unidade no Portal";
    $inf_unidade = "Informações da Unidade";
    
    if (isset($_REQUEST['un'])) {
        $md5_unidade = trim($_REQUEST['un']); 
    } else {
        header('location: ./login.php');
        exit;
    }
    
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 
         "Select "
        ."    u.id "
        ."  , u.nome "
        ."  , u.cnpj "
        ."  , u.municipio_nome "
        ."  , u.municipio_uf "
        ."  , trim(coalesce(nullif(trim(u.titulo_portal), ''), trim(u.nome))) as titulo_portal "
        ."  , coalesce(u.ender_lograd, '...') as endereco "
        ."  , coalesce(u.ender_num,    '...') as numero "
        ."  , coalesce(u.ender_bairro, '...') as bairro "
        ."from ADM_CLIENTE u "
        ."order by "
        ."    trim(coalesce(nullif(trim(u.titulo_portal), ''), trim(u.nome)))";

    $res = $pdo->query($sql);
    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $id = md5($obj->id);
        if ( $id === $md5_unidade ) {
            $unidade     = $obj->id;
            $des_unidade = $obj->titulo_portal;
            $inf_unidade = "CNPJ: " . formatarTexto('##.###.###/####-##', $obj->cnpj);
            $endereco    = $obj->endereco . ", " . $obj->numero . ", " . $obj->bairro;
            break;
        }
    }
    
    if ($unidade === "0") {
        header('location: ./login.php');
        exit;
    }
    
    session_start();
    
    $id_servidor = "0";
    $nm_servidor = "";
    $pw_servidor = "";
    $cp_servidor = "";
    $ad_servidor = "";
    
    if ( isset($_SESSION['unidade']) ) {
        $id_servidor = $_SESSION['unidade']['us'];
        $pw_servidor = $_SESSION['unidade']['pw'];
    } else {
        header('location: ./login.php');
        exit;
    }
    
    $sql = 
          "Select "
        . "    s.id_cliente  "
        . "  , s.id_servidor "
        . "  , s.matricula "
        . "  , s.nome "
        . "  , s.sexo "
        . "  , s.cpf "
        . "  , s.dt_nascimento "
        . "  , s.dt_admissao   "
        . "  , s.nivel_acesso "
        . "from REMUN_SERVIDOR s "
        . "where s.id_cliente  = {$unidade} "
        . "  and s.id_servidor = {$id_servidor} ";

    $res = $pdo->query($sql);
    if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $nm_servidor = trim($obj->nome);
        $cp_servidor = "CPF: " . formatarTexto('###.###.###-##', $obj->cpf);
        $ad_servidor = "Admissão: " . date('d/m/Y', strtotime($obj->dt_admissao));
    }
    
?>
<html ng-app="monarchApp" lang="en">
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
            
            .loading-spinner, .loading-stick {
                float: left;
                margin-top: 5px;
            }    
            
            .td_align_right  { text-align: right; }
            .td_align_left   { text-align: left; }
            .td_align_center { text-align: center; }
            .td_align_justify{ text-align: justify; }
        </style>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <title>Remuneratu$ | DRH Transparência | Portal do Servidor </title>

        <?php
            include './page_head_links.php';
        ?>
   </head>

    <body ng-controller="indexController">

        <div id="page-wrapper">
            <div id="page-header" class="bg-gradient-9">
                <div id="mobile-navigation">
                    <button id="nav-toggle" class="collapsed" data-toggle="collapse" data-target="#page-sidebar"><span></span></button>
                    <a href="servidor.php?un=<?php echo $md5_unidade;?>" class="logo-content-small" title="Controle de Remunerações"></a>
                    <!--<a href="#" class="logo-content-small" title="Controle de Remunerações" onclick="home()"></a>-->
                </div>
                
                <div id="header-logo" class="logo-bg">
                    <a href="servidor.php?un=<?php echo $md5_unidade;?>" class="logo-content-big" title="Controle de Remunerações">
                    <!--<a href="#" class="logo-content-big" title="Controle de Remunerações" onclick="home()">-->    
                        Remuneratu<i>$</i>
                        <span>A solução perfeita para controle da Folha</span>
                    </a>
                    <a href="servidor.php?un=<?php echo $md5_unidade;?>" class="logo-content-small" title="Controle de Remunerações">
                    <!--<a href="#" class="logo-content-small" title="Controle de Remunerações" onclick="home()">-->
                        Remuneratu<i>$</i>
                        <span>A solução perfeita para controle da Folha</span>
                    </a>
                    <a id="close-sidebar" href="#" title="Exibir/Ocultar menu vertical">
                        <i class="glyph-icon icon-angle-left"></i>
                    </a>
                </div>
                
                <div id="header-nav-right">
                    <a href="#" class="hdr-btn" id="fullscreen-btn" title="Tela Cheia">
                        <i class="glyph-icon icon-arrows-alt"></i>
                    </a>
                    <!--<a class="header-btn" id="logout-btn" href="index.php" title="Sair">-->
                    <a class="header-btn" id="logout-btn" href="principal.php?un=<?php echo $md5_unidade;?>" title="Sair">    
                        <i class="glyph-icon icon-power-off"></i>
                    </a>
                </div><!-- #header-nav-right -->

            </div>
            
            <div id="page-sidebar">
                <div class="scroll-sidebar">

                    <ul id="sidebar-menu">
                        <li class="header"><span><?php echo $des_unidade;?></span></li>
                        <li>
                            <a href="javascript:void(0);" title="Página Inicial" onclick="home_servidor()">
                                <i class="glyph-icon icon-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" title="Folha de Pagamento">
                                <i class="glyph-icon icon-linecons-wallet"></i>
                                <span>Serviços</span>
                            </a>
                            <div class="sidebar-submenu">

                                <ul>
                                    <li><a href="javascript:void(0);" title="Alterar Senha de Acesso" onclick="alterarSenha()"><span>Alterar Senha de Acesso</span></a></li>
                                    <li><a href="javascript:void(0);" title="Contra-Cheque" onclick="contracheque('<?php echo 'unidade_' . $unidade?>', '<?php echo 'servidor_' . $id_servidor . '_' . preg_replace("/[^0-9]/", "", $cp_servidor)?>')"><span>Contra-Cheque</span></a></li>
                                    <li><a href="javascript:void(0);" title="Ficha Financeira" onclick="ficha_financeira_servidor('<?php echo 'unidade_' . $unidade?>', '<?php echo 'servidor_' . $id_servidor . '_' . preg_replace("/[^0-9]/", "", $cp_servidor)?>')"><span>Ficha Financeira</span></a></li>
                                    <!--<li><a href="javascript:void(0);" title="Comprovante de Rendimentos" onclick="rendimentos_irpf('<?php echo 'unidade_' . $unidade?>', '<?php echo 'servidor_' . $id_servidor . '_' . preg_replace("/[^0-9]/", "", $cp_servidor)?>')"><span>Comprovante de Rendimentos</span></a></li>-->
                                </ul>

                            </div><!-- .sidebar-submenu -->
                        </li>
                        <li>
                            <a href="principal.php?un=<?php echo $md5_unidade;?>" title="Sair">
                                <i class="glyph-icon icon-power-off"></i>
                                <span>Sair</span>
                            </a>
                        </li>
                    </ul><!-- #sidebar-menu -->
                </div>
            </div>

            <div id="descktop">
                <div id="page-content">
                    <div class="col-md-12">
                        <div id="page-title">
                            <input type="hidden" id="cp_funcionario" value="<?php echo preg_replace("/[^0-9]/", "", $cp_servidor);?>">
                            
                            <h2><strong><?php echo $des_unidade;?></strong></h2>
                            <p><strong><?php echo $inf_unidade;?></strong></p>
                            <br>
                            <h2><strong><?php echo $nm_servidor;?></strong></h2>
                            <p><strong><?php echo $cp_servidor;?></strong></p>
                            <p><strong><?php echo $ad_servidor;?></strong></p>
                        </div>
                        <div id="page-wait">
                            <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait">

                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
            include './page_script_gerais.php';
        ?>
        
        <script type="text/javascript" src="./principal.js"></script>
        <script type="text/javascript" src="./servidor.js"></script>
        <script type="text/javascript" src="./login.js"></script>
        <script type="text/javascript" src="./lib/funcoes.js"></script>
        <script type="text/javascript" src="./lib/jquery.complexify.js"></script>
        <script type="text/javascript" src="./src/contra_cheque.js"></script>
        <script type="text/javascript" src="./src/ficha_financeira_servidor.js"></script>
        <script type="text/javascript" src="./src/rendimentos_irpf.js"></script>
        
        <script type="text/javascript">
            body_sizer_servidor();
            setNomeUnidade ('<?php echo $des_unidade;?>');
            setCnpjUnidade ('<?php echo $inf_unidade;?>');
            setNomeServidor('<?php echo $nm_servidor;?>');
            setCpfServidor ('<?php echo $cp_servidor;?>');
            setAdmissaoServidor('<?php echo $ad_servidor;?>');
        </script>

		<!-- BEGIN JIVOSITE CODE -->
                <!--
		<script type='text/javascript'>
			(function(){ 
				var widget_id = 'jqdHWpEE09';
				var d=document;
				var w=window;
				
				function l(){ 
					var s = document.createElement('script'); 
					
					s.type = 'text/javascript'; 
					s.async = true; 
					s.src = '//code.jivosite.com/script/widget/'+widget_id; 
					
					var ss = document.getElementsByTagName('script')[0]; 
					ss.parentNode.insertBefore(s, ss);
				} if (d.readyState=='complete'){
					l();
				} else {
					if (w.attachEvent){ 
						w.attachEvent('onload',l);
					} else {
						w.addEventListener('load',l,false);
					}
				}
			})();
		</script>
                -->
		<!-- END JIVOSITE CODE -->								
    </body>
</html>