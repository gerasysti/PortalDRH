<!DOCTYPE html>
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 
 Exemplos de SIMPLE TOUR
 1. https://alvaroveliz.github.io/aSimpleTour/
 2. https://linkedin.github.io/hopscotch/
 3. https://clu3.github.io/bootstro.js/#
 4. https://tracelytics.github.io/pageguide/
 
 */
    require_once './lib/classes/configuracao.php';
    require_once './lib/Constantes.php';
    require_once './lib/funcoes.php';

    session_start();
    session_unset();
    session_destroy();
    
    $unidade     = "0";
    $md5_unidade = md5("0000000");
    $des_unidade = "Título da Unidade no Portal";
    $inf_unidade = "Informações da Unidade";
    
    $exibe_lista_servidor = 0;
    
    if (isset($_REQUEST['un'])) {
        $md5_unidade = trim($_REQUEST['un']); 
    } else {
        header('location: ./index.php');
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
        ."  , coalesce(u.exibe_lista, '0') as exibe_lista_servidor "
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
            $exibe_lista_servidor = (int)$obj->exibe_lista_servidor;
            break;
        }
    }
    
    if ($unidade === "0") {
        header('location: ./index.php');
        exit;
    }
    
    $titulo_pagina = removerAcentos($des_unidade);
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
            
            .centralizar-vertical {
                vertical-align: middle;
            }
            .custom-font-size-10 {
                font-size: 10px;
            }
            .custom-font-size-12 {
                font-size: 12px;
            }
            /* Centralizar na verticação as células de tabelas renderizadas pela classe "dataTable()"*/
            table.dataTable tbody td {
                vertical-align: middle;
            }            
        </style>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <title>Remuneratu$ | DRH Transparência | <?php echo $titulo_pagina;?></title>

        <?php
            include './page_head_links.php';
        ?>
   </head>

    <body ng-controller="indexController">

        <div id="page-wrapper">
            <div id="page-header" class="bg-gradient-9">
                <input type="hidden" id="id_cliente" value="<?php echo $unidade;?>">
                <div id="mobile-navigation">
                    <button id="nav-toggle" class="collapsed" data-toggle="collapse" data-target="#page-sidebar"><span></span></button>
                    <a href="principal.php?un=<?php echo $md5_unidade;?>" class="logo-content-small" title="Controle de Remunerações"></a>
                    <!--<a href="#" class="logo-content-small" title="Controle de Remunerações" onclick="home()"></a>-->
                </div>
                
                <div id="header-logo" class="logo-bg">
                    <a href="principal.php?un=<?php echo $md5_unidade;?>" class="logo-content-big" title="Controle de Remunerações">
                    <!--<a href="#" class="logo-content-big" title="Controle de Remunerações" onclick="home()">-->    
                        Remuneratu<i>$</i>
                        <span>A solução perfeita para controle da Folha</span>
                    </a>
                    <a href="principal.php?un=<?php echo $md5_unidade;?>" class="logo-content-small" title="Controle de Remunerações">
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
                    <a class="header-btn" id="logout-btn" href="index.php" title="Sair">
                        <i class="glyph-icon icon-power-off"></i>
                    </a>
                </div><!-- #header-nav-right -->

            </div>
            
            <div id="page-sidebar">
                <div class="scroll-sidebar no-print">

                    <ul id="sidebar-menu">
<!--                        <li class="header"><span>Administração</span></li>
                        <li>
                            <a href="#index" title="Cadastro de Clientes">
                                <i class="glyph-icon icon-linecons-tv"></i>
                                <span>Cadastro de Clientes</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        -->
                        <li class="header"><span><?php echo $unidade . " - " . $des_unidade;?></span></li>
                        <li>
                            <a href="javascript:void(0);" title="Página Inicial" onclick="home()">
                                <i class="glyph-icon icon-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" title="Lei de Acesso a Informação" onclick="lei_acesso_informacao()">
                                <i class="glyph-icon icon-legal"></i>
                                <span>Lei de Acesso a Informação</span>
                            </a>
                        </li>
                        <li>
                            <a href="login.php?un=<?php echo $md5_unidade;?>" title="Área Exclusiva do Servidor">
                                <i class="glyph-icon icon-users"></i>
                                <span>Área Exclusiva do Servidor</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="javascript:void(0);" title="Tabela de Cargos/Funções e Salários" onclick="cargos_salarios('<?php echo 'unidade_' . $unidade?>')">
                                <i class="glyph-icon icon-usd"></i>
                                <span>Tabela de Cargos e Salários</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" title="Despesas com Folha de Pagamento">
                                <i class="glyph-icon icon-money"></i>
                                <span>Despesas com Folha</span>
                            </a>
                            <div class="sidebar-submenu">

                                <ul>
                                    <li><a href="javascript:void(0);" title="Vínculos" onclick="vinculos('<?php echo 'unidade_' . $unidade?>')"><span>Por Vínculos</span></a></li>
                                    <li><a href="javascript:void(0);" title="Cargos / Funções" onclick="cargos('<?php echo 'unidade_' . $unidade?>')"><span>Por Cargos / Funções</span></a></li>
                                    <?php if ($exibe_lista_servidor === 1): ?>
                                    <li><a href="javascript:void(0);" title="Servidores" onclick="servidores('<?php echo 'unidade_' . $unidade?>')"><span>Por Servidores</span></a></li>
                                    <?php // else: ?>
                                    <?php endif; ?>
                                </ul>

                            </div><!-- .sidebar-submenu -->
                        </li>
                        <li>
                            <a href="index.php" title="Página Inicial">
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
                            <h2><strong><?php echo $des_unidade;?></strong></h2>
                            <p><strong><?php echo $inf_unidade;?></strong></p>
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
        <script type="text/javascript" src="./src/cargo_salario.js"></script>
        <script type="text/javascript" src="./src/remuneracao_vinculo.js"></script>
        <script type="text/javascript" src="./src/remuneracao_cargo.js"></script>
        <script type="text/javascript" src="./src/remuneracao_servidor.js"></script>
        
        <script type="text/javascript">
            setNomeUnidade('<?php echo $des_unidade;?>');
            setCnpjUnidade('<?php echo $inf_unidade;?>');
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