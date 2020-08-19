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
    session_unset();
    session_destroy();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Remuneratu$ | DRH Transparência</title>
        <link rel="shortcut icon" href="gerasys.ico" >
         <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.4 -->
        <link href="./assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- WIDGETS -->
        <link href="./assets/widgets/carousel/carousel.css" rel="stylesheet" type="text/css">
        <!-- Font Awesome Icons -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
<!--        <link href="./plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />-->
        <!-- DATA TABLES -->
<!--        <link href="./plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />-->
        <!-- iCheck for checkboxes and radio inputs -->
<!--        <link href="./plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
        <link href="./plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />-->
        <!-- Bootstrap Color Picker -->
<!--        <link href="./plugins/colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />-->
        <!-- Bootstrap time Picker -->
<!--        <link href="./plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />-->
        <!-- Select2 -->
<!--        <link href="./plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />-->

        <!-- Theme style -->
        <link href="./dist/css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <link href="./dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link href="./dist/css/skins/_all-skins.css" rel="stylesheet" type="text/css" />
        <link href="./dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
        
        <!-- JS Core -->

        <script type="text/javascript" src="./assets/js-core/jquery-core.js"></script>
        <script type="text/javascript" src="./assets/js-core/jquery-ui-core.js"></script>
        <script type="text/javascript" src="./assets/js-core/jquery-ui-widget.js"></script>
        <script type="text/javascript" src="./assets/js-core/jquery-ui-mouse.js"></script>
        <script type="text/javascript" src="./assets/js-core/jquery-ui-position.js"></script>
        <script type="text/javascript" src="./assets/js-core/transition.js"></script>
        <script type="text/javascript" src="./assets/js-core/modernizr.js"></script>
        <script type="text/javascript" src="./assets/js-core/jquery-cookie.js"></script>
        
        <script src="http://code.angularjs.org/1.2.13/angular.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular-route.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular-animate.js"></script>
        <script type="text/javascript" src="./assets/angular/app.js"></script>
        
        <link rel="stylesheet" type="text/css" href="./assets/angular/page-animations.css">
    </head>
    <body>
        <style type="text/css" media="screen">
            .jumbotron{
                background-color:#C6DA3F;
            }
            #selecinar_orgao .nav-pills > li.active > a {
                background-color:#1D4619;
            }
            #selecinar_orgao .nav-pills > li.active > a:hover, nav .nav-pills > li > a:hover {
                background-color:#C6DA3F;
            }
            footer {
                padding:5px 0;
                bottom: 0px;
                position:fixed;
                width: 100%;
                background-color:#000;
                color:#CECECE;
            }
        </style>
        
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                
                <div class="navbar-header">
<!--                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-menu" title="Selecionar Unidade/Órgão">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>-->
                    <a class="navbar-header" href="index.php">
                        <img src="./dist/img/remuneratus_logo.png" height="60" alt="DRH Transparencia">
                    </a>
                </div>
                
<!--                <div class="collapse navbar-collapse navbar-right" id="navbar-collapse-menu" style="padding:1% 0;">
                    <ul class="nav nav-pills" id="menuTop">
                        <li class="dropdown" id="menuUnidade">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" onclick="myDropdownUnidade()" id="selecinar_orgao">
                                <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
                                SELECIONAR UNIDADE / ÓRGÃO <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" id="myDropdown">
                                <li>
                                    <div class="box input-group">
                                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                        <input class="form-control" type="text" placeholder="" id="myInput" onkeyup="filterFunction()">
                                    </div>
                                </li>
                                <?php
