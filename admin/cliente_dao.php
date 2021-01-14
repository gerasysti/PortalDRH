<?php

    $protocolo  = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === false) ? 'http' : 'https';
    $host       = $_SERVER['HTTP_HOST'];
    $script     = $_SERVER['SCRIPT_NAME'];
    $parametros = $_SERVER['QUERY_STRING'];
    $metodo     = $_SERVER['REQUEST_METHOD'];
    $UrlAtual   = $protocolo . '://' . $host . $script . '?' . $parametros;

//    echo "<br>";
//    echo "<br>Protocolo: ".$protocolo;
//    echo "<br>Host: ".$host;
//    echo "<br>Script: ".$script;
//    echo "<br>Parametros: ".$parametros;
//    echo "<br>Metodo: ".$metodo;
//    echo "<br>Url: ".$UrlAtual."<br><br><br><br>";
//    print_r("Arquivo : " . $_FILES['arquivo']['name']);
//    echo "<br>";
//    print_r("Arquivo : " . $_FILES['arquivo']['size']);
//    echo "<br>";
//    echo json_encode($_FILES);
//    echo "<br>";

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    ini_set('display_errors', true);
    error_reporting(E_ALL);

    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/classes/dao.php';
    require_once '../lib/funcoes.php';
    require_once './index_dao.php';
    
    session_start();
    $hash    = (!isset($_SESSION['acesso'])?md5("Erro"):(!isset($_SESSION['acesso']['id'])?md5("Erro"):$_SESSION['acesso']['id']));
    $cliente = (!isset($_SESSION['acesso']['id_cliente'])?-1:intval($_SESSION['acesso']['id_cliente']));
    $user_id = (!isset($_SESSION['acesso']['id_usuario'])?-1:intval($_SESSION['acesso']['id_usuario']));
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ac'])) {
            switch ($_POST['ac']) {
                case 'consultar_cliente' : {
                    try {
                        $id = trim(filter_input(INPUT_POST, 'id')); 
                        $us = trim(filter_input(INPUT_POST, 'us')); // Usuário
                        $to = trim(filter_input(INPUT_POST, 'to')); // Tipo
                        $ps = trim(filter_input(INPUT_POST, 'ps')); // Pesquisa
                        
                        // Gerar cabeçalho de campos da Consulta em página
                        $tabela  = "<a id='ancora_datatable-responsive'></a>";
                        $tabela .= "<table id='datatable-responsive' class='table table-striped table-bordered table-hover responsive no-wrap' cellspacing='0' width='100%'>";
                        $tabela .= "    <thead>";
                        $tabela .= "        <tr class='custom-font-size-12'>";
                        $tabela .= "            <th>ID</th>";
                        $tabela .= "            <th>Nome</th>";
                        $tabela .= "            <th>CNPJ</th>";
                        $tabela .= "            <th>Município</th>";
                        $tabela .= "            <th>UF</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: right;'>Servidores</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: right;'>Usuários</th>";
                        $tabela .= "            <th data-orderable='false' style='text-align: left;'>Atualização</th>";
                        $tabela .= "            <th class='numeric' data-orderable='false' style='text-align: center;'></th>";
                        $tabela .= "        </tr>";
                        $tabela .= "    </thead>";
                        $tabela .= "    <tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $ln  = "";
                        $sql = 
                              "Select   "
                            . "    c.id "
                            . "  , trim(coalesce(c.tipo_orgao, '0')) as tipo_orgao "
                            . "  , trim(c.nome) as nome "
                            . "  , trim(c.cnpj) as cnpj "
                            . "  , c.ender_lograd "
                            . "  , c.ender_num    "
                            . "  , c.ender_bairro "
                            . "  , c.ender_cep    "
                            . "  , trim(c.municipio_cod_ibge) as municipio_cod_ibge "
                            . "  , trim(c.municipio_nome)     as municipio_nome "
                            . "  , trim(coalesce(c.municipio_uf, 'XX')) as municipio_uf "
                            . "  , c.telefones "
                            . "  , c.e_mail "
                            . "  , c.dominio "
                            . "  , c.titulo_portal "
                            . "  , c.sub_titulo_portal "
                            . "  , c.logo "
                            . "  , c.brasao_nome "
                            . "  , trim(coalesce(c.exibe_lista, '0'))   as exibe_lista "
                            . "  , coalesce(c.enviar_senha_email, 0)    as enviar_senha_email "
                            . "  , trim(coalesce(c.contra_cheque, 'N')) as contra_cheque "
                            . "  , coalesce(c.margem_consignavel, 0)    as margem_consignavel "
                            . "  , c.atualizacao "
                            . "  , coalesce(c.situacao, 0)   as situacao   "
                            . "  , coalesce(f.funcoes, 0)    as funcoes    "
                            . "  , coalesce(s.servidores, 0) as servidores "
                            . "  , coalesce(u.usuarios, 0)   as usuarios "
                            . "from ADM_CLIENTE c "
                            . "  left join ( "
                            . "    Select "
                            . "        cf.id_cliente "
                            . "      , count(cf.id_cargo) as funcoes "
                            . "    from REMUN_CARGO_FUNCAO cf "
                            . "    group by "
                            . "      cf.id_cliente "
                            . "  ) f on (f.id_cliente = c.id) "
                            . "  left join ( "
                            . "    Select "
                            . "        cs.id_cliente "
                            . "      , count(cs.id_servidor) as servidores "
                            . "    from REMUN_SERVIDOR cs "
                            . "    group by "
                            . "      cs.id_cliente "
                            . "  ) s on (s.id_cliente = c.id) "
                            . "  left join ( "
                            . "    Select "
                            . "        uc.id_cliente "
                            . "      , count(uc.id) as usuarios "
                            . "    from ADM_USUARIO uc "
                            . "    where (uc.situacao = 1) "
                            . "    group by "
                            . "      uc.id_cliente "
                            . "  ) u on (u.id_cliente = c.id) "
                            . "where (c.id > 0)   ";
                        //echo $sql . "<br><br><br>";
                        if ($cliente !== 0) $sql .= "  and (c.id = {$cliente}) " ;
                        
                        switch ($to) {
                            case 1 : {
                                $sql .= "  and (upper(c.nome) like  upper('{$ps}') || '%' ) " ;
                            } break;
                        
                            case 2 : {
                                $sql .= "  and (c.cnpj like  '{$ps}' || '%' ) " ;
                            } break;
                        }
                        
                        $sql .= "order by coalesce(c.nome, 'Administração do Sistema')"; 
                        
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $id   = str_pad($obj->id, 5, "0", STR_PAD_LEFT);
                            $tipo_orgao = (!empty($obj->tipo_orgao)?$obj->tipo_orgao:"0");
                            $nome = (!empty($obj->nome)?$obj->nome:"&nbsp;");
                            $cnpj = (!empty($obj->cnpj)?$obj->cnpj:"&nbsp;");
                            $ender_lograd = (!empty($obj->ender_lograd)?$obj->ender_lograd:"&nbsp;");
                            $ender_num    = (!empty($obj->ender_num)?$obj->ender_num:"&nbsp;");
                            $ender_bairro = (!empty($obj->ender_bairro)?$obj->ender_bairro:"&nbsp;");
                            $ender_cep    = (!empty($obj->ender_cep)?$obj->ender_cep:"&nbsp;");
                            $municipio_cod_ibge = (!empty($obj->municipio_cod_ibge)?$obj->municipio_cod_ibge:"&nbsp;");
                            $municipio_nome = (!empty($obj->municipio_nome)?$obj->municipio_nome:"&nbsp;");
                            $municipio_uf   = (!empty($obj->municipio_uf)?$obj->municipio_uf:"&nbsp;");
                            $telefones = (!empty($obj->telefones)?$obj->telefones:"&nbsp;");
                            $e_mail    = (!empty($obj->e_mail)?$obj->e_mail:"&nbsp;");
                            $dominio   = (!empty($obj->dominio)?$obj->dominio:"&nbsp;");
                            $titulo_portal = (!empty($obj->titulo_portal)?$obj->titulo_portal:"&nbsp;");
                            $sub_titulo_portal = (!empty($obj->sub_titulo_portal)?$obj->sub_titulo_portal:"&nbsp;");
                            $logo        = (!empty($obj->logo)?$obj->logo:"&nbsp;");
                            $brasao_nome = (!empty($obj->brasao_nome)?$obj->brasao_nome:"&nbsp;");
                            
                            $exibe_lista        = (!empty($obj->exibe_lista)?$obj->exibe_lista:"0");
                            $enviar_senha_email = (!empty($obj->enviar_senha_email)?$obj->enviar_senha_email:"0");
                            $contra_cheque      = (!empty($obj->contra_cheque)?$obj->contra_cheque:"N");
                            $margem_consignavel = (!empty($obj->margem_consignavel)?$obj->margem_consignavel:"0");
                            
                            $funcoes     = (!empty($obj->funcoes)?$obj->funcoes:"0");
                            $servidores  = number_format($obj->servidores, 0, ',' , '.');
                            $servidoresX = (!empty($obj->servidores)?$obj->servidores:"0");
                            $usuarios  = number_format($obj->usuarios, 0, ',' , '.');
                            $usuariosX = (!empty($obj->usuarios)?$obj->usuarios:"0");
                            
                            $atualizacao = (!empty($obj->atualizacao)?date('d/m/Y H:i:s', strtotime($obj->atualizacao)):"&nbsp;");
                            $situacao    = $obj->situacao;
                            
                            $input = 
                                  "<input type='hidden' id='id_{$id}' value='{$id}'>"
                                . "<input type='hidden' id='tipo_orgao_{$id}' value='{$tipo_orgao}'>"
                                . "<input type='hidden' id='nome_{$id}' value='{$nome}'>"
                                . "<input type='hidden' id='cnpj_{$id}' value='{$cnpj}'>"
                                . "<input type='hidden' id='ender_lograd_{$id}' value='{$ender_lograd}'>"
                                . "<input type='hidden' id='ender_num_{$id}' value='{$ender_num}'>"
                                . "<input type='hidden' id='ender_bairro_{$id}' value='{$ender_bairro}'>"
                                . "<input type='hidden' id='ender_cep_{$id}' value='{$ender_cep}'>"
                                . "<input type='hidden' id='municipio_cod_ibge_{$id}' value='{$municipio_cod_ibge}'>"
                                . "<input type='hidden' id='municipio_nome_{$id}' value='{$municipio_nome}'>"
                                . "<input type='hidden' id='municipio_uf_{$id}' value='{$municipio_uf}'>"
                                . "<input type='hidden' id='telefones_{$id}' value='{$telefones}'>"
                                . "<input type='hidden' id='e_mail_{$id}' value='{$e_mail}'>"
                                . "<input type='hidden' id='dominio_{$id}' value='{$dominio}'>"
                                . "<input type='hidden' id='titulo_portal_{$id}' value='{$titulo_portal}'>"
                                . "<input type='hidden' id='sub_titulo_portal_{$id}' value='{$sub_titulo_portal}'>"
                                . "<input type='hidden' id='logo_{$id}' value='{$logo}'>"
                                . "<input type='hidden' id='brasao_nome_{$id}' value='{$brasao_nome}'>"
                                . "<input type='hidden' id='exibe_lista_{$id}' value='{$exibe_lista}'>"
                                . "<input type='hidden' id='enviar_senha_email_{$id}' value='{$enviar_senha_email}'>"
                                . "<input type='hidden' id='contra_cheque_{$id}' value='{$contra_cheque}'>"
                                . "<input type='hidden' id='margem_consignavel_{$id}' value='{$margem_consignavel}'>"
                                . "<input type='hidden' id='funcoes_{$id}' value='{$funcoes}'>"
                                . "<input type='hidden' id='servidores_{$id}' value='{$servidoresX}'>"
                                . "<input type='hidden' id='usuarios_{$id}' value='{$usuariosX}'>"
                                . "<input type='hidden' id='atualizacao_{$id}' value='{$atualizacao}'>"
                                . "<input type='hidden' id='situacao_{$id}' value='{$situacao}'>";
                            
                            
                            $icon_ed = "<button id='editar_cliente_{$id}'  class='btn btn-round btn-primary' title='Editar Registro'  onclick='editarCliente(this.id)'><i class='glyph-icon icon-edit'></i></button>";
                            $icon_ex = "<button id='excluir_cliente_{$id}' class='btn btn-round btn-primary' title='Excluir Registro' onclick='excluirCliente(this.id)'><i class='glyph-icon icon-trash'></i></button>";
                            //$icon_ed = "<a id='editar_usuario_{$id}' href='javascript:void(0);' title='Editar Registro' onclick='editarUsuario(this.id)'><i class='glyph-icon icon-edit'></i></a>";
                            $icon_pw = "&nbsp;";
                            
                            if (empty($obj->logo)) {
                                $icon_pw = "<i class='glyph-icon icon-key'></i>";
                            } else {
                                $icon_pw = "<i class='glyph-icon icon-check-square-o'></i>";
                            }
                            
                            // Gerar linha de registro da Consulta em página
                            $tabela .= "    <tr class='custom-font-size-10' id='linha_{$id}'>";
                            $tabela .= "        <td>{$id}</td>";
                            $tabela .= "        <td>{$nome}</td>";
                            $tabela .= "        <td>" . formatarTexto('##.###.###/####-##', $cnpj) . "</td>";
                            $tabela .= "        <td>{$municipio_nome}</td>";
                            $tabela .= "        <td style='text-align: center;'>{$municipio_uf}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$servidores}</td>";
                            $tabela .= "        <td style='text-align: right;'>{$usuarios}</td>";
                            $tabela .= "        <td style='text-align: left;'>{$atualizacao}</td>";
                            $tabela .= "        <td style='text-align: center;'>{$icon_ed}&nbsp;{$icon_ex}{$input}</td>";
                            $tabela .= "    </tr>";
                        }
                        
                        $tabela .= "    </tbody>";
                        $tabela .= "</table>";

                        echo $tabela;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'carregar_usuario' : {
                    try {
                        
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'gravar_cliente' : {
                    try {
                        $op = trim(filter_input(INPUT_POST, 'op'));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $id = trim(filter_input(INPUT_POST, 'id'));
                        $id_cliente = trim(filter_input(INPUT_POST, 'id_cliente'));
                        $tipo_orgao = trim(filter_input(INPUT_POST, 'tipo_orgao'));
                        $nome = trim(filter_input(INPUT_POST, 'nome'));
                        $cnpj = trim(filter_input(INPUT_POST, 'cnpj'));
                        $cnpj_limpo = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cnpj'))); 
                        $telefones = trim(filter_input(INPUT_POST, 'telefones'));
                        $e_mail = trim(filter_input(INPUT_POST, 'e_mail'));
                        $ender_lograd = trim(filter_input(INPUT_POST, 'ender_lograd'));
                        $ender_num    = trim(filter_input(INPUT_POST, 'ender_num'));
                        $ender_bairro = trim(filter_input(INPUT_POST, 'ender_bairro'));
                        $ender_cep    = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'ender_cep')));
                        $municipio_cod_ibge = trim(filter_input(INPUT_POST, 'municipio_cod_ibge'));
                        $municipio_nome     = trim(filter_input(INPUT_POST, 'municipio_nome'));
                        $municipio_uf       = trim(filter_input(INPUT_POST, 'municipio_uf'));
                        $dominio = trim(filter_input(INPUT_POST, 'dominio'));
                        $titulo_portal = trim(filter_input(INPUT_POST, 'titulo_portal'));
                        $sub_titulo_portal = trim(filter_input(INPUT_POST, 'sub_titulo_portal'));
                        
                        $exibe_lista        = trim(filter_input(INPUT_POST, 'exibe_lista'));
                        $enviar_senha_email = trim(filter_input(INPUT_POST, 'enviar_senha_email'));
                        $contra_cheque      = trim(filter_input(INPUT_POST, 'contra_cheque'));
                        $margem_consignavel = trim(filter_input(INPUT_POST, 'margem_consignavel'));
                        
                        $situacao = trim(filter_input(INPUT_POST, 'situacao'));
                        
                        $file = '../downloads/cliente_' . $hs . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else 
                        if (($e_mail !== "") && !filter_var($e_mail, FILTER_VALIDATE_EMAIL)) {
                            echo "E-mail inválido!";
                        } else {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            if ($op === "inserir_cliente") {
                                $dao = Dao::getInstancia();
                                $uf = $dao->getFieldValueDB("ADM_ESTADO", "ID", "UF = '{$municipio_uf}'");
                                $id = $uf . str_pad(intval($dao->getGeneratorID("GEN_ADM_CLIENTE")), 3, "0", STR_PAD_LEFT); 
                                $stm = $pdo->prepare(
                                      "Insert Into ADM_CLIENTE ("
                                    . "    id                 "
                                    . "  , nome               "
                                    . "  , dominio            "
                                    . "  , cnpj               "
                                    . "  , municipio_cod_ibge "
                                    . "  , municipio_nome     "
                                    . "  , municipio_uf       "
                                    . "  , cor                "
                                    . "  , telefones          "
                                    . "  , e_mail             "
                                    . "  , ender_lograd       "
                                    . "  , topo               "
                                    . "  , logo               "
                                    . "  , titulo_portal      "
                                    . "  , sub_titulo_portal  "
                                    . "  , contador           "
                                    . "  , cor_texto          "
                                    . "  , codigo             "
                                    . "  , brasao             "
                                    . "  , ender_num          "
                                    . "  , ender_bairro       "
                                    . "  , ender_cep          "
                                    . "  , tipo_orgao         "
                                    . "  , exibe_lista        "
                                    . "  , enviar_senha_email "
                                    . "  , brasao_nome        "
                                    . "  , contra_cheque      "
                                    . "  , margem_consignavel "
                                    . "  , atualizacao        "
                                    . "  , situacao           "
                                    . ") values ("
                                    . "    :id                 "
                                    . "  , :nome               "
                                    . "  , :dominio            "
                                    . "  , :cnpj               "
                                    . "  , :municipio_cod_ibge "
                                    . "  , :municipio_nome     "
                                    . "  , :municipio_uf       "
                                    . "  , null                " // cor
                                    . "  , :telefones          "
                                    . "  , :e_mail             "
                                    . "  , :ender_lograd       "
                                    . "  , null                " // topo
                                    . "  , null                " // logo
                                    . "  , :titulo_portal      "
                                    . "  , :sub_titulo_portal  "
                                    . "  , null                " // contador
                                    . "  , null                " // cor_texto
                                    . "  , null                " // codigo
                                    . "  , null                " // brasao
                                    . "  , :ender_num          "
                                    . "  , :ender_bairro       "
                                    . "  , :ender_cep          "
                                    . "  , :tipo_orgao         "
                                    . "  , :exibe_lista        "
                                    . "  , :enviar_senha_email "
                                    . "  , null                " // brasao_nome
                                    . "  , :contra_cheque      "
                                    . "  , :margem_consignavel "
                                    . "  , null                " // atualizacao
                                    . "  , :situacao           "
                                    . ")");
                                $stm->execute(array(
                                    ':id'                 => $id,
                                    ':nome'               => $nome,
                                    ':dominio'            => $dominio,
                                    ':cnpj'               => $cnpj_limpo,
                                    ':municipio_cod_ibge' => $municipio_cod_ibge,
                                    ':municipio_nome'     => $municipio_nome,
                                    ':municipio_uf'       => $municipio_uf,
                                    //':cor'                => $cor,
                                    ':telefones'          => $telefones,
                                    ':e_mail'             => $e_mail,
                                    ':ender_lograd'       => $ender_lograd,
                                    //':topo'               => $topo,
                                    //':logo'               => $logo,
                                    ':titulo_portal'      => $titulo_portal,
                                    ':sub_titulo_portal'  => $sub_titulo_portal,
                                    //':contador'           => null,
                                    //':cor_texto'          => $cor_texto,
                                    //':codigo'             => $codigo,
                                    //':brasao'             => $brasao,
                                    ':ender_num'          => $ender_num,
                                    ':ender_bairro'       => $ender_bairro,
                                    ':ender_cep'          => $ender_cep,
                                    ':tipo_orgao'         => $tipo_orgao,
                                    ':exibe_lista'        => $exibe_lista,
                                    ':enviar_senha_email' => $enviar_senha_email,
                                    //':brasao_nome'        => $brasao_nome,
                                    ':contra_cheque'      => $contra_cheque,
                                    ':margem_consignavel' => $margem_consignavel,
                                    //':atualizacao'        => $atualizacao,
                                    ':situacao'           => $situacao
                                ));
                            } else
                            if ($op === "editar_cliente") {
                                $stm = $pdo->prepare(
                                      "Update ADM_CLIENTE c Set "
                                    . "    c.nome               = :nome               "
                                    . "  , c.dominio            = :dominio            "
                                    . "  , c.cnpj               = :cnpj               "
                                    . "  , c.municipio_cod_ibge = :municipio_cod_ibge "
                                    . "  , c.municipio_nome     = :municipio_nome     "
                                    . "  , c.municipio_uf       = :municipio_uf       "
                                    //. "  , c.cor                = :cor                "
                                    . "  , c.telefones          = :telefones          "
                                    . "  , c.e_mail             = :e_mail             "
                                    . "  , c.ender_lograd       = :ender_lograd       "
                                    //. "  , c.topo               = :topo               "
                                    //. "  , c.logo               = :logo               "
                                    . "  , c.titulo_portal      = :titulo_portal      "
                                    . "  , c.sub_titulo_portal  = :sub_titulo_portal  "
                                    //. "  , c.contador           = :contador           "
                                    //. "  , c.cor_texto          = :cor_texto          "
                                    //. "  , c.codigo             = :codigo             "
                                    //. "  , c.brasao             = :brasao             "
                                    . "  , c.ender_num          = :ender_num          "
                                    . "  , c.ender_bairro       = :ender_bairro       "
                                    . "  , c.ender_cep          = :ender_cep          "
                                    . "  , c.tipo_orgao         = :tipo_orgao         "
                                    . "  , c.exibe_lista        = :exibe_lista        "
                                    . "  , c.enviar_senha_email = :enviar_senha_email "
                                    //. "  , c.brasao_nome        = :brasao_nome        "
                                    . "  , c.contra_cheque      = :contra_cheque      "
                                    . "  , c.margem_consignavel = :margem_consignavel "
                                    . "  , c.atualizacao        = current_timestamp   "
                                    . "  , c.situacao           = :situacao           "
                                    . "where c.id = :id   ");
                                $stm->execute(array(
                                    ':nome'               => $nome,
                                    ':dominio'            => $dominio,
                                    ':cnpj'               => $cnpj_limpo,
                                    ':municipio_cod_ibge' => $municipio_cod_ibge,
                                    ':municipio_nome'     => $municipio_nome,
                                    ':municipio_uf'       => $municipio_uf,
                                    //':cor'                => $cor,
                                    ':telefones'          => $telefones,
                                    ':e_mail'             => $e_mail,
                                    ':ender_lograd'       => $ender_lograd,
                                    //':topo'               => $topo,
                                    //':logo'               => $logo,
                                    ':titulo_portal'      => $titulo_portal,
                                    ':sub_titulo_portal'  => $sub_titulo_portal,
                                    //':contador'           => null,
                                    //':cor_texto'          => $cor_texto,
                                    //':codigo'             => $codigo,
                                    //':brasao'             => $brasao,
                                    ':ender_num'          => $ender_num,
                                    ':ender_bairro'       => $ender_bairro,
                                    ':ender_cep'          => $ender_cep,
                                    ':tipo_orgao'         => $tipo_orgao,
                                    ':exibe_lista'        => $exibe_lista,
                                    ':enviar_senha_email' => $enviar_senha_email,
                                    //':brasao_nome'        => $brasao_nome,
                                    ':contra_cheque'      => $contra_cheque,
                                    ':margem_consignavel' => $margem_consignavel,
                                    //':atualizacao'        => $atualizacao,
                                    ':situacao'           => $situacao,
                                    ':id'                 => $id
                                ));
                            }
                            
                            $pdo->commit();
                            
                            $registros = array('form' => array());

                            $registros['form'][0]['id']   = str_pad(intval($id), 5, "0", STR_PAD_LEFT);
                            $registros['form'][0]['nome'] = $nome;
                            $registros['form'][0]['cnpj'] = $cnpj;

                            $json = json_encode($registros);
                            file_put_contents($file, $json);
                            
                            echo "OK";
                        }
                        
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'logotipo_cliente' : {
                    try {
                        $id = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id')));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        $logo        = trim(filter_input(INPUT_POST, 'logo'));
                        $brasao_nome = trim(filter_input(INPUT_POST, 'brasao_nome'));
                        
                        if (($hs === $hash) && ($logo !== "") && (file_exists($logo))) {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $stm = $pdo->prepare(
                                  "Update ADM_CLIENTE c Set         "
                                . "    c.logo        = :logo        "
                                . "  , c.brasao_nome = :brasao_nome "
                                . "where id = :id   ");
                            $stm->execute(array(
                                ':logo'        => $logo,
                                ':brasao_nome' => $brasao_nome,
                                ':id'          => $id
                            ));
                            
                            $pdo->commit();
                            
                            echo "OK";
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
                case 'excluir_cliente' : {
                    try {
                        $id = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'id')));
                        $fn = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'fn')));
                        $sv = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'sv')));
                        $su = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'su')));
                        $hs = trim(filter_input(INPUT_POST, 'hs'));
                        
                        if ($hs !== $hash) {
                            echo "Acesso Inválido";
                        } else 
                        if (intval($fn) !== 0) {
                            echo "Cliente possui cargos/funções cadastradas!";
                        } else 
                        if (intval($sv) !== 0) {
                            echo "Cliente possui servidores cadastrados!";
                        } else 
                        if (intval($su) !== 0) {
                            echo "Cliente possui usuários cadastrados!";
                        } else 
                        if ((intval($cliente) !== 0) && (intval($cliente) === intval($id))) {
                            echo "Auto exclusão não permitida!";
                        } else {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $stm = $pdo->prepare(
                                  "Delete from ADM_CLIENTE " 
                                . "where id = :id   ");
                            $stm->execute(array(
                                ':id'    => $id
                            ));
                            
                            $pdo->commit();
                            
                            echo "OK";
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
                
            }
        } else {
            echo "Erro ao tentar identificar ação requistada!";
        }
    }
