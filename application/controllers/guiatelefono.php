<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class GuiaTelefono extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
    	}
	
	//Vista para el generador
    	public function consultar(){
		//Verificamos que el usuario ya se haya logeado 
		if (!UsuarioSesion::usuario()->registrado) {
            		$this->session->set_flashdata('redirect', current_url());
            		redirect('tramites/disponibles');
        	}
		//Lista de usuarios para guia 
		$json_ws = apcu_fetch('json_list_users_guia_telefono');
        	if (!$json_ws){
                
                	$url = urlapi . "users/list/small/phone";
                	$json_ws = $this->conectUrl($url);//json_decode($result);
                	apcu_add('json_list_users_guia_telefono',$json_ws,120);
        	}

      		$data['json_list_users'] = $json_ws;		
                $data['sidebar']='guia_telefono';
                $data['content'] = 'guiaTelefono/consultar';
                $this->load->view('template', $data);
	}

	public function conectUrl($url){
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "Content-Type: application/json"
                 ));
                $result=curl_exec($ch);
                curl_close($ch);
                return json_decode($result);
        }

}
