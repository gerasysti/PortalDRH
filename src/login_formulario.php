<?php
    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';

    session_start();
    
    $id_unidade  = "0";
    $ds_unidade  = "";
    $id_servidor = "0";
    $nm_servidor = "";
    $pw_servidor = "";
    $cp_servidor = "";
    $ad_servidor = "";
    $nc_servidor = "";
    $em_servidor = "";
    
    if ( isset($_SESSION['unidade']) ) {
        $id_unidade  = $_SESSION['unidade']['id'];
        $id_servidor = $_SESSION['unidade']['us'];
        $pw_servidor = $_SESSION['unidade']['pw'];
    } else {
        header('location: ./login.php');
        exit;
    }
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
        . "  , coalesce(nullif(trim(s.e_mail), ''), '...') as email "
        . "  , s.nivel_acesso "
        . "  , trim(coalesce(nullif(trim(u.titulo_portal), ''), trim(u.nome))) as ds_unidade "
        . "from REMUN_SERVIDOR s "
        . "  inner join ADM_CLIENTE u on (u.id = s.id_cliente) "
        . "where s.id_cliente  = {$id_unidade} "
        . "  and s.id_servidor = {$id_servidor} ";

    $res = $pdo->query($sql);
    if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $ds_unidade  = $obj->ds_unidade;
        $nm_servidor = trim($obj->nome);
        $cp_servidor = formatarTexto('###.###.###-##', $obj->cpf);
        $ad_servidor = "Admissão: " . date('d/m/Y', strtotime($obj->dt_admissao));
        $nc_servidor = date('d/m/Y', strtotime($obj->dt_nascimento));
        $em_servidor = $obj->email;
    }
