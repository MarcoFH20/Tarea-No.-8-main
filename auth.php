<?php
require_once 'clases/auth.class.php';
require_once 'clases/respuestas.class.php';

$_auth = new auth;
$respuestas = new respuestas;

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $postBody = file_get_contents("php://input");

    $datosArray = $_auth->login($postBody);

    header('content-type: aplication/json');
    if(isset($datosArray['result']["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    echo json_encode($datosArray);

}else{
    header('content-type: aplication/json');
    $datosArray = $_repuestas->error_405();
    echo json_encode($datosArray);  
}