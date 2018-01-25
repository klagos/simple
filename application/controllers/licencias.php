<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Licencias extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
    	}

    	public function buscador(){
		//Verificamos que el usuario ya se haya logeado 
		if (!UsuarioSesion::usuario()->registrado) {
            		$this->session->set_flashdata('redirect', current_url());
            		redirect('tramites/disponibles');
        	}
				
                $data['sidebar']='licencia';
                $data['content'] = 'licencias/buscador';
                $data['title'] = 'Buscador de Licencias';
                $this->load->view('template', $data);

	}

	public function buscar($inicio=0){	
		//Verificamos que el usuario ya se haya logeado	
		if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

		//Datos del formulario
        	$licencia_numero =($this->input->get('licencia_numero'))?$this->input->get('licencia_numero'):null;
        	$trabajador_rut  =($this->input->get('trabajador_rut'))?$this->input->get('trabajador_rut'):null;
		
		
	
		//Variables de la query
		$proceso_id = 2;
		$contador = 0;
		$rowtramites = [];
		$inicio =0;//incio
		$limite =30;//limite
		
		//librerias
		$this->load->library('pagination');
        	$this->load->helper('form');
        	$this->load->helper('url');
			
		$rowtramites = Doctrine::getTable('Tramite')->findLicencias($licencia_numero,$trabajador_rut,$proceso_id,$inicio,$limite);	
		
		$objlicencias = array();
		foreach ($rowtramites as $tr){

			$licencia = new Licencia(); //se crea objeto licencia
			$estado = 'Ingresada'; //estado por defecto

			if (isset($tr["Etapas"][0]["DatosSeguimiento"]))
                                foreach($tr["Etapas"][0]["DatosSeguimiento"] as $d){
                                        if ($d["nombre"] == "rut_trabajador_subsidio")
                                                $licencia->rut_trabajador_subsidio =  substr($d["valor"],1,-1);
                                        if ($d["nombre"] == "numero_licencia")
                                                $licencia->numero_licencia = (int)substr($d["valor"],1,-1);
                                        if ($d["nombre"] == "fecha_inicio_licencia")
                                                $licencia->fecha_inicio_licencia = substr($d["valor"],1,-1);
                                        if ($d["nombre"] == "fecha_termino_licencia")
                                                $licencia->fecha_termino_licencia = substr($d["valor"],1,-1);
                                }
			if (isset($tr["Etapas"][1]["DatosSeguimiento"]))
                                foreach($tr["Etapas"][1]["DatosSeguimiento"] as $d){
					if ($d["nombre"] == "fecha_pago_subsidio")
						if ($d["valor"]){
							$estado = 'Pagada';
							break;
						}						
			}
			if (isset($tr["Etapas"][2]["DatosSeguimiento"]))
                                foreach($tr["Etapas"][2]["DatosSeguimiento"] as $d){
					if ($d["nombre"] == "fecha_retorno_subsidio")
                                                if ($d["valor"]){
                                                        $estado = 'Retornada';
							break;
						}
			}
			if (isset($tr["Etapas"][2]["pendiente"]))
				if (!$tr["Etapas"][2]["pendiente"])
					$estado = 'Finalizada';
			
			$tareas_completadas = 0;
			$etapas_array = array();
                        
			foreach($tr["Etapas"] as $e){
                                if ($e["pendiente"]){  //analogo a getEtapasActuales, metodo de clase tramite
                                        $etapas_array[] = $e["id"];

                                }
                                else
                                        $tareas_completadas ++; //analogo a getTareasCompletadas, metodo de clase tramite
                        }
			$licencia->id = $tr["id"];
			$licencia->pendiente = (int) $tr["pendiente"];
			$licencia->etapa_id = implode(', ', $etapas_array);
			$licencia->tareas_completadas = $tareas_completadas;
			$licencia->estado_licencia = $estado;
		
			$objlicencias[] = $licencia;
			
		}
			
		$config['base_url'] = site_url('licencias/buscar');
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
        	$data['tramites']=$objlicencias;
			
		$data['sidebar'] ='licencia';
        	$data['content'] ='licencias/encontrados';
		$data['title']   = 'Licencias encontradas';
		
		$data['links'] = $this->pagination->create_links();
       		$this->load->view('template', $data);	
	}

	

}
