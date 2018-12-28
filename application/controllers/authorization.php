<?php
session_start();
require_once(FCPATH."procesos.php");
require_once('../simple/application/models/ChromePhp.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Authorization extends MY_Controller {

    private $token ;

    public function __construct() {
        parent::__construct();
    }

    public function revokeToken(){
        $_SESSION['token'] = "";

        //revocar token de la base de datos 
        //(cuando se implemente almacenar tokens en BD)
    }

    public function newToken(){
        $res = $this->getAccessToken();
        //No se obtiene token
        if ($res == "unauthorized") {
            $this->token = "";
            $_SESSION['token'] = "";
        }else{
            $this->token = $res;
            $_SESSION['token'] = $this->token;
        }
        
    }

    public function getToken(){

        //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
            $this->session->set_flashdata('redirect', current_url());
            redirect('tramites/disponibles');
        }else{
            if ($_SESSION['token'] != "") {

                $t=$_SESSION['token'];
                $this->checkToken($t);
                $_SESSION['token'] = $this->token;
                return $this->token;

            } else{ //No deberia pasar
                ChromePhp::log("CASO ESPECIAL");
                $this->token = $this->getAccessToken();
                $_SESSION['token'] = $this->token;               
                return $this->token;
            }
        }        
    }

    public function getTokenWithOutLogin(){

       
        if ($_SESSION['token'] != "") {

            $t=$_SESSION['token'];
            $this->checkToken($t);
            $_SESSION['token'] = $this->token;
            return $this->token;

        } else{ //No deberia pasar
            ChromePhp::log("CASO ESPECIAL");
            $this->token = $this->getAccessToken();
            $_SESSION['token'] = $this->token;               
            return $this->token;
        }
            
    }


    //obtener token client y secret id hardcodeado
    private function getAccessToken(){
  	$url = urlapi ."oauth/token";
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_PORT => port,
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=client_credentials&undefined=",
        CURLOPT_HTTPHEADER => array(
        "Authorization: Basic ".idsecret, 
        "Content-Type: application/x-www-form-urlencoded",
        "cache-control: no-cache" ),));

        $response = curl_exec($curl);
        curl_close($curl);
      
        $token = substr(explode(",",explode(":", $response)[1])[0],1,-1);
        return $token;
    }


    //client y secret id Hardcodeado
    public function getAccessTokenPassword($username, $password){
        $curl = curl_init();
	$url = urlapi ."oauth/token";
        curl_setopt_array($curl, array(
        CURLOPT_PORT => "443",
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=password&username=".$username."&password=".$password."&undefined=",
        CURLOPT_HTTPHEADER => array(
        "Authorization: Basic ".idsecret,
        "Content-Type: application/x-www-form-urlencoded",
        "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $response = split(",", $response);

        $token = substr(split(":",$response[0])[1],1,-1); 
        $refreshToken = substr(split(":",$response[2])[1],1,-1);

        return array($token, $refreshToken);
    }


    //Renovar Token modo Password
    public function refreshToken($rToken){
        $curl = curl_init();
	$url = urlapi ."oauth/token";
        curl_setopt_array($curl, array(
        CURLOPT_PORT => "443",
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=refresh_token&refresh_token=".$rToken."&undefined=",
        CURLOPT_HTTPHEADER => array(
        "Authorization: Basic ".idsecret,
        "Content-Type: application/x-www-form-urlencoded",
        "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response = split(",", $response);

        $token = substr(split(":",$response[0])[1],1,-1); 
        $refreshToken = substr(split(":",$response[2])[1],1,-1);

        return array($token, $refreshToken);

    }	

    public function checkToken($token){
        $curl = curl_init();
	$url = urlapi;
        curl_setopt_array($curl, array(
        CURLOPT_PORT => "8080",
        CURLOPT_URL => $url."oauth/check_token?token=".$token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "undefined=",
        CURLOPT_HTTPHEADER => array(
        "Authorization: Basic ".idsecret,
        "cache-control: no-cache"),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $response = explode(",", $response);

        if (count($response)==2) {
            $error_description = explode(":",$response[1])[1];

            if ($error_description == "Token has expired" ) { //Token expiro, entonces se pide otro
                $this->token = $this->getAccessToken();
               
            } elseif ($error_description == "Token was not recognised") { //Token mal formado, no deberia pasar
                $this->token = $this->getAccessToken();
                return "Token was not recognised";
            } else{
                 $this->token = $this->getAccessToken();
                return "Otro Caso";
            }  
        } else{ //Token valido
            // Aprovecha de solicitar nuevo token en caso que expire
            $this->token = $this->getAccessToken();
        }
    }
}


?>
