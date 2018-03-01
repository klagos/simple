<?php
require_once(FCPATH."procesos.php");
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


	public function buscar($inicio=0){	
		//Verificamos que el usuario ya se haya logeado	
		if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

		//Datos del formulario
        	$licencia_numero =trim(($this->input->get('licencia_numero'))?$this->input->get('licencia_numero'):null);
        	$licencia_tipo   =trim(($this->input->get('licencia_tipo'))?$this->input->get('licencia_tipo'):null);
		$licencia_estado =trim(($this->input->get('licencia_estado'))?$this->input->get('licencia_estado'):null);
		$trabajador_rut  =trim(($this->input->get('trabajador_rut'))?$this->input->get('trabajador_rut'):null);
		$trabajador_rut  =str_replace('.','', $trabajador_rut);	
		//Frase para cache
		$cacheString = $licencia_numero +  $licencia_tipo + $licencia_estado + $trabajador_rut;		

		//Variables de la query
		$proceso_id = proceso_subsidio_id;
		$contador = 0;
		$rowtramites = [];
		//$inicio =0;//incio
		$limite =30;//limite
		
		//librerias
		$this->load->library('pagination');
        	$this->load->helper('form');
        	$this->load->helper('url');
		
		//obtener contador del cache
		$id_cache = (($licencia_numero)?'_'.$licencia_numero:'').(($licencia_tipo)?'_'.$licencia_tipo:'').(($licencia_estado)?'_'.$licencia_estado:'').(($trabajador_rut)?'_'.$trabajador_rut:'');
		$contador = apcu_fetch('contador_tram_busc'.$id_cache);

		//si no esta en cache
		if (!$contador){
			$contador = count(Doctrine::getTable('Tramite')->findLicencias($licencia_numero,$licencia_tipo,$licencia_estado,$trabajador_rut,$proceso_id,null, null));
			//se agrega la variable en el cache
			apcu_add('contador_tram_busc'.$id_cache,$contador,300);
		}
		if($contador>0)
			$rowtramites = Doctrine::getTable('Tramite')->findLicencias($licencia_numero,$licencia_tipo,$licencia_estado,$trabajador_rut,$proceso_id,$inicio, $limite);	
		$objlicencias = array();
		foreach ($rowtramites as $tr){
			if ($tr["Etapas"][0]["DatosSeguimiento"]){
				$licencia = new Licencia($tr['id']); //se crea objeto licencia
				$estado = 'Ingresada'; //estado por defecto

				if (isset($tr["Etapas"][0]["DatosSeguimiento"]))
                        	        foreach($tr["Etapas"][0]["DatosSeguimiento"] as $d){
                        	                if ($d["nombre"] == "rut_trabajador_subsidio")
                        	                        $licencia->rut_trabajador_subsidio =  substr($d["valor"],1,-1);
                        	                if ($d["nombre"] == "numero_licencia")
							if ($d["valor"][0] == '"' and substr($d["valor"],-1) == '"')
								$licencia->numero_licencia = (int) substr($d["valor"],1,-1);
							else 
                        	                       		$licencia->numero_licencia = (int) $d["valor"];
                        	                if ($d["nombre"] == "fecha_inicio_licencia")
                        	                        $licencia->fecha_inicio_licencia = substr($d["valor"],1,-1);
                        	                if ($d["nombre"] == "fecha_termino_licencia")
                        	                        $licencia->fecha_termino_licencia = substr($d["valor"],1,-1);
                        	        }

				//obtener estado de licencia
				$estado="";
				for ($i = count($tr["Etapas"]) - 1; $i >= 0; $i--){
					if (isset($tr["Etapas"][$i]["DatosSeguimiento"]))
                        	        	foreach($tr["Etapas"][$i]["DatosSeguimiento"] as $d){
							if ($d["nombre"] == "retorno_continuidad"){
								if ($d["valor"] == '"devolver"'){
									$estado = 'Proceso de pago';
									break 2;
								}
								if ($d["valor"] == '"mantener"'){
                                                	                $estado = 'Mantener en retorno';
                                                	                break 2;
                                                	        }
								if ($d["valor"] == '"cerrar"'){
                                                	                $estado = 'Finalizada';
                                                	                break 2;
                                                	        }
							}						
					
							if ($d["nombre"] == "pago_continuidad"){
                                                        	if ($d["valor"] == '"mantener"'){
                                                                	$estado = 'Mantener en pago';
                                                                	break 2;
                                                        	}
                                                        	if ($d["valor"] == '"avanzar"'){
                                                        	        $estado = 'Pagada';
                                                        	        break 2;
                                                        	}
                                                        	if ($d["valor"] == '"cerrar"'){
                                                        	        $estado = 'Finalizada';
                                                        	        break 2;
                                                        	}
							}
						 	if ($d["nombre"] == "ingreso_continuidad"){	
                                                        	if ($d["valor"] == '"avanzar"'){
                                                        	        $estado = 'Ingresada';
                                                        	        break 2;
                                                        	}
                                                        	if ($d["valor"] == '"cerrar"'){
                                                        	        $estado = 'Finalizada';
                                                        	        break 2;
                                                        	}
							}
						}
				}
	
				$tareas_completadas = 0;
				$etapas_array = array();
                        
				foreach($tr["Etapas"] as $e){
                        	        if ($e["pendiente"]){  //analogo a getEtapasActuales, metodo de clase tramite
                        	                $etapas_array[] = $e["id"];	
        	                        }
        	                        else
        	                                $tareas_completadas ++; //analogo a getTareasCompletadas, metodo de clase tramite
        	                }
				$licencia->pendiente = (int) $tr["pendiente"];
				$licencia->etapa_id = implode(', ', $etapas_array);
				$licencia->tareas_completadas = $tareas_completadas;
				$licencia->estado_licencia = $estado;
				$licencia->etapas_tramites = $licencia->getEtapasTramites();		
	
				$objlicencias[] = $licencia;
			}
		}
			
		$config['base_url'] = site_url('licencias/buscar');
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
        	//$data['tramites']=$rowtramites;
        	$data['tramites']=$objlicencias;
			
		$data['sidebar'] ='licencia';
        	$data['content'] ='licencias/encontrados';
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
                /*
		if($contador >0){
                        $rowtramites = Doctrine::getTable('Tramite')->findLicenciasPago($fecha_pago,$proceso_id);
                }
		*/
		
		$data['fecha']=$fecha_pago;
		$data['tipo'] =$tipo_pago;

		$data['contador']=$contador;
			
		$data['sidebar'] ='licencia_pago';
                $data['content'] ='licencias/archivopago';
                $data['title']   = 'Licencias encontradas';
                $this->load->view('template', $data);
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
