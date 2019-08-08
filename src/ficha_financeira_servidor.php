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
    
    $id_und = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'un')));
    $id_ser = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'sv')));
    $nm_ser = "";
    $cp_ser = "";
    $ad_ser = "";
    $nr_exercicio = date("Y");
    
    session_start();
    $id_und = (int)$_SESSION['unidade']['id'];
    $id_ser = (int)$_SESSION['unidade']['us'];
    
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
        . "  , coalesce(s.e_mail, '...') as email "
        . "  , coalesce(s.senha, '...')  as senha "
        . "  , s.nivel_acesso "
        . "  , current_timestamp as ultimo_acesso "
        . "from REMUN_SERVIDOR s "
        . "where s.id_cliente  = {$id_und} "
        . "  and s.id_servidor = {$id_ser} ";

    $res = $pdo->query($sql);
    if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
        $nm_ser = trim($obj->nome);
        $cp_ser = "CPF: <strong>" . formatarTexto('###.###.###-##', $obj->cpf) . "</strong>";
        $ad_ser = "Admissão: <strong>" . date('d/m/Y', strtotime($obj->dt_admissao)) . "</strong>";
    }
?>
    <!--<body>-->
        
                <style>
                    .lg-text {
                        height: 40px;
                        margin: 0 auto;
                    }
                    .lg-button {
                        width : 40px;
                        height: 40px;
                        margin: 0 auto;
                    }
                </style>
                
                <div id="page-content">
                    
                    <div class="col-md-12">
                        <div id="page-title">
                            <h2>Ficha Financeira (<?php echo $nm_ser;?>)</h2>
                            <p><?php echo $cp_ser;?><br><?php echo $ad_ser;?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                    
                        <div class="panel">

                            <div class="panel-body">
                                <h3 class="title-hero">
                                    Favor selecionar os filtros necessário para pesquisa
                                </h3>

                                <div class="box-wrapper">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-sm-1 control-label">Exercício</label>
                                            <div class="col-sm-1">
                                                <select class="form-control chosen-select" id="nr_exercicio">
                                                    <?php
//                                                        echo "<option value='0'>Exercício</option>";
//
                                                        $cnf = Configuracao::getInstancia();
                                                        $pdo = $cnf->db('', '');
                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                        $sql = 
                                                             "Select "
                                                            ."    e.nr_exercicio "
                                                            ."from GET_EXERCICIO_SERVIDOR({$id_und}, {$id_ser}) e ";

                                                        $res = $pdo->query($sql);
                                                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                                            $selected = ((int)$obj->nr_exercicio === (int)$nr_exercicio ? ' selected': '');
                                                            echo "<option value={$obj->nr_exercicio} {$selected}>{$obj->nr_exercicio}</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div>&nbsp;</div>
                                            </div>
                                            
                                            <div class="col-sm-3">
                                                <select class="form-control chosen-select" id="id_servidor">
                                                    <option value="0">Automático</option>
                                                    <?php
                                                        $cnf = Configuracao::getInstancia();
                                                        $pdo = $cnf->db('', '');
                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                        $sql = 
                                                             "Select distinct "
                                                            ."    s.id_servidor "
                                                            ."  , f.descricao as cargo "
                                                            ."from REMUN_SERVIDOR s "
                                                            ."  inner join REMUN_CARGO_FUNCAO f on (f.id_cliente = s.id_cliente and f.id_cargo = s.id_cargo_atual) "
                                                            ."where s.id_cliente = {$id_und} "
                                                            ."  and s.cpf = '" . preg_replace("/[^0-9]/", "", $cp_ser) . "' ";

                                                        $res = $pdo->query($sql);
                                                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                                            $selected = ((int)$obj->id_servidor === (int)$id_ser ? ' selected': '');
                                                            echo "<option value={$obj->id_servidor} {$selected}>{$obj->cargo}</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-2">
                                                <button id="btn_consultar" class="btn ra-round btn-primary lg-text" onclick="consultarFichaFinanceira('<?php echo md5($id_und);?>', '<?php echo 'unidade_' . $id_und?>', '<?php echo 'servidor_' . $id_ser?>')" title="Executar Consulta"><i class="glyph-icon icon-search"></i></button>
                                                <button id="btn_imprimir"  class="btn ra-round btn-primary lg-text" onclick="imprimirFichaFinanceira ('<?php echo md5($id_und);?>', '<?php echo 'unidade_' . $id_und?>', '<?php echo 'servidor_' . $id_ser?>')"  title="Baixar Consulta em PDF" disabled><i class="glyph-icon icon-file-pdf-o"></i></button>
                                                <button id="btn_exportar"  class="btn ra-round btn-primary lg-text" onclick="exportarFichaFinanceira ('<?php echo md5($id_und);?>', '<?php echo 'unidade_' . $id_und?>', '<?php echo 'servidor_' . $id_ser?>', 'arquivo_txt')"  title="Baixar Consulta em Arquivo TXT" disabled><i class="glyph-icon icon-file-text-o"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    
                    </div>    
                    
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-body">
                                <div id="page-wait">
                                    <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait">

                                    </a>
                                </div>
                                <div class="box-wrapper" id="tabela-fichafinanceira">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
    <!--</body>-->
        