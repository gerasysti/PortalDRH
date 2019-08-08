<?php
/*
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream;");
    header("Content-Length:".filesize($arquivo));
    header("Content-disposition: attachment; filename=".$arquivo);
    header("Pragma: no-cache");
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Expires: 0");
    readfile($arquivo);
    flush();
 */
$arquivo = $_GET["arquivo"];
// Faz o teste se a variavel não esta vazia e se o arquivo realmente existe
if( isset($arquivo) && file_exists($arquivo) ){ 
    // Verifica a extensão do arquivo para pegar o tipo
    switch(strtolower(substr(strrchr(basename($arquivo), "."), 1))){ 
        case "pdf" : $tipo = "application/pdf"; break;
        case "exe" : $tipo = "application/octet-stream"; break;
        case "zip" : $tipo = "application/zip"; break;
        case "doc" : $tipo = "application/msword"; break;
        case "xls" : $tipo = "application/vnd.ms-excel"; break;
        case "ppt" : $tipo = "application/vnd.ms-powerpoint"; break;
        case "gif" : $tipo = "image/gif"; break;
        case "png" : $tipo = "image/png"; break;
        case "jpg" : $tipo = "image/jpg"; break;
        case "mp3" : $tipo = "audio/mpeg"; break;
        case "csv" : $tipo = "text/csv"; break;
        case "txt" : $tipo = "text/txt"; break;
        case "php" : // deixar vazio por segurança
        case "htm" : // deixar vazio por segurança
        case "html": // deixar vazio por segurança
    }
    // Informa o tipo do arquivo ao navegador
    header("Content-Type: "   . $tipo);                 
    // Informa o tamanho do arquivo ao navegador
    header("Content-Length: " . filesize($arquivo));    
    // Informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
    header("Content-Disposition: attachment; filename=" . basename($arquivo)); 
    // Lê o arquivo
    readfile($arquivo); 
    // Aborta pós-ações
    exit; 
}