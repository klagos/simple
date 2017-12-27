<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Licencias extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
    	}

    	public function buscador(){
                $data['sidebar']='licencia';
                $data['content'] = 'licencias/buscador';
                $data['title'] = 'Buscador de Licencias';
                $this->load->view('template', $data);

	}

	public function buscar(){
		//Datos del formulario
        	$licencia_numero =($this->input->get('licencia_numero'))?$this->input->get('licencia_numero'):null;
        	$trabajador_rut  =($this->input->get('trabajador_rut'))?$this->input->get('trabajador_rut'):null;
		
		//Variables de la query
		$proceso_id = 2;
		$contador = 0;
		$rowtramites = [];
		$inicio =0;//inciio
		$limite =50;//limite

		$this->load->library('pagination');
		
		$contador = Doctrine::getTable('Tramite')->findLicencias($licencia_numero,$trabajador_rut,$proceso_id,null,null)->count();	
		if($contador >0){
			$rowtramites = Doctrine::getTable('Tramite')->findLicencias($licencia_numero,$trabajador_rut,$proceso_id,$inicio, $limite);
		}
		
		
		$config['base_url'] = site_url('licencias/encontrados');
        	$config['total_rows'] = $contador;
        	$config['per_page']   = $limite;
        	$config['full_tag_open'] = '<div class="pagination pagination-centered"><ul>';
		
		$config['full_tag_close'] = '</ul></div>';
        	$config['page_query_string']=false;
        	$config['query_string_segment']='offset';
        	$config['first_link'] = 'Primero';
        	$config['first_tag_open'] = '<li>';
        	$config['first_tag_close'] = '</li>';
        	$config['last_link'] = 'Último';
        	$config['last_tag_open'] = '<li>';
        	$config['last_tag_close'] = '</li>';
        	$config['next_link'] = '»';
        	$config['next_tag_open'] = '<li>';
        	$config['next_tag_close'] = '</li>';
        	$config['prev_link'] = '«';
        	$config['prev_tag_open'] = '<li>';
        	$config['prev_tag_close'] = '</li>';
        	$config['cur_tag_open'] = '<li class="active"><a href="#">';
       		$config['cur_tag_close'] = '</a></li>';
        	$config['num_tag_open'] = '<li>';
        	$config['num_tag_close'] = '</li>';	
		
		$this->pagination->initialize($config);
        	$data['tramites']=$rowtramites;
			
		$data['sidebar'] ='licencia';
        	$data['content'] ='licencias/encontrados';
		$data['title']   = 'Licencias encontradas';
		
		$data['links'] = $this->pagination->create_links();
       		$this->load->view('template', $data);	
	}

	

}
