<!DOCTYPE html>
<?php
/* Simple Tour (Ajuda Interativa) :
 * 
 * https://alvaroveliz.github.io/aSimpleTour/index.html
 * http://codesells.com/TourTip
 * http://bootstraptour.com/
 * 
 */
    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';

    error_reporting(E_ALL); 
    ini_set("display_errors", 1);
    
    session_start();
    
    $id = md5(date('d/m/Y'));
    
    // Manter nesta variável a função que será chamada para montar os dados da versão atual do sistema...
    // Esta função estará no arquivo "controle.js"
    $version_function = "display_system_version_v202()";
    
    // Manter nesta variável a informação da versão anterior do sistema...
    $version_info =
          "Versão <b>2.0.1</b><br>"
        . "Copyright &copy; 2020 <strong>Gerasys TI / M Cruz Consultoria.</strong><br>"
        . "Todos os direitos reservados. &nbsp;&nbsp;&nbsp;";
    
    if (isset($_REQUEST['id'])) {
        if ($id !== trim($_REQUEST['id'])) {
            header('location: ./index.php');
            exit;
        }
    } else {
        header('location: ./index.php');
        exit;
    }

    if (!isset($_SESSION['acesso'])) {
        header('location: ./index.php');
        exit;
    }
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 
          "Select "
        . "    u.* "
        . "  , coalesce(u.id_cliente, 0) as cliente "
        . "  , coalesce(nullif(trim(c.titulo_portal), ''), c.nome, 'Administração do Sistema') as nome_cliente "
        . "from ADM_USUARIO u "
        . "  left join ADM_CLIENTE c on (c.id = u.id_cliente) "
        . "where u.e_mail = '{$_SESSION['acesso']['us']}'";
        
    $qry = $pdo->query($sql);    
    $dados   = $qry->fetchAll(PDO::FETCH_ASSOC);
    $usuario = null;
    foreach($dados as $item) {
        $usuario = $item;
    }
    
    // Fechar conexão PDO
    unset($qry);
    unset($pdo);
    
