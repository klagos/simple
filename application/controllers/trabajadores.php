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
		//Lista de gerencias
		/*$json_gerencias = apcu_fetch('json_list_gerencias');		
		if(!$json_gerencias){
			$url = "http://private-120a8-apisimpleist.apiary-mock.com/gerencias";
			$json_gerencias = $this->conectUrl($url);
			apcu_add('json_list_gerencias',$json_gerencias,120);
		}*/

      		$data['json_list_users'] = $json_ws;		
		//$data['json_gerencias']  = $json_gerencias;	
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

		$json_centro = apcu_fetch('json_list_centro'); 
                if(!$json_centro){
                        $url = urlapi."/costcenter/list";
                        $json_centro = $this->conectUrl($url);
                        apcu_add('json_list_centro',$json_centro,120);
                }	
			
		$data['json_gerencias'] = $json_gerencias;  	
		$data['json_usuario'] 	= $json_usuario;
		$data['json_localidad'] = $json_localidad;
		$data['json_centro']	= $json_centro;		
		
                $data['sidebar'] = 'trabajadores_edit';
                $data['content'] = 'trabajadores/resultado';
                $this->load->view('template', $data);	
	}
	/* EDITAR SOLO LOS TELEFONOS Y CODIGOS DE AREA*/
	public function update_half(){
		$nombre     =($this->input->get('nombre'))?$this->input->get('nombre'):null;
		$apellido   =($this->input->get('apellido'))?$this->input->get('apellido'):null;
                $gerencia   =($this->input->get('gerencia'))?$this->input->get('gerencia'):null;
                $cargo      =($this->input->get('cargo'))?$this->input->get('cargo'):null;
                $celular    =($this->input->get('celular'))?$this->input->get('celular'):0;
                $anexo      =($this->input->get('anexo'))?$this->input->get('anexo'):0;
                $codigo     =($this->input->get('codigo'))?$this->input->get('codigo'):0;
                $rut        =($this->input->get('rut'))?$this->input->get('rut'):null;		
		if($rut){
			$rut = trim($rut);
			$json = '[{"rut":"'.$rut.'", "phone":"'.$celular.'","annexPhone":"'.$anexo.'","areaCode":"'.$codigo.'"}]';
			$this->update_user_api($json);
		}
		$this->descargar($nombre.' '.$apellido,$gerencia,$cargo, $celular, $anexo, $codigo);
	}	

	
	
	/* EDITAR TODOS LOS  DATOS DEL PIE DE FIRMA*/
	public function update(){
                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
				
		//Datos del formulario
                $nombre_trabajador 	=($this->input->get('nombre_trabajador'))?$this->input->get('nombre_trabajador'):null;
		$apellido_trabajador	=($this->input->get('apellido_trabajador'))?$this->input->get('apellido_trabajador'):null;
                $gerencia_trabajador  	=($this->input->get('gerencia_trabajador'))?$this->input->get('gerencia_trabajador'):null;
		$cargo_trabajador	=($this->input->get('cargo_trabajador'))?$this->input->get('cargo_trabajador'):null;
		$celular_trabajador     =($this->input->get('celular_trabajador'))?$this->input->get('celular_trabajador'):0;
		$anexo_trabajador     	=($this->input->get('anexo_trabajador'))?$this->input->get('anexo_trabajador'):0;
		$codigo_trabajador	=($this->input->get('codigo_trabajador'))?$this->input->get('codigo_trabajador'):0;		
		$rut_trabajador		=($this->input->get('rut_trabajador'))?$this->input->get('rut_trabajador'):null;		
		
		if($rut_trabajador){
                	$json = '[{"name":"'.$nombre_trabajador.'","lastName":"'.$apellido_trabajador.'","rut":"'.$rut_trabajador.'","management":"'.$gerencia_trabajador.'","position":"'.$cargo_trabajador.'","phone":"'.$celular_trabajador.'",';
                	$json=$json.'"annexPhone":"'.$anexo_trabajador.'","areaCode":"'.$codigo_trabajador.'"}]';
		
			$this->update_user_api($json);

			//Los datos actualizados del usuario
			$rut 	= $rut_trabajador;
			$url    = urlapi."/users/footSignature/".$rut;
			$json_usuario = $this->conectUrl($url);

                	//Nombres y apellidos en minusculas     
                	if($json_usuario){
                	        if(strlen($json_usuario->name)>18){
				        $name  = substr(ucwords(mb_strtolower($json_usuario->name,'UTF-8')), 0, 18);     
                        	}
                        	else
                                	$name  = ucwords(mb_strtolower($json_usuario->name,'UTF-8'));

                                $json_usuario->name = explode(" ",$name)[0];

                        	$json_usuario->lastName =ucwords(mb_strtolower($json_usuario->lastName,'UTF-8'));
                	}
                	$data['json_usuario'] = $json_usuario;
                	$data['rut'] = $rut;
                	$data['sidebar'] = 'pie_firma_generar';
                	$data['content'] = 'piefirma/resultado';
                	$this->load->view('template', $data);
		}
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




}