?>

    <!--<body>-->
        
        <form action="./src/login_dao.php" id="login-validation-first" class="center-margin" method="post" target="_self">
            
                <div id="page-content">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-body">
                                
                                <small><strong>Dados do(a) Servidor(a):</strong></small>
                                <h3 class="title-hero">
                                    <input type="hidden" id="id_servidor" name="id_servidor" value="<?php echo $id_servidor;?>" form="login-validation-first"/>
                                    <input type="hidden" id="nm_servidor" name="nm_servidor" value="<?php echo $nm_servidor;?>" form="login-validation-first"/>
                                    <strong><?php echo $nm_servidor;?></strong>
                                </h3>

                                <div class="box-wrapper">
                                    <div class="col-md-12 form-group">
                                        <label for="ds_unidade">Unidade / Órgão:</label>
                                        <div class="input-group">
                                            <span class="input-group-addon add-on bg-gray">
                                                <i class="glyph-icon icon-map-marker"></i>
                                            </span>
                                            <input type="hidden" id="hash" name="hash" value="<?php echo md5($id_unidade);?>" form="login-validation-first"/>
                                            <input type="hidden" id="id_unidade" name="id_unidade" value="<?php echo $id_unidade;?>" form="login-validation-first"/>
                                            <input type="hidden" id="ds_unidade" name="ds_unidade" value="<?php echo $ds_unidade;?>" form="login-validation-first"/>
                                            <input type="text" class="form-control" id="ds_unidadeX" name="ds_unidadeX" title="<?php echo $ds_unidade;?>" value="<?php echo $ds_unidade;?>" required disabled>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 form-group">
                                        <label for="nr_matricula">Matrícula:</label>
                                        <div class="input-group">
                                            <span class="input-group-addon add-on bg-gray">
                                                <i class="glyph-icon icon-archive"></i>
                                            </span>
                                            <input type="text" class="form-control" id="nr_matricula" name="nr_matricula" value="<?php echo str_pad($id_servidor, 5, "0", STR_PAD_LEFT);?>" required disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="nr_cpf">CPF:</label>
                                        <div class="input-group">
                                            <span class="input-group-addon add-on bg-gray">
                                                <i class="glyph-icon icon-barcode"></i>
                                            </span>
                                            <input type="hidden" id="nr_cpf" name="nr_cpf" value="<?php echo $cp_servidor;?>" form="login-validation-first"/>
                                            <input type="text" class="form-control" value="<?php echo $cp_servidor;?>" required maxlength="14" onkeypress="return formatar_numero('###.###.###-##', this, event)" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="dt_nascimento">Data de Nascimento:</label>
                                        <div class="input-group">
                                            <span class="input-group-addon add-on bg-gray">
                                                <i class="glyph-icon icon-calendar"></i>
                                            </span>
                                            <input type="hidden" id="dt_nascimento" name="dt_nascimento" value="<?php echo $nc_servidor;?>" form="login-validation-first"/>
                                            <input type="text" class="form-control" value="<?php echo $nc_servidor;?>" required disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="ds_email">E-mail:</label>
                                        <div class="input-group">
                                            <span class="input-group-addon add-on bg-gray">
                                                <i class="glyph-icon icon-envelope"></i>
                                            </span>
                                            <input type="hidden" id="ds_email" name="ds_email" value="<?php echo $em_servidor;?>" form="login-validation-first"/>
                                            <input type="email" class="form-control" form="login-validation-first" value="<?php echo $em_servidor;?>" required disabled>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>    
                    
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-body">
                                
                                <div class="box-wrapper">
                                    <div class="box-wrapper">
                                        <div class="col-md-12">
                                            <p>A senha informada deverá ter no mínimo 6 caracteres e no máximo 10 caracteres alfanuméricos (letras e números).</p><br>
                                        </div>
                                        
                                        <div class="col-md-3 form-group">
                                            <label for="ds_senha_atual">Senha atual:</label>
                                            <div class="input-group">
                                                <span class="input-group-addon add-on bg-gray">
                                                    <i class="glyph-icon icon-key"></i>
                                                </span>
                                                <input type="password" class="form-control" id="ds_senha_atual" name="ds_senha_atual" placeholder="Senha atual" maxlength="10" required onchange="validando_senha(1, this)">
                                            </div>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="ds_senha_nova">Nova Senha:</label>
                                            <div class="input-group">
                                                <span class="input-group-addon add-on bg-gray">
                                                    <i class="glyph-icon icon-key"></i>
                                                </span>
                                                <input type="password" class="form-control" id="ds_senha_nova" name="ds_senha_nova" placeholder="Nova Senha" maxlength="10" required onkeyup="validando_senha(2, this)">
                                            </div>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label for="ds_senha_confirma">Confirmar Nova Senha:</label>
                                            <div class="input-group">
                                                <span class="input-group-addon add-on bg-gray">
                                                    <i class="glyph-icon icon-key"></i>
                                                </span>
                                                <input type="password" class="form-control" id="ds_senha_confirma" name="ds_senha_confirma" placeholder="Confirmar Nova Senha" maxlength="10" required onkeyup="validando_senha(3, this)">
                                            </div>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label>Nível de segurança da senha:</label>
                                            <div class="input-group">
<!--                                                <span class="input-group-addon add-on bg-gray">
                                                    <i class="glyph-icon icon-key"></i>
                                                </span>-->
                                                <meter id="mtSenha" low="20" high="90" max="100" value="0"></meter><div id="dvSenha"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 form-group">
                                        <div class="help-block text-center" id="pmTesteSenha">
                                            <!--<p class="label-warning">Teste</p>-->
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="page-wait">
                                    <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="button-pane text-center">
                        <button type="submit" class="btn btn-md btn-primary" id="ac" name="ac" value="login_altetar_senha">Confirmar</button>
                        <a href="javascript:void(0);" class="btn btn-md btn-primary switch-button" title="Cancelar" onclick="home_servidor()">Cancelar</a>
                    </div>
                    
                </div>
        
        </form>
        
    <!--</body>-->
        