//    $cnf = Configuracao::getInstancia();
//    $pdo = $cnf->db('', '');
//    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//    $sql = 
//         "Select "
//        ."    u.id "
//        ."  , u.nome "
//        ."  , u.cnpj "
//        ."  , u.municipio_nome "
//        ."  , u.municipio_uf "
//        ."  , trim(coalesce(u.titulo_portal, u.nome)) as titulo_portal "
//        ."  , coalesce(u.ender_lograd, '...') as endereco "
//        ."  , coalesce(u.ender_num,    '...') as numero "
//        ."  , coalesce(u.ender_bairro, '...') as bairro "
//        ."from ADM_CLIENTE u "
//        ."order by "
//        ."    trim(coalesce(u.titulo_portal, u.nome))";
//
//    $res = $pdo->query($sql);
//    while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
//        $id = md5($obj->id);
//        if ( $id === $md5_unidade ) {
//            $unidade     = $obj->id;
//            $des_unidade = $obj->titulo_portal;
//            $inf_unidade = "CNPJ: " . formatarTexto('##.###.###/####-##', $obj->cnpj);
//            $endereco    = $obj->endereco . ", " . $obj->numero . ", " . $obj->bairro;
//            break;
//        }
//    }
//    
//    
//    session_start();
//    
//    $id_servidor = "0";
//    $nm_servidor = "";
//    $pw_servidor = "";
//    $cp_servidor = "";
//    $ad_servidor = "";
//    
//    if ( isset($_SESSION['unidade']) ) {
//        $id_servidor = $_SESSION['unidade']['us'];
//        $pw_servidor = $_SESSION['unidade']['pw'];
//    } else {
//        header('location: ./login.php');
//        exit;
//    }
//    
//    $sql = 
//          "Select "
//        . "    s.id_cliente  "
//        . "  , s.id_servidor "
//        . "  , s.matricula "
//        . "  , s.nome "
//        . "  , s.sexo "
//        . "  , s.cpf "
//        . "  , s.dt_nascimento "
//        . "  , s.dt_admissao   "
//        . "  , s.nivel_acesso "
//        . "from REMUN_SERVIDOR s "
//        . "where s.id_cliente  = {$unidade} "
//        . "  and s.id_servidor = {$id_servidor} ";
//
//    $res = $pdo->query($sql);
//    if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
//        $nm_servidor = trim($obj->nome);
//        $cp_servidor = "CPF: " . formatarTexto('###.###.###-##', $obj->cpf);
//        $ad_servidor = "Admissão: " . date('d/m/Y', strtotime($obj->dt_admissao));
//    }
//    
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
        <title>Remuneratu$ | Administração </title>
        <link rel="shortcut icon" href="../gerasys.ico" >

        <?php
            $page_head_links = file_get_contents("../page_head_links.php");
            echo str_replace("./", "../", $page_head_links);
        ?>
        <!-- DRH -->
        <link rel="stylesheet" type="text/css" href="./controle.css">
   </head>

    <body ng-controller="indexController">

        <div id="page-wrapper">
            <div id="page-header" class="bg-gradient-9">
                <div id="mobile-navigation">
                    <input type="hidden" id="id_sessao"   value="<?php echo 'id_' . $_SESSION['acesso']['id'];?>">
                    <input type="hidden" id="lg_sessao"   value="<?php echo 'lg_' . $_SESSION['acesso']['us']?>">
                    <input type="hidden" id="administrar" value="<?php echo ((int)$usuario['cliente'] === 0?"1":"0");?>">
                    <button id="nav-toggle" class="collapsed" data-toggle="collapse" data-target="#page-sidebar"><span></span></button>
                    <a href="controle.php?id=<?php echo $id;?>" class="logo-content-small" title="Administração"></a>
                    <!--<a href="#" class="logo-content-small" title="Controle de Remunerações" onclick="home()"></a>-->
                </div>
                
                <div id="header-logo" class="logo-bg">
                    <a href="controle.php?id=<?php echo $id;?>" class="logo-content-big" title="Administração">
                    <!--<a href="#" class="logo-content-big" title="Controle de Remunerações" onclick="home()">-->    
                        Remuneratu<i>$</i>
                        <span>A solução perfeita para controle da Folha</span>
                    </a>
                    <a href="controle.php?id=<?php echo $id;?>" class="logo-content-small" title="Administração">
                    <!--<a href="#" class="logo-content-small" title="Controle de Remunerações" onclick="home()">-->
                        Remuneratu<i>$</i>
                        <span>A solução perfeita para controle da Folha</span>
                    </a>
                    <a id="close-sidebar" href="#" title="Exibir/Ocultar menu vertical">
                        <i class="glyph-icon icon-angle-left"></i>
                    </a>
                </div>
                
                <div id="header-nav-right">
                    <!--
                    <a href="#" class="hdr-btn" id="fullscreen-btn" title="Tela Cheia">
                        <i class="glyph-icon icon-arrows-alt"></i>
                    </a>
                    -->
                    <div class="pull-left" id="system_version" style="color: #FFFFFF;">
                        <?php echo $version_info;?>
                    </div>
                    
                    <a href="javascript:void(0);" class="header-btn" id="toor-btn" title="Ajuda">
                        <i class="glyph-icon icon-question"></i>
                    </a>
                    <!--<a class="header-btn" id="logout-btn" href="index.php" title="Sair">-->
                    <a class="header-btn" id="logout-btn" href="index.php?ac=clear" title="Sair">    
                        <i class="glyph-icon icon-power-off"></i>
                    </a>
                </div><!-- #header-nav-right -->

            </div>
            
            <div id="page-sidebar">
                <div class="scroll-sidebar">

                    <ul id="sidebar-menu">
                        <?php if (intval($_SESSION['acesso']['id_cliente']) === 0):?>
                        <li class="header"><span>Administração do Sistema</span></li>
                        <?php else:?>
                        <li class="header"><span>.:: <?php echo $usuario['nome_cliente'];?> ::.</span></li>
                        <?php endif;?>
                        
                        <li>
                            <a href="javascript:void(0);" title="Página Inicial" onclick="home_controle()">
                                <i class="glyph-icon icon-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <?php if (intval($usuario['administrar_portal']) === 1):?>
                        <li>
                            <a href="javascript:void(0);" title="Controles de Clientes e Usuários">
                                <i class="glyph-icon icon-linecons-wallet"></i>
                                <span>Controles</span>
                            </a>
                            <div class="sidebar-submenu">

                                <ul>
                                    <!--<li><a href="javascript:void(0);" title="Alterar Senha de Acesso" onclick="alterarSenha()"><span>Alterar Senha de Acesso</span></a></li>-->
                                    <li><a href="javascript:void(0);" title="Usuários" onclick="controle_usuario('<?php echo 'id_' . $id?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')"><span>Usuários</span></a></li>
                                    <li><a href="javascript:void(0);" title="Clientes" onclick="controle_cliente('<?php echo 'id_' . $id?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')"><span>Clientes</span></a></li>
                                </ul>

                            </div><!-- .sidebar-submenu -->
                        </li>
                        <?php endif;?>
                        
                        <li class="header base-dados"><span>Base de Dados</span></li>
                        <?php if ( (intval($usuario['lancar_eventos']) === 1) || (intval($usuario['lancar_ch_professores']) === 1) ) :?>
                        <li>
                            <a href="javascript:void(0);" title="Pesquisar Unidades Gestoras" onclick="controle_unidade_gestora('<?php echo 'id_' . $id?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')">
                                <i class="glyph-icon icon-bank"></i>
                                <span>Unidades Gestoras</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" title="Pesquisar Unidades de Lotação" onclick="controle_unidade_lotacao('<?php echo 'id_' . $id?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')">
                                <i class="glyph-icon icon-map-marker"></i>
                                <span>Unidades de Lotação</span>
                            </a>
                        </li>
                        <?php if (intval($usuario['lancar_eventos']) === 1):?>
                        <li>
                            <a href="javascript:void(0);" title="Manutenção da Tabela de Eventos" onclick="controle_tabela_eventos('<?php echo 'id_' . $id?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')">
                                <i class="glyph-icon icon-th"></i>
                                <span>Tabela de Eventos</span>
                            </a>
                        </li>
                        <?php endif;?>
                        <li>
                            <a href="javascript:void(0);" title="Pesquisa ao Cadastro de Servidores" onclick="controle_tabela_servidores('<?php echo 'id_' . $id?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')">
                                <i class="glyph-icon icon-users"></i>
                                <span>Cadastro de Servidores</span>
                            </a>
                        </li>
                        <?php endif;?>
                        
                        <li class="header manutencao-dados"><span>Manutenção de Dados</span></li>
                        <?php if (intval($usuario['lancar_eventos']) === 1):?>
                        <li>
                            <a href="javascript:void(0);" title="Lançamento de Eventos por Servidor" onclick="controle_lancar_eventos_mensais('<?php echo 'id_' . $id?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')">
                                <i class="glyph-icon icon-money"></i>
                                <span>Eventos Mensais</span>
                            </a>
                        </li>
                        <?php endif;?>

                        <?php if (intval($usuario['lancar_ch_professores']) === 1):?>
                        <li>
                            <a href="javascript:void(0);" title="Lançamento de Cargar Horária por Professor" onclick="controle_cargar_horaria_prof('<?php echo 'id_' . $id?>', '<?php echo 'lg_' . $_SESSION['acesso']['us']?>')">
                                <i class="glyph-icon icon-dashboard"></i>
                                <span>Carga Horária Professores</span>
                            </a>
                        </li>
                        <?php endif;?>
                        
                        <li class="divider"></li>
                        <li id="menu-opcao_sair">
                            <a href="index.php?ac=clear" title="Sair">
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
<!--                            <h2><strong><?php // echo $des_unidade;?></strong></h2>
                            <p><strong><?php // echo $inf_unidade;?></strong></p>-->
                            <h2><strong><?php echo "";?></strong></h2>
                            <p><strong> <?php echo "";?></strong></p>
                            <br>
