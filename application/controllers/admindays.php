<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admindays extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

public function consultar(){
                
	$json_ws = apcu_fetch('json_admin_days');
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
                apcu_add('json_admin_days',$json_ws,60);
        }
	
	$data['json_admin_days'] = $json_ws;
	$data['procesos']=Doctrine::getTable('Proceso')->findProcesosDisponiblesParaIniciar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(),'nombre','asc');
        $data['sidebar']='consultar_admin_days';
        $data['content'] = 'admindays/consultar';
        $data['title'] = 'Consulta los días administrativos';
        $this->load->view('template', $data);
    }
}
?>
