<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Licencias extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
    	}

	public function buscador_new(){
                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

                $json_ws = apcu_fetch('json_list_users_vacation');
                if (!$json_ws){
                        //Obtener data de usuarios
                        $url = urlapi . "users/list/small?parameter=name,lastname,location,rut";
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
                        apcu_add('json_list_users_vacation',$json_ws,1800);
                }

                $data['json_list_users'] = $json_ws;
                $data['sidebar']='licencia';
                $data['content'] = 'licencias/buscador_new';
                $data['title'] = 'Buscador de Licencias';
                $this->load->view('template', $data);
        }


	public function pago(){
                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

                $data['sidebar']='licencia_pago';
                $data['content'] = 'licencias/pago';
                $data['title'] = 'Reporte de Licencias';
                $this->load->view('template', $data);

        }

	public function buscar_new($inicio=0){
                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

                //Datos del formulario
                $licencia_numero =trim(($this->input->get('licencia_numero'))?$this->input->get('licencia_numero'):null);
                $licencia_tipo   =trim(($this->input->get('licencia_tipo'))?$this->input->get('licencia_tipo'):null);
                $licencia_estado =trim(($this->input->get('licencia_estado'))?$this->input->get('licencia_estado'):null);
                $fecha_inicial   =trim(($this->input->get('fecha_inicial'))?$this->input->get('fecha_inicial'):null);
                $fecha_final     =trim(($this->input->get('fecha_termino'))?$this->input->get('fecha_termino'):null);
                $trabajador_rut  =trim(($this->input->get('trabajador_rut'))?$this->input->get('trabajador_rut'):null);
                $trabajador_rut  =str_replace('.','', $trabajador_rut);
                
		//Frase para cache
                $cacheString = $licencia_numero +  $licencia_tipo + $licencia_estado + $trabajador_rut;

                //Variables de la query
                $proceso_id = proceso_subsidio_id;
                $contador = 0;
                $rowtramites = [];
               
                $limite =30;//limite

                //librerias
                $this->load->library('pagination');
                $this->load->helper('form');
                $this->load->helper('url');
	
		$json   = new stdClass();
		$json->number 	= $licencia_numero;
		$json->type	=  $licencia_tipo;	
		$json->fecha_inicio  = $fecha_inicial;
		$json->fecha_termino = $fecha_final;
		$json->rut	=$trabajador_rut;
		$json->state	= $licencia_estado;	
		$json = json_encode($json);
		$url = urlapi . "licenses/count?";
			 
		$id_cache = (($licencia_numero)?'_'.$licencia_numero:'').(($licencia_tipo)?'_'.$licencia_tipo:'').(($licencia_estado)?'_'.$licencia_estado:'').(($trabajador_rut)?'_'.$trabajador_rut:'');
                $contador = apcu_fetch('contador_tram_busc'.$id_cache);
		if (!$contador){
			$contador = $this->conectUrl($url,$json);			
			apcu_add('contador_tram_busc'.$id_cache,$contador,300);
			
		}
		if($contador>0){
			$url = urlapi . "licenses/find?limitInit=".$inicio."&limitEnd=".$limite."";
			$rowtramites = json_decode($this->conectUrl($url,$json));
		}
		//Se puede borrar si : 1 Super usuario
		$permisoLicencia = Doctrine::getTable('GrupoUsuarios')->cantGruposUsuaros(UsuarioSesion::usuario()->id,"MODULO_LICENCIA");
		$objlicencias = array();
                foreach ($rowtramites as $tr){	
			$estado = 'Ingresada';
			
			if($tr->state!=1){
				if($tr->state==2){
					$estado = 'Pagada';
				}else{
					if($tr->state==3)
						$estado = 'Retornada';
					else 
                        	        	$estado = 'Finalizada';
				}
			}
			//Values for object
			
			$idTramite	= $tr->idTramite;		
			$fecha_inicio 	= date('d-m-Y', ($tr->initDate)/1000);
			$fecha_termino	= date('d-m-Y', ($tr->endDate)/1000);
			$dias	      	= $tr->days;
			$numero 	= $tr->number;
			$rut 		= $tr->rut;		
			$tramite 	= Doctrine::getTable ( 'Tramite' )->find ($idTramite);
			$etapas 	= $tramite->getEtapasTramites();
				
			$countEtapas 	= count($etapas);
			$accion ="-";	
			$delete = false;
			if($tr->state!=4){
				for($i = $countEtapas -1; $i >= 0; $i--){
					$id = $etapas[$i]->id;	
					//Etapa 3
					$d = Doctrine::getTable('DatoSeguimiento')->findByNombreEtapa("retorno_continuidad",$id);
					if($d!=false){
						if ($d["valor"] == 'devolver'){
                                	       		$accion = 'Pagar';
                                	                break;
                                        	}
                                        	if ($d["valor"] == 'mantener'){
                                        		$accion = 'Retornar';
							$estado = 'Mantenida en retorno';
                                        	        break;
                                		}	

					}
					//Etapa 2
					$d = Doctrine::getTable('DatoSeguimiento')->findByNombreEtapa("pago_continuidad",$id);
					if($d!=false){
                                	        if ($d["valor"] == 'mantener'){
                                	      		$accion = 'Pagar';
							$estado = 'Mantenida en pago';
                                	                break;
                                        	}
                                        	if ($d["valor"] == 'avanzar'){
                                        		$accion = 'Retornar';
                                        	        break;
                                        	}        
					}
					//Etapa 1
					$d = Doctrine::getTable('DatoSeguimiento')->findByNombreEtapa("ingreso_continuidad",$id);
                                        if($d!=false){
                                                if ($d["valor"] == 'avanzar'){
                                                        $accion = 'Pagar';
                                                        break;
                                                }
                                        }
					

 
                         	}      
			}
				
			$id_pendiente   = 0;
                        if(( $permisoLicencia>=3 && $accion=='Retornar') || $permisoLicencia==4 )
                        	$id_pendiente = $tramite->getEtapasActuales()[0]->id;
			

			//Se puede borrar si :
			
			//Permiso basico
			//Ingresado
			//Participado
			if($permisoLicencia == 1 && $tr->state==1 && $tramite->usuarioHaParticipado( UsuarioSesion::usuario()->id ) && !$tr->downloaded )
				$delete = true;
			
			//Permiso admin
			//Ingresado
			if($permisoLicencia == 2 && $tr->state==1 && !$tr->downloaded )
				$delete = true;
			
			//Super admin
			if($permisoLicencia ==4)	
				$delete = true;


			//Save object
			$licencia = new Licencia($idTramite); //se crea objeto licencia
			$licencia->estado_licencia = $estado;
			$licencia->numero_licencia = $numero;
			$licencia->fecha_inicio_licencia  = $fecha_inicio;
			$licencia->fecha_termino_licencia = $fecha_termino;
			$licencia->rut_trabajador_subsidio = $rut;
			$licencia->dias	= $dias;
			$licencia->etapas_tramites = $etapas ;
			$licencia->pendiente= $id_pendiente;
			$licencia->accion = $accion;
			$licencia->delete = $delete;
			$objlicencias[] = $licencia;
			
		}

		$config['base_url'] = site_url('licencias/buscar_new');
                $config['total_rows'] = $contador;
                $config['per_page']   = $limite;
                $config['full_tag_open'] = '<div class="pagination pagination-centered"><ul>';
                if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");

                $config['full_tag_close'] = '</ul></div>';
                $config['page_query_string']=false;
                $config['query_string_segment']='offset';
                $config['first_link'] = 'Primero';
                $config['first_tag_open'] = '<li>';
                $config['first_tag_close'] = '</li>';
                $config['last_link'] = 'Ultimo';
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
                $data['content'] ='licencias/encontrados_new';
                $data['title']   = 'Licencias encontradas';

                $data['links'] = $this->pagination->create_links();
                $this->load->view('template', $data);



	}
	
	public function generarpago(){
		//Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
		//Variables de la query
                $proceso_id = proceso_subsidio_id;
                $contador = 0;

                //Datos del formulario
                $fecha_pago =($this->input->get('fecha_pago'))?$this->input->get('fecha_pago'):null;
		$tipo_pago  =($this->input->get('tipo_pago'))?$this->input->get('tipo_pago'):null;		
		
		$contador = Doctrine::getTable('Tramite')->findLicenciasPago($fecha_pago,$proceso_id)->count();
		
		$data['fecha']=$fecha_pago;
		$data['tipo'] =$tipo_pago;

		$data['contador']=$contador;
			
		$data['sidebar'] ='licencia_pago';
                $data['content'] ='licencias/archivopago';
                $data['title']   = 'Licencias encontradas';
                $this->load->view('template', $data);
	}

	public function conectUrl($url,$json){
		
		$ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ));
                $result = curl_exec($ch);
                curl_close($ch);                
                return $result;
        }


	public function generarexcel($fecha_pago,$tipo_pago){
                
		//Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
			
                //Variables de la query
                $proceso_id = proceso_subsidio_id;
                $contador = 0;
		$rowtramites = [];
                $rowtramites = Doctrine::getTable('Tramite')->findLicenciasPago($fecha_pago,$proceso_id);
                
		$CI =& get_instance();
                $CI->load->library('Excel');	
		$object = new PHPExcel();

		$table_columns = array("PerRut","CtoNumero","CnRCodigo","NcnDIndValorInf","NcnDMdaId","NcnValor","NcnDPerIdIniDev","NcnDPerIdTerDev","CreCodigo","TprId","PryNumero","ValorBase");				
		$column = 0;

  		foreach($table_columns as $field){
   			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
   			$column++;
 		}
		
		$excel_row = 2;

		foreach($rowtramites as $tramite){
			$trabajador_rut = "";
			$anticipo_subsidio=0;
			$meses_anteriores_subsidio=0;
			$dias_no_cubiertos_subsidio=0;
			$complemento_subsidio=0;
			
			foreach ($tramite->getValorDatoSeguimiento() as $tra_nro){
				if($tra_nro->nombre == 'rut_trabajador_subsidio')
                        	       	$trabajador_rut = $tra_nro->valor;
				if($tra_nro->nombre == 'anticipo_subsidio')
                                        $anticipo_subsidio = $tra_nro->valor;
				if($tra_nro->nombre == 'meses_anteriores_subsidio')
                                        $meses_anteriores_subsidio = $tra_nro->valor;
				if($tra_nro->nombre == 'dias_no_cubiertos_subsidio')
                                        $dias_no_cubiertos_subsidio = $tra_nro->valor;
				if($tra_nro->nombre == 'complemento_subsidio')
                                        $complemento_subsidio = $tra_nro->valor;
			}
			
			if($anticipo_subsidio!=0){
				$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $trabajador_rut);
                        	$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,($tipo_pago==15)?'H_ANTLICENCIA':'H_LCMEDICA');
				$object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $anticipo_subsidio);
				
				$object=$this->loadColumn($object,$excel_row);

				$excel_row++;
			}

			if($meses_anteriores_subsidio!=0){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $trabajador_rut);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,($tipo_pago==15)?'H_ANTLICENCIA':'H_LCMEDICAANT');
                                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $meses_anteriores_subsidio);
                                
                                $object=$this->loadColumn($object,$excel_row);

                                $excel_row++;
                        }

			if($dias_no_cubiertos_subsidio!=0){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $trabajador_rut);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,($tipo_pago==15)?'H_ANTDNC':'H_DIASNC');
                                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $dias_no_cubiertos_subsidio);

                                $object=$this->loadColumn($object,$excel_row);

                                $excel_row++;
                        }

			 if($complemento_subsidio!=0){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $trabajador_rut);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,($tipo_pago==15)?'H_ANTLICENCIA':'H_COMPLICEN');
                                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $complemento_subsidio);

                                $object=$this->loadColumn($object,$excel_row);

                                $excel_row++;
                        }
		}

		$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
  		header('Content-Type: application/vnd.ms-excel');
  		header('Content-Disposition: attachment;filename="Pago_licencia_"'.$fecha_pago.'".xls"');
  		$object_writer->save('php://output');

        }
	
	public function reporte(){
		 //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

	 	$data['sidebar']='reporte';
         	$data['content'] = 'licencias/reporte';
                $data['title'] = 'Reporte de Licencias';
                $this->load->view('template', $data);
           

	}

	public function reporte_descargar(){
		if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
		$fecha_inicial   =trim(($this->input->get('fecha_inicial'))?$this->input->get('fecha_inicial'):null);
                $fecha_final     =trim(($this->input->get('fecha_termino'))?$this->input->get('fecha_termino'):null);
		$downloaded	 =trim(($this->input->get('downloaded'))?$this->input->get('downloaded'):null);		
	
		$url = urlapi . "licenses/report?";	
		
		if($fecha_inicial!=null)
			$url.="&finit=".$fecha_inicial;
		if($fecha_final!=null)
                        $url.="&fend=".$fecha_final;
		if($downloaded !=null)
                        $url.="&down=".$downloaded ;	
			
		//CALL API
		$ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                               "Content-Type: application/json"
                        ));
                $result=curl_exec($ch);
                curl_close($ch);
               	$json_lic = json_decode($result);
		
		//EXCEL
		$CI =& get_instance();
                $CI->load->library('Excel');
                $object = new PHPExcel();	
		$table_columns = array("RUT","NOMBRE","NUMERO","ORG SALUD", "INICIO","TERMINO","DIAS","TIPO","TIPO REPOSO");
		$excel_row 	= 2;
                $column 	= 0;
		foreach($table_columns as $field){
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                        $column++;
                }		
		
		//VALUES FROM API
		foreach($json_lic as $json){
			$rut		= $json->rut;
			$fullname 	= $json->lastName." ".$json->name;
			$number   	= $json->number;
			$org_salud	= $json->healthAgency;
			$fecha_inicio	= date('d-m-Y', ($json->initDate)/1000);
			$fecha_termino  = date('d-m-Y', ($json->endDate)/1000);
			$days		= $json->days;
			$typeRepose	= ($json->typeRepose==1)?'Total':'Parcial';
			$type		= $json->type;
			
			$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $rut);
			$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $fullname);
			$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $number);
			$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $org_salud);
			$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $fecha_inicio);
			$object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $fecha_termino);
			$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $days);
			$object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $type);
			$object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $typeRepose);
			
			$excel_row++;	
		}
	
			
		$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="reporte_licencia.xls"');
                $object_writer->save('php://output');	
	}
	
	public function reporte_masivo(){
		//Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

		//Variables de la query
		$proceso_id = proceso_subsidio_id;
		$contador = 0;
		$rowtramites = [];

		$rowtramites = Doctrine::getTable('Tramite')->findLicenciasMasiva($proceso_id,0,null);

		$CI =& get_instance();
		$CI->load->library('Excel');
		$object = new PHPExcel();

		$table_columns = array("TRAMITE","RUT","NOMBRE","NUMERO","FECHA RECEPCION","ORG SALUD", "INICIO", "TERMINO","DIAS","TIPO","TIPO REPOSO","LUGAR REPOSO","FECHA RETORNO","PAGADO ANT.","ANTICIPO","MESES ANT.","DIAS NO CUBIERTOS","COMPLEMENTO","TOTAL","OBSERV. PAGO","FECHA RETORNO","MONTO RETORNO","SALDO RETORNO","OBSERV. RETORNO","ESTADO","RUT MEDICO");
		
		$excel_row = 2;

		$column = 0;

                foreach($table_columns as $field){
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                        $column++;
                }
		
		foreach ($rowtramites as $tramite){
			$idTramite="";
			$rut = "";
			$nombre="";
			$numero="";
			$fecha_rec="";
			$org_salud="";
			$inicio = "";
			$termino= "";
			$dias="";
			$tipo="";
			$tipo_reposo="";
			$lugar_reposo="";
					
			//DATOS PAGO
			$fecha_pago = "";
			$pagado_anterior="";
			$dias_no_cub_ant="";
			$complemento_ant="";
			$anticipo = "";
			$meses_ant = "";
			$dias_no_cu = "";
			$complemento = "";
			$obs_pago = "";
			
			//DATOS RETORNO
			$fecha_retorno = "";
			$monto_retorno = "";
			$saldo_retorno = "";
			$obs_retorno   = "";
			
			//ESTADO 
			$estado="";

			//RUT
			$rut_medico="";

			$idTramite = $tramite['id'];
			$num_etapas = count($tramite["Etapas"]);
			for($i = 0 ; $i< $num_etapas ; $i++){ 									
				foreach ($tramite["Etapas"][$i]["DatosSeguimiento"] as $tra_nro){
                        	       
					//DATOS LICENCIA
					if($tra_nro["nombre"] == 'rut_trabajador_subsidio')
                        	                $rut = str_replace('"','',$tra_nro["valor"]);
                                	if($tra_nro["nombre"] == 'nombre_trabajador_subsidio')
                                	        $nombre = str_replace('"','',$tra_nro["valor"]);
                                	if($tra_nro["nombre"] == 'numero_licencia')
                                                $numero = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'fecha_recepcion_licencia')
                                                $fecha_rec = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'organismo_salud_licencia')
                                	        $org_salud = str_replace('"','',$tra_nro["valor"]);
                                	if($tra_nro["nombre"] == 'fecha_inicio_licencia')
                                	        $inicio = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'fecha_termino_licencia')
                                	        $termino = str_replace('"','',$tra_nro["valor"]);
                                	if($tra_nro["nombre"] == 'dias_licencia')
                                	        $dias = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'tipo_licencia')
                                	        $tipo = str_replace('"','',$tra_nro["valor"]);
                        		if($tra_nro["nombre"] == 'tipo_reposo_licencia')
                                                $tipo_reposo = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'lugar_reposo_licencia'){
                                                $lugar_reposo = str_replace('"','',$tra_nro["valor"]);
						$lugar_reposo = str_replace('[','',$lugar_reposo);
						$lugar_reposo = str_replace(']','',$lugar_reposo);
					}
					
					//DATOS PAGO
					if($tra_nro["nombre"] == 'fecha_pago_subsidio')
                                                $fecha_pago = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'pagado_anterior_subsidio')
                                                $pagado_anterior = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'dias_no_cubiertos_pagados_anteri')
                                                $dias_no_cub_ant = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'complemento_anterior_subsidio')
                                                $complemento_ant = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'anticipo_subsidio')
                                                $anticipo = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'meses_anteriores_subsidio')
                                                $meses_ant = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'dias_no_cubiertos_subsidio')
                                                $dias_no_cu = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'complemento_subsidio')
                                               	$complemento = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'observacion_pago_sub')
                                                $obs_pago = str_replace('"','',$tra_nro["valor"]);
					
					//DATOS RETORNO
					if($tra_nro["nombre"] == 'fecha_retorno_subsidio')
                                                 $fecha_retorno = str_replace('"','',$tra_nro["valor"]);
                                        if($tra_nro["nombre"] == 'monto_retorno_subsidio')
                                                $monto_retorno = str_replace('"','',$tra_nro["valor"]);
                                        if($tra_nro["nombre"] == 'saldo_retorno_subsidio')
                                                $saldo_retorno = str_replace('"','',$tra_nro["valor"]);
					if($tra_nro["nombre"] == 'observacion_retorno_sub')
                                                $obs_retorno = str_replace('"','',$tra_nro["valor"]);


					//ESTADO LICENCIA
					if($tra_nro["nombre"] == 'ingreso_continuidad'){
                                        	$estado = str_replace('"','',$tra_nro["valor"]);
						if($estado =='avanzar')
							$estado = 1;
						if($estado =='cerrar')
							$estado = 4;
					}

					if($tra_nro["nombre"] == 'pago_continuidad'){
                                                $estado = str_replace('"','',$tra_nro["valor"]);
                                                if($estado =='mantener')
                                                        $estado = 2;
                                                if($estado =='avanzar')
                                                        $estado = 2;
						if($estado =='cerrar')
                                                        $estado = 4;
                                        }
					
					if($tra_nro["nombre"] == 'retorno_continuidad'){
                                                $estado = str_replace('"','',$tra_nro["valor"]);
                                                if($estado =='devolver')
                                                        $estado = 3;
                                                if($estado =='mantener')
                                                        $estado = 3;
                                                if($estado =='cerrar')
                                                        $estado = 4;
                                        }
					//RUT MEDICO
					if($tra_nro["nombre"] == 'rut_medico')
                                                 $rut_medico = str_replace('"','',$tra_nro["valor"]);
					
					
				}
			}
			
			$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $idTramite);
			$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rut);
			$object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $nombre);
			$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $numero);
			$object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $fecha_rec);
			$object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $org_salud);
			$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $inicio);
			$object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $termino);
			$object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $dias);
			$object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $tipo);
			$object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, $tipo_reposo);
			$object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $lugar_reposo);				

			//PAGO
			$object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $fecha_pago);
			$object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $pagado_anterior);

			$object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $anticipo);
			$object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $meses_ant);
			$object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $dias_no_cu  +  $dias_no_cub_ant);
			$object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $complemento + $complemento_ant);
			$total = ($pagado_anterior + $anticipo+$meses_ant);
			$object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $total);
			$object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $obs_pago);
			
			//RETORNO
			$object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $fecha_retorno);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $monto_retorno);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $monto_retorno - $total);
			$object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $obs_retorno);
			
			//ESTADO	
			$object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $estado);

			//RUT MEDICO
                        $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $rut_medico);
			$excel_row++;
			
			
		}
	
		$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        	header('Content-Type: application/vnd.ms-excel');
        	header('Content-Disposition: attachment;filename="masiva_licencias".xls"');
		header_remove('Set-Cookie');
        	$object_writer->save('php://output');

			
		$data['sidebar']='reporte';
                $data['content'] = 'licencias/reporte';
                $data['title'] = 'Reporte de Licencias';
                $this->load->view('template', $data);
			
	}
	
	public function ajax_auditar_eliminar_tramite_licencias($tramite_id,$rut){
		$tramite = Doctrine::getTable("Tramite")->find($tramite_id);
        	$data['tramite'] = $tramite;
        	$data['rut'] = $rut;
        	$this->load->view ( 'licencias/ajax_auditar_eliminar_tramite_licencias', $data );	
	
	}


	 public function borrar_tramite_licencias($tramite_id,$rut) {
               
                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

                $this->form_validation->set_rules ( 'descripcion', 'Razón', 'required' );

                $respuesta = new stdClass ();
                if ($this->form_validation->run () == TRUE){

                        $tramite = Doctrine::getTable ( 'Tramite' )->find ( $tramite_id );

                        if($tramite!=null){
                                $user_id = UsuarioSesion::usuario()->id;

                                if(true){
                                        $fecha = new DateTime ();
                                        $proceso = $tramite->Proceso;
                                        // Auditar
                                        $registro_auditoria = new AuditoriaOperaciones ();
                                        $registro_auditoria->fecha = $fecha->format ( "Y-m-d H:i:s" );
                                        $registro_auditoria->operacion = 'Eliminaciónlicencia del rut '.$rut ;
                                        $registro_auditoria->motivo = $this->input->post('descripcion');

                                        $registro_auditoria->usuario= UsuarioSesion::usuario()->nombres .' '. UsuarioSesion::usuario()->apellido_paterno.' '.UsuarioSesion::usuario()->apellido_materno.' '.UsuarioSesion::usuario()->email;

                                        $registro_auditoria->proceso = $proceso->nombre;
                                        $registro_auditoria->cuenta_id = 1;

                                        $tramite_array['proceso'] = $proceso->toArray(false);

                                        $tramite_array['tramite'] = $tramite->toArray(false);
                                        unset($tramite_array['tramite']['proceso_id']);


                                        $registro_auditoria->detalles = json_encode($tramite_array);
					
                                        $data = array();
                                        $url = urlapi."licenses/".$tramite_id."/delete";
                                        $ch = curl_init($url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                                        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));

                                        $response = curl_exec($ch);

                                        if ($response){
                                                $tramite->delete ();
                                                $registro_auditoria->save();
                                        }
					$tramite->delete ();
                                        $registro_auditoria->save();
					
                                        $respuesta->validacion = TRUE;
                                        $respuesta->redirect = site_url('licencias/buscador_new');

                                }else{
                                        ChromePhp::log('El usuario no participo en el tramite');
                                //El usuario no realizo esta solicitud
                                        $respuesta->validacion = FALSE;
                                        $respuesta->errores = validation_errors();

                                }
                        }
                        else{

                                $respuesta->validacion = FALSE;
                                $respuesta->errores = validation_errors();
                        }

                }else {
                        $respuesta->validacion = FALSE;
                        $respuesta->errores = validation_errors();
                }

                echo json_encode($respuesta);
        }




	public function loadColumn($object,$excel_row){
		$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, 0);
		$object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 2);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, 0);
		$object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row,0);
		$object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,0);
		$object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row,0);
		$object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row,0);
		$object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row,0);
		$object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row,0);
		return $object;
	}

}
