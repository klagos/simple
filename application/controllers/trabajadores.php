<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trabajadores extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
		//Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
    	}
	
	//Vista para el generador
    	public function buscar(){
		//Verificamos que el usuario ya se haya logeado 
		if (!UsuarioSesion::usuario()->registrado) {
            		$this->session->set_flashdata('redirect', current_url());
            		redirect('tramites/disponibles');
        	}
		//Lista de usuarios
		$json_ws = apcu_fetch('json_list_users_editar');
        	if (!$json_ws){
                
                	$url = urlapi . "users/list/small?parameter=name,lastname";
                	$json_ws = $this->conectUrl($url);
                	apcu_add('json_list_users_editar',$json_ws,120);
        	}

      		$data['json_list_users'] = $json_ws;		
                $data['sidebar']='trabajadores_edit';
                $data['content'] = 'trabajadores/buscar';
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
	
	public function buscar_user (){
		$rut	=($this->input->get('rut_trabajador'))?$this->input->get('rut_trabajador'):null;
		$rut  	=str_replace('.','', $rut);
		$url    = urlapi."/users/dto/".$rut;	
		$json_usuario = $this->conectUrl($url);
		
		$json_usuario->name =ucwords(mb_strtolower($json_usuario->name,'UTF-8'));
                $json_usuario->lastName =ucwords(mb_strtolower($json_usuario->lastName,'UTF-8'));
		
		//Codigo localidad y centro
		$code_localidad = $json_usuario->locationCode;	
		$code_centro	= $json_usuario->costCenterCode;
		
		 //Lista de gerencias
                $json_gerencias = apcu_fetch('json_list_gerencias');          
                if(!$json_gerencias){
                        $url = "http://private-120a8-apisimpleist.apiary-mock.com/gerencias";
                        $json_gerencias = $this->conectUrl($url);
                        apcu_add('json_list_gerencias',$json_gerencias,120);
                }
		
		//Lista de localidad	
		$json_localidad = apcu_fetch('json_list_localidad');           
                if(!$json_localidad){
                        $url = urlapi."/location/list";
                        $json_localidad = $this->conectUrl($url);
                        apcu_add('json_list_localidad',$json_localidad,120);
                }
		
		//Localidad por defecto
		if($code_localidad!=""){
			foreach ($json_localidad as $item) {
   				if($item->code==$code_localidad){
					$json_usuario->location = $item->name.' - '.$item->code;
					break; 
				}
			}
		}
		
		$json_centro = apcu_fetch('json_list_centro'); 
                if(!$json_centro){
                        $url = urlapi."/costcenter/list";
                        $json_centro = $this->conectUrl($url);
                        apcu_add('json_list_centro',$json_centro,120);
                }

		//Centro de codigo por defecto
                if($code_centro!=""){
                        foreach ($json_centro as $item) {
                                if($item->code==$code_centro){
                                        $json_usuario->costCenter = $item->name.' - '.$item->code;
                                        break;
                                }
                        }
                }
	
		$data['json_gerencias'] = $json_gerencias;  	
		$data['json_usuario'] 	= $json_usuario;
		$data['json_localidad'] = $json_localidad;
		$data['json_centro']	= $json_centro;		
		
                $data['sidebar'] = 'trabajadores_edit';
                $data['content'] = 'trabajadores/resultado';
                $this->load->view('template', $data);	
	}

	/* EDITAR TODOS LOS  DATOS DEL PIE DE FIRMA*/
	public function update(){
				
		//Datos del formulario
		$rut            =($this->input->get('rut'))?$this->input->get('rut'):null;
                $nombre 	=($this->input->get('nombre'))?$this->input->get('nombre'):null;
		$apellido	=($this->input->get('apellido'))?$this->input->get('apellido'):null;
		$gender       	=($this->input->get('gender'))?$this->input->get('gender'):null;
		$birth_day	=($this->input->get('birth_day'))?$this->input->get('birth_day'):null;
		//Datos laborales
		$cargo          =($this->input->get('cargo'))?$this->input->get('cargo'):null;
		$email          =($this->input->get('email'))?$this->input->get('email'):null;
                $contract_date	=($this->input->get('contract_date'))?$this->input->get('contract_date'):null;
		$contractType	=($this->input->get('contractType'))?intval($this->input->get('contractType')):null;	
		$gerencia  	=($this->input->get('gerencia'))?$this->input->get('gerencia'):null;
		$localidad      =($this->input->get('localidad'))?$this->input->get('localidad'):null;
		$centro       	=($this->input->get('centro'))?$this->input->get('centro'):null;
		$celular	=($this->input->get('celular'))?intval($this->input->get('celular')):0;
		$anexo     	=($this->input->get('anexo'))?intval($this->input->get('anexo')):0;
		$codigo		=($this->input->get('codigo'))?intval($this->input->get('codigo')):0;		
		$active 	=($this->input->get('vinculacion'))?$this->input->get('vinculacion'):null;		
		
				
		//Personales
		$json->rut=trim($rut);
		$json->name=$nombre;
		$json->gender=$gender;
		$json->apellido=$apellido;
		$json->birth_day=$birth_day;
		
		//Laborales
		$json->position=$cargo;
                $json->email=$email;
                $json->contract_date=$contract_date;
                $json->management=$gerencia;
                $json->locationCode=trim(explode("-",$localidad)[1]);
		$json->costCenterCode=trim(explode("-",$centro)[1]);
		$json->phone=$celular;
		$json->annexPhone = $anexo;
		$json->contractType = $contractType;
		$json->areaCode=$codigo;
		$json->active = ($active==1)?true:false;		
		
		//Encode	
		$json = json_encode($json);
		$json = '['.$json.']';
		$this->update_user_api($json);
		$this->buscar();
				
		
	}

	public function update_user_api($json){
		
		//$url = "http://private-120a8-apisimpleist.apiary-mock.com/users";
                $url  = urlapi."users/list";

		$ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ));
                curl_exec($ch);
                curl_close($ch);
		
	}



	public function reporte(){
                 //Verificamos que el usuario ya se haya logeado
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

                $data['sidebar']='reporte';
                $data['content'] = 'trabajadores/reporte';
                $data['title'] = 'Reporte de Trabajadores';
                $this->load->view('template', $data);
        }




}