//                                    $cnf = Configuracao::getInstancia();
//                                    $pdo = $cnf->db('', '');
//                                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//                                    $sql = 
//                                         "Select "
//                                        ."    u.id "
//                                        ."  , u.nome "
//                                        ."  , u.cnpj "
//                                        ."  , u.municipio_nome "
//                                        ."  , u.municipio_uf "
//                                        ."  , trim(coalesce(u.titulo_portal, u.nome)) as titulo_portal "
//                                        ."from ADM_CLIENTE u "
//                                        ."order by "
//                                        ."    trim(coalesce(u.titulo_portal, u.nome))";
//
//                                    $res = $pdo->query($sql);
//                                    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
//                                        $id = md5($obj->id);
//                                        echo "<li><a href='principal.php?un={$id}' id='unidade_{$obj->id}'>{$obj->titulo_portal}</a></li>";
//                                    }
                                ?>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="login.php">
                                    <span class="glyphicon glyphicon-user"></span> 
                                    ÁREA EXCLUSIVA DO SERVIDOR <b class="caret"></b>
                            </a>
                        </li>
                    </ul>
                    
                    <script>
                        function filterFunction() {
                            var input, filter, ul, li, a, i;
                            input = document.getElementById('myInput');
                            filter = input.value.toUpperCase();
                            div = document.getElementById("myDropdown");
                            a = div.getElementsByTagName('a');
                            for (i = 0; i < a.length; i++) {
                                if (a[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
                                    a[i].style.display = '';
                                } else {
                                    a[i].style.display = 'none';
                                }
                            }
                        }
                    </script>

                </div>-->
                
            </div>
        </nav>
        
        <div class="container">
            <div class="row">
                <!--<div class="col-md-6">-->
                    <div class="panel">
                        <div class="panel-body">
                            <h3 class="title-hero">
                                Bootstrap Slider
                            </h3>
                            <div class="example-box-wrapper">
                                <div class="row">
                                    
                                    <ul class="nav nav-pills" id="menuTop">
                                        <li class="dropdown" id="menuUnidade">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" onclick="myDropdownUnidade()" id="selecinar_orgao">
                                                <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
                                                SELECIONAR UNIDADE / ÓRGÃO <b class="caret"></b>
                                            </a>
                                            <ul class="dropdown-menu" id="myDropdown">
                                                <li>
                                                    <div class="box input-group">
                                                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                                        <input class="form-control" type="text" placeholder="" id="myInput" onkeyup="filterFunction()">
                                                    </div>
                                                </li>
                                                <?php
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
                                                        ."from ADM_CLIENTE u "
                                                        ."order by "
                                                        ."    trim(coalesce(nullif(trim(u.titulo_portal), ''), trim(u.nome)))";

                                                    $res = $pdo->query($sql);
                                                    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                                        $id = md5($obj->id);
                                                        echo "<li><a href='principal.php?un={$id}' id='unidade_{$obj->id}'>{$obj->titulo_portal}</a></li>";
                                                    }
                                                ?>
                                            </ul>
                                        </li>
                                    </ul>
                                    <script>
                                        function filterFunction() {
                                            var input, filter, ul, li, a, i;
                                            input  = document.getElementById('myInput');
                                            filter = input.value.toUpperCase();
                                            div = document.getElementById("myDropdown");
                                            a = div.getElementsByTagName('a');
                                            for (i = 0; i < a.length; i++) {
                                                if (a[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
                                                    a[i].style.display = '';
                                                } else {
                                                    a[i].style.display = 'none';
                                                }
                                            }
                                        }
                                        
                                        function myDropdownUnidade() {
                                            
                                        }
                                    </script>
                                    
                                </div>
                                
                                <div class="row"></div>
                                <div class="row"></div>
                                
                                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                                        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                                    </ol>
                                    <div class="carousel-inner">
                                        <div class="item">
                                            <img src="./dist/img/home-carousel-acessoinformacao.png" alt="First slide">
                                        </div>
                                        <div class="item">
                                            <img src="./dist/img/home-carousel-gerasysti.png" alt="Second slide">
                                        </div>
                                        <div class="item active">
                                            <img src="./dist/img/home-carousel-mcruz.png" alt="Third slide">
                                        </div>
                                    </div>
                                    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                                        <span class="glyph-icon icon-chevron-left"></span>
                                    </a>
                                    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                                        <span class="glyph-icon icon-chevron-right"></span>
                                    </a>
                                </div>
								
                            </div>
                        </div>
                    </div>
                <!--</div>-->

            </div>
        </div>
        
        
	<div class="container">
            <div class="row" style="margin-bottom:80px;">
                <div class="col-sm-8">
                    <h2>Sobre a Transparência</h2>
                    <p>
                            Disponibilizar informações sobre a LEI Nº 12.527, DE 18 DE NOVEMBRO DE 2011.<br />
                                            <strong>(Lei de acesso à informação).</strong>
                                    </p>
                                    <p>Disponibilizar o Contra-Cheque on-line do Servidor Municipal.</p>
                </div>
                <div class="col-sm-4">
                    <h2>Contato</h2>
                    <address>
                        <strong><a href="#">GERASYS.TI</a></strong>
                        <br>Rua Scilla Médice, 455
                        <br>Centro - Rondon do Pará - PA
                        <br>
                    </address>
                    <address>
                        <abbr title="Phone">Fone </abbr>(94) 3326 2160<br>
                        <abbr title="Phone">Fone </abbr>(94) 98119 4915<br>
                        <abbr title="Phone">Fone </abbr>(94) 99129 1158<br>
                        <abbr title="Email">Email </abbr> <a href="mailto:#">gerasys.ti@hotmail.com</a>
                    </address>
                    <!--<div id="fb-root"></div>-->
                    <!--<div class="fb-send" data-href="https://developers.facebook.com/docs/plugins/"></div>-->
                </div>
            </div>
            <!-- /.row -->
	</div>
        
        <footer>
            <div class="container">
                <p class="text-left col-md-12">© 2015-2020. GeraSys TI. Todos os direitos reservados. | Desenvolvido pela <a href="mailto:isaque.ribeiro@agilsoftwares.com.br">Ágil Soluções em Softwares</a></p>
            </div>
        </footer>
        
        <script type="text/javascript" src="./assets/bootstrap/js/bootstrap.js"></script>
        <!-- Slidebars -->
        <script type="text/javascript" src="./assets/widgets/slidebars/slidebars.js"></script>
        <script type="text/javascript" src="./assets/widgets/slidebars/slidebars-demo.js"></script>

        <script type='text/javascript'>
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                
                if (d.getElementById(id)) return;
                
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.10";
                
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));    
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