<!--                            <h2><strong><?php // echo $nm_servidor;?></strong></h2>
                            <p><strong><?php // echo $cp_servidor;?></strong></p>
                            <p><strong><?php // echo $ad_servidor;?></strong></p>-->
                            <h2><strong><?php echo "";?></strong></h2>
                            <p><strong><?php  echo "";?></strong></p>
                            <p><strong><?php  echo "";?></strong></p>
                        </div>
                        <div id="page-wait">
                            <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait">

                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="modal fade bs-example-modal-lg box_confirme" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">-->
        <div class="modal fade bs-example-modal box_confirme" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog"><!--modal-lg-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Confirme</h4>
                    </div>
                    <div class="modal-body" id="box_confirme_msg">
                        <p>Large modal content here ...</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="btnF_confirma_msg">Fechar</button>
                        <button type="button" class="btn btn-primary" id="btnC_confirma_msg">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="modal fade bs-example-modal-lg box_alerta" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">-->
        <div class="modal fade bs-example-modal box_informe" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog"><!--modal-lg-->
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Informação</h4>
                    </div>
                    <div class="modal-body" id="box_informe_msg">
                        <p>Large modal content here ...</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="modal fade bs-example-modal-lg box_alerta" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">-->
        <div class="modal fade bs-example-modal box_alerta" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog"><!--modal-lg-->
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Alerta</h4>
                    </div>
                    <div class="modal-body" id="box_alerta_msg">
                        <p>Large modal content here ...</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="modal fade bs-example-modal-lg box_erro" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">-->
        <div class="modal fade bs-example-modal box_erro" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog"><!--modal-lg-->
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Erro</h4>
                    </div>
                    <div class="modal-body" id="box_erro_msg">
                        <p>Large modal content here ...</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php
            $page_script_gerais = file_get_contents("../page_script_gerais.php");
            echo str_replace("./", "../", $page_script_gerais);
        ?>
        
        <script type="text/javascript" src="../lib/funcoes.js"></script>
        <script type="text/javascript" src="../lib/numberFormat154.js"></script>
        <script type="text/javascript" src="./controle.js"></script>
        <script type="text/javascript" src="./usuario_controller.js"></script>
        <script type="text/javascript" src="./cliente_controller.js"></script>
        <script type="text/javascript" src="./unidade_gestora_controller.js"></script>
        <script type="text/javascript" src="./unidade_orcament_controller.js"></script>
        <script type="text/javascript" src="./unidade_lotacao_controller.js"></script>
        <script type="text/javascript" src="./evento_controller.js"></script>
        <script type="text/javascript" src="./servidor_controller.js"></script>
        <script type="text/javascript" src="./lancar_eventos_controller.js"></script>
        <script type="text/javascript" src="./lancar_chprofessores_controller.js"></script>
       
        <script src="../js/jquery.aSimpleTour.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.2/jquery.scrollTo.js"></script>
        
        <!-- Necessários à ordenação do campo Data/Hora nos DataTables -->
        <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
        <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>
        
        <script type="text/javascript">
            body_sizer_controle();
            
            // Função "overlay" extraída do arquivo "overlay.js"
            //overlay_home();
