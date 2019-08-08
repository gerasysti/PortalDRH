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
    $nr_ano = date("Y");
    $nr_mes = date("m");
    
    $meses = [
        "01" => 'JANEIRO',
        "02" => 'FEVEREIRO',
        "03" => 'MARÇO',
        "04" => 'ABRIL',
        "05" => 'MAIO',
        "06" => 'JUNHO',
        "07" => 'JULHO',
        "08" => 'AGOSTO',
        "09" => 'SETEMBRO',
        "10" => 'OUTUBRO',
        "11" => 'NOVEMBRO',
        "12" => 'DEZEMBRO',
        "13" => 'DEC. TERC. 1º PARCELA',
        "14" => 'DEC. TERC. PARCELA FINAL',
        "15" => 'ABONO FUNDEB'
    ];
?>
    <body>
        
                <div id="page-content">
                    
                    <div class="col-md-12">
                        <div id="page-title">
                            <h2><strong>Remuneração de Servidores</strong></h2>
                            <p><strong>Listagem de Servidores de acordo com a Lei de Acesso à Informação - <b>Lei Nº 12.527, de 18 de Novembro de 2011.</b></strong></p>
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
                                            <label class="col-sm-1 control-label">Competência</label>
                                            <div class="col-sm-1">
                                                <select class="form-control chosen-select" id="nr_ano">
                                                    <?php
                                                        echo "<option value='0'>Exercício</option>";

                                                        $cnf = Configuracao::getInstancia();
                                                        $pdo = $cnf->db('', '');
                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                        $sql = 
                                                             "Select "
                                                            ."    e.nr_exercicio "
                                                            ."from GET_EXERCICIO_UNIDADE({$id_und}) e ";

                                                        $res = $pdo->query($sql);
                                                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                                            $selected = ((int)$obj->nr_exercicio === (int)$nr_ano ? ' selected': '');
                                                            echo "<option value={$obj->nr_exercicio} {$selected}>{$obj->nr_exercicio}</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div>&nbsp;</div>
                                            </div>

                                            <div class="col-sm-2">
                                                <select class="form-control chosen-select" id="nr_mes">
                                                    <?php
                                                        echo "<option value='0'>Mês</option>";
                                                        foreach ($meses as $codigo => $descricao) {
                                                            $selected = ((int)$codigo === (int)$nr_mes ? ' selected': '');
                                                            echo "<option value={$codigo} {$selected}>{$descricao}</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <!--<label class="col-sm-1 control-label">Parcela</label>-->
                                            <div class="col-sm-2">
                                                <select class="form-control chosen-select" id="nr_par">
                                                    <option value="0" selected>NORMAL</option>
                                                    <option value="1">COMPLEMENTAR</option>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-1 control-label">Vínculo Empregatício</label>
                                            <div class="col-sm-3">
                                                <select class="form-control chosen-select" id="id_vinculo">
                                                    <?php
                                                        echo "<option value='0' selected>(Todos)</option>";

                                                        $cnf = Configuracao::getInstancia();
                                                        $pdo = $cnf->db('', '');
                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                        $sql = 
                                                             "Select "
                                                            ."    v.id "
                                                            ."  , v.descricao "
                                                            ."from REMUN_VINCULO v "
                                                            ."order by "
                                                            ."    v.id ";

                                                        $res = $pdo->query($sql);
                                                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                                            echo "<option value='{$obj->id}'>{$obj->descricao}</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div>&nbsp;</div>
                                            </div>

                                            <div class="col-sm-2">
                                                <button id="btn_consultar" class="btn ra-round btn-primary" onclick="consultarRemuneracaoServidor('<?php echo md5($id_und);?>', '<?php echo 'unidade_' . $id_und?>')" title="Executar Consulta"><i class="glyph-icon icon-search"></i></button>
                                                <button id="btn_imprimir"  class="btn ra-round btn-primary" onclick="imprimirRemuneracaoServidor ('<?php echo md5($id_und);?>', '<?php echo 'unidade_' . $id_und?>')"  title="Baixar Consulta em PDF" disabled><i class="glyph-icon icon-file-pdf-o"></i></button>
                                                <button id="btn_exportar"  class="btn ra-round btn-primary" onclick="exportarRemuneracaoServidor ('<?php echo md5($id_und);?>', '<?php echo 'unidade_' . $id_und?>', 'arquivo_txt')"  title="Baixar Consulta em Arquivo TXT" disabled><i class="glyph-icon icon-file-text-o"></i></button>
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
                                <div class="box-wrapper" id="tabela-servidores">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
    </body>
        