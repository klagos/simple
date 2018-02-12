<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admindays extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

public function consultar(){
	
	$data['procesos']=Doctrine::getTable('Proceso')->findProcesosDisponiblesParaIniciar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(),'nombre','asc');
        $data['sidebar']='consultar_admin_days';
        $data['content'] = 'admindays/consultar';
        $data['title'] = 'Consulta los días administrativos';
        $this->load->view('template', $data);
    }
}
?>