//                        $('.overlay-button').click(function(){
//
//                              var loadertheme = $(this).attr('data-theme');
//                              var loaderopacity = $(this).attr('data-opacity');
//                              var loaderstyle = $(this).attr('data-style');
//
//
//                              var loader = '<div id="loader-overlay" class="ui-front loader ui-widget-overlay ' + loadertheme + ' opacity-' + loaderopacity + '"><img src="../../assets/images/spinner/loader-' + loaderstyle + '.gif" alt="" /></div>';
//
//                          if ( $('#loader-overlay').length ) {
//                                  $('#loader-overlay').remove();
//                          }
//                          $('body').append(loader);
//                          $('#loader-overlay').fadeIn('fast');
//
//                          //demo
//
//                          setTimeout(function() {
//                            $('#loader-overlay').fadeOut('fast');
//                          }, 3000);
//
//                        });
            var tour = {
                autoStart: false,
                welcomeMessage: '<h2>Tour</h2><p>Bem-vindo ao <strong>Guia de Navegação</strong> do sistema.</p><br>',
                data: [{
                    element : '#page-sidebar', // '.scroll-sidebar',
                    tooltip : 'Este é o menu principal do sistema.',
                    position: 'RT',
                    text    : '<h2>Tour</h2><p>Bem-vindo ao <strong>Guia de Navegação</strong> do sistema.</p><br>'
                }, {
                    element : '.base-dados',
                    tooltip : 'Grupo de Tabelas do Sistema',
                    position: 'R',
                    text    : '<h2>Base de Dados</h2><line><p>Os registros deste grupo estão disponíveis apenas para pesquisa e a carga destas informações é feita pelo sistema <strong>REMUNERATUS</strong>.</p><br>'
                }, {
                    element : '.manutencao-dados',
                    tooltip : 'Grupo de Rotinas de Lançamentos',
                    position: 'R',
                    text    : '<h2>Manutenção de Dados</h2><line><p>Os dados inseridos pelo usuário através desta opções serão importados pelo sistema <strong>REMUNERATUS</strong> na central da entidade.</p><br>'
//                }, {
//                    element: '#inserir_professor_lancamento',
//                    tooltip: 'Teste',
//                    position: 'L',
//                    text    : '<h2>Inserir Professores</h2><line><p>Os dados inseridos pelo usuário através desta opções serão importados pelo sistema <strong>REMUNERATUS</strong> na central da entidade.</p><br>'
//                }, {
//                    element: '#the-tour',
//                    tooltip: 'The tour section it is very important',
//                    position: 'T',
//                    text: '<h1>Data</h1><p>It is a attribute that contains every the texts and configurations that the plugin use</p>'
//                }, {
//                    element: '#the-tour dt[data-name="element"]',
//                    tooltip: 'Use a selector!',
//                    position: 'R',
//                    'controlsPosition': 'BR'
//                }, {
//                    element: '#the-tour dt[data-name="position"]',
//                    tooltip: 'Like this, "R"',
//                    position: 'R'
//                }, {
//                    element: '#the-tour dt[data-name="text"]',
//                    tooltip: 'This can be HTML! (be standard, pls)',
//                    position: 'R'
//                }, {
//                    element: '#the-tour dt[data-name="tooltip"]',
//                    tooltip: 'Oops! I just forgot myself configuration!',
//                    position: 'R'
//                }, {
//                    element: '#other',
//                    tooltip: 'Backgrounds and more!',
//                    text: '<p>Use rgba() because it looks really nice</p>'
//                },{
//                    element: '#dependency',
//                    tooltip: 'Please use it'
//                },{
//                    element: '#demos',
//                    tooltip: 'Wanna see some demos?',
//                    text: '<h2>Look out for our demos</h2>',
//                    callback: function() {
//                        $('[data-toggle=dropdown]').dropdown('toggle');
//                    }
//                },{
//                    element: '#github',
//                    tooltip: 'fork it',
//                    text: '<h1>That\'s all!</h1><p>Now you can download it or fork it on Github.</p>'
                }],
                controlsPosition: 'TR',
                buttons: {
                    next: {
                        text : 'Próximo &rarr;',
                        class: 'btn btn-default'
                    },
                    prev: {
                        text : '&larr; Anterior',
                        class: 'btn btn-default'
                    },
                    start: {
                        text : 'Iniciar',
                        class: 'btn btn-primary'
                    },
                    end: {
                        text : 'Fechar',
                        class: 'btn btn-primary'
                    }
                },
                controlsCss: {
                    background: 'rgba(55, 59, 65, 0.9)',
                    color: '#fff',
                    width: '400px',
                    'border-radius': 10
                }
            };

            $(document).ready(function(){
                $('#toor-btn').on('click', function(){
                    $.aSimpleTour(tour);
                });
            });            
            
            <?php echo $version_function;?>;
        </script>
        
    </body>
</html>