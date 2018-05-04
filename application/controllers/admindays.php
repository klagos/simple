<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admindays extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

public function consultar(){
	
	  //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
        	$this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }		

                
	$json_ws = apcu_fetch('json_list_users_admin');
        if (!$json_ws){
                //Obtener data de usuarios
                $url = urlapi . "users/list/small/admindays";
	        $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       "Content-Type: application/json"
                ));
                $result=curl_exec($ch);
                curl_close($ch);

                $json_ws = json_decode($result);
                apcu_add('json_list_users_admin',$json_ws,1);
        }
	
	$data['json_list_users'] = $json_ws;
	$data['procesos']=Doctrine::getTable('Proceso')->findProcesosDisponiblesParaIniciar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(),'nombre','asc');
        $data['sidebar']='consultar_admin_days';
        $data['content'] = 'admindays/consultar';
        $data['title'] = 'Consulta los días administrativos';
        $this->load->view('template', $data);
    }
}
?>
