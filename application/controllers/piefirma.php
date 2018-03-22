<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PieFirma extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
    	}
	
	//Vista para el generador
    	public function editar(){
		//Verificamos que el usuario ya se haya logeado 
		if (!UsuarioSesion::usuario()->registrado) {
            		$this->session->set_flashdata('redirect', current_url());
            		redirect('tramites/disponibles');
        	}
		//Lista de usuarios
		$json_ws = apcu_fetch('json_list_users_pie_firma');
        	if (!$json_ws){
                
                	$url = urlapi . "users/list/footSignature";
                	$json_ws = $this->conectUrl($url);//json_decode($result);
                	apcu_add('json_list_users_pie_firma',$json_ws,120);
        	}
		//Lista de gerencias
		$json_gerencias = apcu_fetch('json_list_gerencias');		
		if(!$json_gerencias){
			$url = "http://private-120a8-apisimpleist.apiary-mock.com/gerencias";
			$json_gerencias = $this->conectUrl($url);
			apcu_add('json_list_gerencias',$json_gerencias,120);
		}
      		$data['json_list_users'] = $json_ws;		
		$data['json_gerencias']  = $json_gerencias;	
                $data['sidebar']='pie_firma_editar';
                $data['content'] = 'piefirma/editar';
                $this->load->view('template', $data);

	}

	public function generar(){
               $data['sidebar']='pie_firma_generar';
               $data['content'] = 'piefirma/generar';
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

	
	/* UPDATE DATOS DEL USUARIO*/
	public function update(){
                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
				
		//Datos del formulario
                $nombre_trabajador 	=($this->input->get('nombre_trabajador'))?$this->input->get('nombre_trabajador'):null;
                $gerencia_trabajador  	=($this->input->get('gerencia_trabajador'))?$this->input->get('gerencia_trabajador'):null;
		$cargo_trabajador	=($this->input->get('cargo_trabajador'))?$this->input->get('cargo_trabajador'):null;
		$celular_trabajador     =($this->input->get('celular_trabajador'))?$this->input->get('celular_trabajador'):0;
		$anexo_trabajador     	=($this->input->get('anexo_trabajador'))?$this->input->get('anexo_trabajador'):0;
		$codigo_trabajador	=($this->input->get('codigo_trabajador'))?$this->input->get('codigo_trabajador'):null;		
		$rut_trabajador		=($this->input->get('rut_trabajador'))?$this->input->get('rut_trabajador'):null;		
		
		/** UPDATE USER  **/
                //$url = "http://private-120a8-apisimpleist.apiary-mock.com/users";
		$url  = urlapi."users/list";
                $json = '[{"rut":"'.$rut_trabajador.'","management":"'.$gerencia_trabajador.'","position":"'.$cargo_trabajador.'","phone":"'.$celular_trabajador.'",';
                $json=$json.'"annexPhone":"'.$anexo_trabajador.'","areaCode":"'.$codigo_trabajador.'"}]';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ));
                curl_exec($ch);
                curl_close($ch);

		$this->descargar($nombre_trabajador,$gerencia_trabajador, $cargo_trabajador, $celular_trabajador, $anexo_trabajador, $codigo_trabajador);	
				
	}
	/* GENERA Y DESCARGA IMAGEN DEL USUARIO  */
	public function descargar($nombre_trabajador,$gerencia_trabajador, $cargo_trabajador, $celular_trabajador, $anexo_trabajador, $codigo_trabajador ){ 
		
		$file 	  = 'uploads/resources/piefirma/plantilla.png';
		if(strlen($nombre_trabajador)>20)
			$file     = 'uploads/resources/piefirma/plantilla_large.png';			
		
		$font	  = 'uploads/resources/piefirma/calibri.ttf';
		$font_bold= 'uploads/resources/piefirma/calibri_b.ttf';
			
		/** CREATE FILE  **/
		if(file_exists($file)){
			header("Content-type: image/png");
			$im	= imagecreatefrompng($file);
			
			//Name
			$color= imagecolorallocate($im,90,90,90);
			$size = 18;		
			$x = 108;
			$y = 26;
			imagettftext($im, $size,0,$x,$y,$color,$font_bold,$nombre_trabajador);

			//Cargo
			$color= imagecolorallocate($im, 115, 115, 115);
			$size = 11;
                        $y = 42;
			imagettftext($im, $size,0,$x,$y,$color,$font,$cargo_trabajador);
			
			//Gerencia
			$y =58;
			imagettftext($im, $size,0,$x,$y,$color,$font,$gerencia_trabajador );			
			
			//Telefono y celular 
			$y =75;
			if($anexo_trabajador!=0 || $celular_trabajador!=0){
				$telefono="";
				if($anexo_trabajador!=0){
					$anexo = substr($anexo_trabajador,-4);
                                	$numer = substr($anexo_trabajador,0,-4);
					$telefono = $codigo_trabajador.'-'.$numer.' '.$anexo;
					if($celular_trabajador!=0){
						$telefono =$telefono.' / +569 '.$celular_trabajador;
					}
				}
				else{
					$telefono = '+569 '.$celular_trabajador;
				}
                        	imagettftext($im, $size,0,$x,$y,$color,$font_bold,$telefono);	
			}
			imagepng($im);	
			imagedestroy($im);			
		}			
        }

}
