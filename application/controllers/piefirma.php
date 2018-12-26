<?php
require_once(FCPATH."procesos.php");
require_once('authorization.php');

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

		$oa = new Authorization();
        $token = $oa->getToken();

		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
       		"Authorization: Bearer ".$token
         ));
		$result=curl_exec($ch);
        curl_close($ch);
		return json_decode($result);
	}
	
	public function buscar (){
		$rut	=($this->input->get('rut'))?$this->input->get('rut'):null;
		$rut  	=str_replace('.','', $rut);
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
			$json_usuario->lastName	=ucwords(mb_strtolower($json_usuario->lastName,'UTF-8'));
		}
		$data['json_usuario'] = $json_usuario;
		$data['rut'] = $rut;
                $data['sidebar'] = 'pie_firma_generar';
                $data['content'] = 'piefirma/resultado';
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
			$json = '{"rut":"'.$rut.'", "phone":"'.$celular.'","annexPhone":"'.$anexo.'","areaCode":"'.$codigo.'"}';

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
                	$json = '{"name":"'.$nombre_trabajador.'","lastName":"'.$apellido_trabajador.'","rut":"'.$rut_trabajador.'","management":"'.$gerencia_trabajador.'","position":"'.$cargo_trabajador.'","phone":"'.$celular_trabajador.'",';
                	$json=$json.'"annexPhone":"'.$anexo_trabajador.'","areaCode":"'.$codigo_trabajador.'"}';
			
	
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
		
		$oa = new Authorization();
        $token = $oa->getToken();

        $url  = urlapi."users";

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json",
              
              "Authorization: Bearer ".$token 
        ));
        curl_exec($ch);
        curl_close($ch);
		
	}



	/* GENERA Y DESCARGA IMAGEN DEL USUARIO  */
	public function descargar($nombre_trabajador,$gerencia_trabajador, $cargo_trabajador, $celular_trabajador, $anexo_trabajador, $codigo_trabajador ){ 
			
		$file 	  = 'uploads/resources/piefirma/plantilla.png';
		
		if(strlen($nombre_trabajador)<24 && strlen($gerencia_trabajador)<40 && strlen($cargo_trabajador)<40){
			$file     = 'uploads/resources/piefirma/plantilla.png';
		}elseif(strlen($nombre_trabajador)<27 && strlen($gerencia_trabajador)<48 && strlen($cargo_trabajador)<48){
			$file     = 'uploads/resources/piefirma/plantilla_2.png';
		}elseif(strlen($nombre_trabajador)<30 && strlen($gerencia_trabajador)<56 && strlen($cargo_trabajador)<56){
                        $file     = 'uploads/resources/piefirma/plantilla_3.png';
                }
		else{
			$file     = 'uploads/resources/piefirma/plantilla_4.png';
		}
		/*
			
		if(strlen($nombre_trabajador)>26 or strlen($gerencia_trabajador)>30 or strlen($cargo_trabajador)>40)
			$file     = 'uploads/resources/piefirma/plantilla_2.png';
		
		if(strlen($nombre_trabajador)>34 or strlen($gerencia_trabajador)>36 or strlen($cargo_trabajador)>45)
                        $file     = 'uploads/resources/piefirma/plantilla_3.png';
		
		if(strlen($nombre_trabajador)>37 or strlen($gerencia_trabajador)>36 or strlen($cargo_trabajador)>45)
                        $file     = 'uploads/resources/piefirma/plantilla_4.png';
		*/

		//if(strlen($nombre_trabajador)>27  or strlen($gerencia_trabajador)>27) 
		//	$file     = 'uploads/resources/piefirma/plantilla_large.png';			
		
		$font	  = 'uploads/resources/piefirma/calibri.ttf';
		$font_bold= 'uploads/resources/piefirma/calibri_b.ttf';
			
		/** CREATE FILE  **/
		if(file_exists($file)){
			header("Content-type: image/png");
			$im	= imagecreatefrompng($file);
			
			//Name
			$color= imagecolorallocate($im,90,90,90);
			$size = 18;	
			$x = 10;
			$base=17;
			$plus=16;
			$y = $base + $plus;
			imagettftext($im, $size,0,$x,$y,$color,$font_bold,$nombre_trabajador);

			//Cargo
			$color= imagecolorallocate($im, 115, 115, 115);
			$size = 11;
                        $y = $base + 2*$plus;
			imagettftext($im, $size,0,$x,$y,$color,$font,$cargo_trabajador);
			
			//Gerencia
			$y =$base + 3*$plus;
			imagettftext($im, $size,0,$x,$y,$color,$font,$gerencia_trabajador );			
			
			//Telefono y celular 
			$y =$base + 4*$plus;
			if($anexo_trabajador!=0 || $celular_trabajador!=0){
				$telefono="";
				if($anexo_trabajador!=0){
					$anexo = substr($anexo_trabajador,-4);
                                	$numer = substr($anexo_trabajador,0,-4);
					$telefono = '+56 '.$codigo_trabajador.' '.$numer.' '.$anexo;
					if($celular_trabajador!=0){
						$telefono =$telefono.' / +56 9 '.substr($celular_trabajador,1,4).' '.substr($celular_trabajador,5,8) ;
					}
				}
				else{
					$telefono = '+56 9 '.substr($celular_trabajador,1,4).' '.substr($celular_trabajador,5,8);
				}
                        	imagettftext($im, $size,0,$x,$y,$color,$font,$telefono);	
			}
			$path	= 'uploads/resources/piefirma/tmp/pie_firma.jpg';
			
			$quality = 100; //Calidad de imagen
			imagejpeg($im, $path, $quality);
			
			$dpi = 96;
			$image = file_get_contents($path);
			$image =substr_replace($image, pack("cnn", 1, $dpi, $dpi), 13, 5); 	
			file_put_contents($path,$image);			
			
			$this->load->helper('download');
                        $data = file_get_contents ( $path );
                        force_download ("pie_firma.jpg", $data );       
                        
                        imagedestroy($im);
			
		}			
        }

}
