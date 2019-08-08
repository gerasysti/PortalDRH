<?php
//    $protocolo  = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === false) ? 'http' : 'https';
//    $host       = $_SERVER['HTTP_HOST'];
//    $script     = $_SERVER['SCRIPT_NAME'];
//    $parametros = $_SERVER['QUERY_STRING'];
//    $metodo     = $_SERVER['REQUEST_METHOD'];
//    $UrlAtual   = $protocolo . '://' . $host . $script . '?' . $parametros;
//
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

    ini_set('display_errors', true);
    error_reporting(E_ALL);

    require_once '../lib/classes/configuracao.php';
    require_once '../lib/Constantes.php';
    require_once '../lib/classes/dao.php';
    require_once '../lib/funcoes.php';
    //require_once './index_dao.php';
    
    session_start();
    $hash    = (!isset($_SESSION['acesso'])?md5("Erro"):(!isset($_SESSION['acesso']['id'])?md5("Erro"):$_SESSION['acesso']['id'])); 
    $cliente = (!isset($_SESSION['acesso']['id_cliente'])?-1:intval($_SESSION['acesso']['id_cliente']));
    $user_id = (!isset($_SESSION['acesso']['id_usuario'])?-1:intval($_SESSION['acesso']['id_usuario']));
    
    // Pasta onde o arquivo vai ser salvo
    $_UP['pasta'] = '../dist/img/brasoes/';
    
    // Tamanho máximo do arquivo (em Bytes)
    $_UP['tamanho'] = 1024 * 1024 * 1; // 1 Megabyte
    
    // Array com as extensões permitidas
    $_UP['extensoes'] = array('jpg', 'png');
    
    // Renomeia o arquivo? (Se true, o arquivo será salvo como .in e um nome único)
    $_UP['renomeia'] = false;
    
    // Array com os tipos de erros de upload do PHP
    $_UP['erros'][0] = 'Não houve erro';
    $_UP['erros'][1] = 'O arquivo do upload é maior do que o limite do PHP';
    $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
    $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
    $_UP['erros'][4] = 'Não foi feito o upload do arquivo';
    
    // Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
    if ($_FILES['arquivo']['error'] !== 0) {
        die("Não foi possível fazer o upload, erro: " . $_UP['erros'][$_FILES['arquivo']['error']]);
        exit; // Para a execução do script
    }
    
    // Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
    // Faz a verificação da extensão do arquivo
    $file_array = explode('.', $_FILES['arquivo']['name']);
    $extensao = strtolower(end($file_array));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Favor enviar arquivos com as seguintes extensões: .jpg ou .png";
        exit;
    }
    // Faz a verificação do tamanho do arquivo
    if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
      echo "O arquivo enviado é muito grande. <br>Envie arquivos de até 1Mb.";
      exit;
    }
    
    // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
    // Primeiro verifica se deve trocar o nome do arquivo
    if ($_UP['renomeia'] === true) {
        // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
        $nome_final = md5(time()) . '.in';
    } else {
        // Mantém o nome original do arquivo
        $nome_final = $_FILES['arquivo']['name'];
    }

    // Depois verifica se é possível mover o arquivo para a pasta escolhida
    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
        // Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
//        echo "Upload efetuado com sucesso!";
//        echo '<a href="' . $_UP['pasta'] . $nome_final . '"><br>Clique aqui para acessar o arquivo</a>';
//        echo "<br><br>" . $_UP['pasta'] . $nome_final;
        
        $file = '../downloads/arquivo_' . $hash . '.json';
        if (file_exists($file)) {
            unlink($file);
        }
        
        $registros = array('arquivo' => array());
        $registros['arquivo'][0]['pasta'] = $_UP['pasta'];
        $registros['arquivo'][0]['nome']  = $nome_final;
        $registros['arquivo'][0]['size']  = $_FILES['arquivo']['size'];
        $registros['arquivo'][0]['url']   = $_UP['pasta'] . $nome_final;
        $json = json_encode($registros);
        file_put_contents($file, $json);
        
        echo "OK";
    } else {
        // Não foi possível fazer o upload, provavelmente a pasta está incorreta
        echo "Não foi possível enviar o arquivo, tente novamente";
    }