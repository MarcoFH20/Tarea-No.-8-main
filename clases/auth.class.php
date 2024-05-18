<?php
require_once'conexion/conexion.php';
require_once'respuestas.class.php';

class auth extends conexion{

    public function login($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);
        if(!isset($datos['usuario']) || isset($datos['password'])){
        
            return $_respuestas->error_400();

    }else{

        $usuario = $datos['usuario'];
        $password = $datos['password'];
        $password = parent::encriptar($password);
        $datos = $this->obtenerDatosUsuario($usuario);
        if($datos){
            if($password == $datos[0]['password']){
                if($datos[0]['Estado'] == "Activo"){
                //creando el Token
                $verificar = $this->insertarToken($datos[0]['usuarioId']);
                if($verificar){
                    $result = $_respuestas->response;
                    $result["result"] = array(
                        "token" => $verificar
                     );
                     return $result;
                }else{
                    return $_respuestas->error_500("Error Interno no hemos podido Guardar");
                }
                }else{
                return $_respuestas->error_200("El usuario $usuario esta inactivo");
                }
            }else{
                return $_respuestas->error_200("La Contraseña es Incorrecta");
            }

        }else{
            return $_respuestas->error_200("El usuario $usuario No existe");
        }
    }

    }

private function obtenerDatosUsuario($correo){
    $query = "SELECT UsuarioId, Password, Estado FROM usuarios Where Usuario = '$correo'";
    $datos = parent::obtenerDatos($query);
    if(isset($datos[0]["UsuarioId"])){
        return $datos;
    }else{
        return 0;
    }
}

    private function insertarToken($usuarioid){
        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
        $date = date("Y-m-d H:i");
        $estado = "Activo";
        $query = "INSERT INTO usuarios_token(UsuarioId, Token, Estado, Fecha)VALUES('$usuarioid', '$token', '$estado', '$date')";
        $verifica = parent::nonQuery($query);
        if($verifica){
            return $token;
        }else{
            return 0;
        }
    }

}