<?php
    $strPage = substr($page,0,-4);
    $idConsulta = $strPage == 'consultas' ? isset($_GET['cod']) ? substr($_GET['cod'],1) : substr('C1',1) : null;

    $rowLn = null;
    $nomeActive = null;
    $content = null;

    for ( $i=0; $i < count($consultas); $i++)
    {
        $id = $consultas[$i]['id'];
        $nome = $consultas[$i]['descricao'];
        $active = $id == $idConsulta ? ' class="active"' : null;
        $rowLn .= "<li{$active}><a href='?page=consultas&cod=C{$id}'>{$nome}</a></li>";

        if( $id == $idConsulta )
        {
            $nomeActive = $consultas[$i]['descricao'];
            $content = $consultas[$i]['conteudo'];
        };
    }
?>
    <!-- Page Content -->
    <div class="container">

        <section id="consultas">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Consultas
                    </h1>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 sidebarConsultas">
                    <ul class="nav list-unstyled">
                        <?php echo $rowLn; ?>
                    </ul>
                </div>
                <div class="col-md-8" id="consultasContent">
                    <div class="headerModal">
                        <h2 class="page-header">
                            <span><?php echo $nomeActive; ?></span>
                            <button id="modalAg"  class="btn btn-md btn-default pull-right" data-toggle="modal" data-target="#modalAgendamento">
                                <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                                Agendamento
                            </button>
                        </h2>
                    </div>
                    <div class="bodyConsultas">
                        <?php echo $content; ?>
                    </div>
                    <div class="collapse" id="collapseExample">
                        <!-- <div class="col-md-4 hide"> -->
                    </div>
                </div>
            </div>
        </section>

    </div>
    <hr>
    <!-- /.container -->