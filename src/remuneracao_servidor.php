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
    $hash_sessao = md5($id_und . $nr_ano . $nr_mes . date("H:i:s"));
    
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
        
                <style>
                    .optionGroup {
                        font-weight: bold;
                        font-style: italic;
                        font-variant:small-caps;
                    }                
                    .optionChild {
                        padding-left: 15px;
                    }                    
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
                            <input type="hidden" id="hash_arquivo" value="<?php echo $hash_sessao;?>">
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

                                <div class="box-wrapper form-horizontal">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-sm-1 control-label padding-label">Competência</label>
                                            <div class="col-sm-1 padding-field">
                                                <select class="form-control chosen-select" id="nr_ano" onchange="montar_lista_vinculo()">
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

                                            <div class="col-sm-2 padding-field">
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
                                            <div class="col-sm-2 padding-field">
                                                <select class="form-control chosen-select" id="nr_par">
                                                    <option value="0" selected>NORMAL</option>
                                                    <option value="1">COMPLEMENTAR</option>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-1 control-label padding-label">Vínculo Empregatício</label>
                                            <div class="col-sm-3 padding-field" id="vinculo">
                                                <select class='form-control chosen-select' id='id_vinculo'>
                                                    <option value='0' selected>(Todos)</option>
                                                    <?php
                                                        echo "<optgroup label='A PARTIR DE JAN/2020'>";
                                                        
                                                        $sql = 
                                                             "Select "
                                                            ."    s.id "
                                                            ."  , s.descricao "
                                                            ."from REMUN_SITUACAO_TCM2020 s "
                                                            ."order by "
                                                            ."    s.id ";

                                                        $res = $pdo->query($sql);
                                                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                                            echo "<option class='optionChild' value='{$obj->id}'>{$obj->descricao}</option>";
                                                        }
                                                        
                                                        echo "</optgroup>";
                                                        echo "<optgroup label='ANTES DE 2020'>";
                                                        
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
                                                            echo "<option class='optionChild' value='{$obj->id}'>{$obj->descricao}</option>";
                                                        }
                                                        
                                                        echo "</optgroup>";
                                                    ?>
                                                </select>
                                                <div>&nbsp;</div>
                                            </div>

                                            <div class="col-sm-2 padding-field">
                                                <button id="btn_consultar" class="btn ra-round btn-primary lg-text" onclick="consultarRemuneracaoServidor('<?php echo md5($id_und);?>', '<?php echo 'unidade_' . $id_und?>')" title="Executar Consulta"><i class="glyph-icon icon-search"></i></button>
                                                <button id="btn_imprimir"  class="btn ra-round btn-primary lg-text" onclick="imprimir_remuneracao_servidor ('<?php echo md5($id_und);?>', '<?php echo 'unidade_' . $id_und?>')"  title="Baixar Consulta em PDF" disabled><i class="glyph-icon icon-file-pdf-o"></i></button>
                                                <button id="btn_exportar"  class="btn ra-round btn-primary lg-text" onclick="exportarRemuneracaoServidor ('<?php echo md5($id_und);?>', '<?php echo 'unidade_' . $id_und?>', 'arquivo_txt')"  title="Baixar Consulta em Arquivo TXT" disabled><i class="glyph-icon icon-file-text-o"></i></button>
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
                                    <a href="#" class="btn btn-md btn-default overlay-button hide" data-style="dark" data-theme="bg-default" data-opacity="60" id="link_wait"></a>
                                </div>
                                <div class="box-wrapper" id="tabela-servidores">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12" id="panel_cadastro">
                        <a href="#" class="btn btn-md btn-black overlay-button hide" data-style="light" data-theme="bg-black" data-opacity="60" id="link_overlay"></a>
                    </div>
                    
                    <script type="text/javascript">
                        // Função "overlay" extraída do arquivo "overlay.js"
                        $('.overlay-button').click(function(){
                            var loadertheme = $(this).attr('data-theme');
                            var loaderopacity = $(this).attr('data-opacity');
                            var loaderstyle = $(this).attr('data-style');

                            var loader = '<div id="loader-overlay" class="ui-front loader ui-widget-overlay ' + loadertheme + ' opacity-' + loaderopacity + '"><img src="./assets/images/spinner/loader-' + loaderstyle + '.gif" alt="" /></div>';

                            if ( $('#loader-overlay').length ) {
                                $('#loader-overlay').remove();
                            }

                            $('body').append(loader);
                        });
                        
                        function montar_lista_vinculo() {
                            var exercicio = $('#nr_ano').val();
                            if ( parseInt("0" + exercicio) > 0 ) {
                                var params = {
                                    'ac'  : 'chosen-select-vinculos',
                                    'ano' : exercicio
                                };

                                // Iniciamos o Ajax 
                                $.ajax({
                                    // Definimos a url
                                    url : 'src/chosen-select_dao.php',
                                    // Definimos o tipo de requisição
                                    type: 'post',
                                    // Definimos o tipo de retorno
                                    dataType : 'html',
                                    // Dolocamos os valores a serem enviados
                                    data: params,
                                    // Antes de enviar ele alerta para esperar
                                    beforeSend : function(){
                                        ;
                                    },
                                    // Colocamos o retorno na tela
                                    success : function(data){
                                        $('#vinculo').html(data);
                                        // Refazer o estilo do controle
                                        $("#vinculo .chosen-select").chosen();
                                        $("#vinculo .chosen-search").append('<i class="glyph-icon icon-search"></i>');
                                        $("#vinculo .chosen-single div").html('<i class="glyph-icon icon-caret-down"></i>');

                                        $('#id_vinculo').val("0");
                                        $('#id_vinculo').trigger('chosen:updated');
                                    },
                                    error: function (request, status, error) {
                                        ;
                                    }
                                });  
                                // Finalizamos o Ajax
                            }
                        }
                    </script>
                </div>
        
    </body>
        