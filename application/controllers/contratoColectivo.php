<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class contratoColectivo extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
    	}
	
	//Vista para la lista de documentos
    	public function mostrar(){
		
		//Verificamos que el usuario ya se haya logeado 
		if (!UsuarioSesion::usuario()->registrado) {
            		$this->session->set_flashdata('redirect', current_url());
            		redirect('tramites/disponibles');
        	}
		
		$path ="uploads/resources/contratoColectivo/sind";

		//Vigentes
		$data['archivo1'] = "sind1.pdf";
		$data['archivo2'] = "sind2.pdf";
		$data['archivo3'] = "sind3.pdf";
				
		//Historicos
		$list_sind1 = $this->read_files($path.'1');
		$list_sind2 = $this->read_files($path.'2');
		$list_sind3 = $this->read_files($path.'3');

		rsort($list_sind1);
		rsort($list_sind2);
		rsort($list_sind3);
 
		$data['list_sind1'] = $list_sind1;	
		$data['list_sind2'] = $list_sind2;
		$data['list_sind3'] = $list_sind3;

	      	$data['sidebar']='pie_firma_generar';
              	$data['content'] = 'contratoColectivo/mostrar';
         	$this->load->view('template', $data);

	}

	//Lee los archivos contenidos en el path
	public function read_files($path){
		$list_files = array();
		if($handler = opendir($path)){
                        while (($file = readdir($handler)) !== FALSE){
                                if ($file != "." && $file != "..") {
                                        if(is_file($path."/".$file)){
                                        $list_files[] = $file;
                                        }
                                }
                        }
                        closedir($handler);
			return $list_files;
                }
		else
			return null;
	
	}
	
	public function vigentes($file){
		//Verified login
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
		$path = "uploads/resources/contratoColectivo/".$file;
		$this->descargar($path);
		
	}
	
	public function historicos($sind,$file){
                //Verified login
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
                $path = "uploads/resources/contratoColectivo/".$sind."/".$file;
                $this->descargar($path);

        }		
	
	/* Descargar los archivos*/
	public function descargar($file){
		$this->load->helper('download');
                $data = file_get_contents ( $file );
                force_download ( $file, $data );
	}

	

}
