<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class procesoInduccion extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
    	}
	
	//Vista para descarga de documentos
	  public function descargar(){

                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

		
		//Obligatorios
                $data['archivo1'] = "doc1.pdf";
                $data['archivo2'] = "doc2.pdf";
                $data['archivo3'] = "doc3.pdf";		
		$data['archivo4'] = "doc4.pdf";
		$data['archivo5'] = "doc5.pdf";
		$data['archivo6'] = "doc6.pdf";
		
		//Complementarios
		$path	   ="uploads/resources/procesoInduccion/complement";
		$list_comp = $this->read_files($path);
		
		$data['list_comp'] = $list_comp;

		$data['sidebar']='descarga_inducccion';
 	        $data['content'] = 'procesoInduccion/descargar';
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
	
	public function obligatorio($file){
		//Verified login
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }	
		$path = "uploads/resources/procesoInduccion/".$file;
		$this->descargar_($path);
		
	}
	
	public function complement($file){
                //Verified login
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
                $path = "uploads/resources/procesoInduccion/complement/".$file;
                $this->descargar_($path);

        }		
	
	/* Descargar los archivos*/
	public function descargar_($file){
		$this->load->helper('download');
                $data = file_get_contents ( $file );
                force_download ( $file, $data );
	}

	

}
