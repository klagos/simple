<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Estudios extends MY_Controller {

    	public function __construct() {
        	parent::__construct();
    	}
	
	//Funcion que genera la vista para descargar el documento de avance
	public function avance(){
		//Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
		}
		
		$data['sidebar']='avance_estudio';
                $data['content'] = 'estudios/avance';
                $this->load->view('template', $data);

		
	}
	
	   //Funcion que genera la vista para descargar el documento de avance
        public function generar(){
                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

		$CI =& get_instance();
                $CI->load->library('Excel');
                $objPHPExcel=null;

		$inputFileName = 'uploads/datos/avance.xlsx';
		try {
                        log_message('info',"Load excel: ".$inputFileName);
                        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFileName);
                } catch(Exception $e) {
                        log_message('error',"Load excel: ".$filename." failed");
                        die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                }
		
		$n_comite=30;
		$n_procesos=8;
		$num_rows=34;
		$num_columns = 8;
		
		$id_proceso_1 = proceso_acta_de_reunion_id;
		$id_proceso_2 = proceso_carta_gantt_id;
		$id_proceso_3 = proceso_acta_de_constitucion_id;	
		$id_proceso_4 = proceso_registro_difusion_id;
		$id_proceso_5 = proceso_registro_entrega_id;
                $id_proceso_6 = proceso_reg_difusion_resultados_id;
                $id_proceso_7 = proceso_informe_resultados;
                $id_proceso_8 = proceso_informe_proceso;

		//arreglo ordenado segun numero de columna de los procesos en el excel
		$list_ids_procesos = array($id_proceso_3,$id_proceso_2,$id_proceso_1,$id_proceso_4,$id_proceso_5,$id_proceso_6,$id_proceso_7,$id_proceso_8);

		$sheet = $objPHPExcel->getSheet(0);

		//diccionario donde keys son el nombre de las columnas y values el numero de columna	
		$columns = [];
		for ($i = 2; $i < $num_rows + 1; $i ++){
			$cell = $sheet->getCellByColumnAndRow(0, $i);
                	$val  = trim($cell->getValue());
			$columns[$val] = $i;
		}

		//rellenando plantilla con No enviado's
                for ($excel_row = 2; $excel_row < $num_rows + 1; $excel_row++){
			for ($excel_column = 1; $excel_column < $num_columns + 1; $excel_column ++) 
                        	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($excel_column, $excel_row, "No enviado");
                }	

		for ($idx_column = 0; $idx_column < count($list_ids_procesos); $idx_column++ ){	
			$rowtramites = Doctrine::getTable('Tramite')->getDocumentosProcesoEstudios($list_ids_procesos[$idx_column]);
			foreach($rowtramites as $tramite){
                        	$comite="";

                        	foreach ($tramite->getValorDatoSeguimiento() as $tra_nro){
                        	        if($tra_nro->nombre == 'comite')
                        	                $comite = $tra_nro->valor;
                        	}

                        	if($comite){
                                	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($idx_column+1, $columns[$comite], "Enviado");
                        	}
			}
		}

		$object_writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="avance.xls"');
                $object_writer->save('php://output');
        }




  	//Funcion que muestra los documentos del estudio psicolaboral	
	public function descargardoc(){
		//Verificamos que el usuario ya se haya logeado 
        	if (!UsuarioSesion::usuario()->registrado) {
                	$this->session->set_flashdata('redirect', current_url());
                	redirect('tramites/disponibles');
       		}	

        	$idProcesoDocumentacion =proceso_estudio_documentacion_id;
        	$canDescargarDocEstudio=Doctrine::getTable('Proceso')->canDesargarDocEstudio(UsuarioSesion::usuario()->id);
		if($canDescargarDocEstudio){
                	$tramiteDoc     = Doctrine::getTable('Tramite')->getDocumentosProcesoEstudios($idProcesoDocumentacion);
                	$rowEtapas      = $tramiteDoc[0]->getEtapasTramites();
                	$sizeEtapas     = count($rowEtapas);
			//Nombre de los documentos dentro del formulario
			$url_formato_acta = null;
			$url_instructivo  = null;
			$url_instructivo_trabajadores = null;
			$url_registro_entrega_codigos = null;		
			$url_taller_trabajadores_mail = null;
			$url_taller_trabajadores_presencial= null;
			$url_registro_sensibilizacion=null;
			$url_registro_difusion=null;
			$url_carta_gantt=null;		
			$pos_formato = 0;
			for ($i = $sizeEtapas-1  ; $i >=0 ; $i--) {
				$etapa 	  = $rowEtapas[$i];
    				$paso = $etapa->getPasoEjecutable(0);
				foreach ($paso->Formulario->Campos as $c){
					$string = $c->displayConDatoSeguimiento($etapa->id, 'visualizacion');
					//Formato de acta
					if ((strpos($string, 'formato_acta') !== false)  && !$url_formato_acta ) {
						$s = "";
	                                	preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
						$url_formato_acta= $s['href'][0];
					}

					//Instructivo_aplicacion
					if ((strpos($string, 'instructivo_aplicacion') !== false)  && !$url_instructivo) {
                                	        $s = "";
                                	        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                	        $url_instructivo= $s['href'][0];
					}

					//Instructivo para trabajadores
                                	if ((strpos($string, 'instructivo_trabajadores') !== false)  && !$url_instructivo_trabajadores) {
                                	        $s = "";
                                	        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                        	$url_instructivo_trabajadores= $s['href'][0];
                                	}
				
					//Registro entrega de codigos
                                	if ((strpos($string, 'registro_entrega_codigos') !== false)  && !$url_registro_entrega_codigos){
                                        	$s = "";
                                        	preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                        	$url_registro_entrega_codigos= $s['href'][0];
                                	}
				
					//Taller informativo trabajadores
                                	if ((strpos($string, 'taller_trabajadores_mail') !== false)  && !$url_taller_trabajadores_mail) {
                                        	$s = "";
                                        	preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                        	$url_taller_trabajadores_mail= $s['href'][0];
                                	}

					//Taller informativo trabajadores
                                	if ((strpos($string, 'taller_trabajadores_mail') !== false)  && !$url_taller_trabajadores_presencial) {
                                	        $s = "";
                                	        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                	        $url_taller_trabajadores_presencial= $s['href'][0];
                                	}

					//Registro de sensibilizacion
					if ((strpos($string, 'registro_sensibilizacion') !== false)  && !$url_registro_sensibilizacion) {
                                                $s = "";
                                                preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                                $url_registro_sensibilizacion= $s['href'][0];
                                        }

					//Registro de difusion
                                        if ((strpos($string, 'registro_difusion') !== false)  && !$url_registro_difusion) {
                                                $s = "";
                                                preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                                $url_registro_difusion= $s['href'][0];
                                        }

					//Carta Gantt
                                        if ((strpos($string, 'carta_gantt') !== false)  && !$url_carta_gantt) {
                                                $s = "";
                                                preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                                $url_carta_gantt= $s['href'][0];
                                        }

				}
			}
		}
	
		$data['some_doc']=true;
		if(!$url_formato_acta && !$url_instructivo && !$url_instructivo_trabajadores && !$url_registro_entrega_codigos && !$url_taller_trabajadores_mail &&!$url_taller_trabajadores_presencial &&!$url_registro_sensibilizacion && !$url_registro_difusion && !$url_carta_gantt)
			$data['some_doc']=false;	
		$data['url_formato_acta'] = $url_formato_acta;
        	$data['url_instructivo']  = $url_instructivo;
		$data['url_instructivo_trabajadores']=$url_instructivo_trabajadores;
		$data['url_registro_entrega_codigos']=$url_registro_entrega_codigos;
 		$data['url_taller_trabajadores_mail']=$url_taller_trabajadores_mail;
 		$data['url_taller_trabajadores_presencial']=$url_taller_trabajadores_presencial;
		
		$data['url_registro_sensibilizacion']  = $url_registro_sensibilizacion;
		$data['url_registro_difusion']  = $url_registro_difusion;
		$data['url_carta_gantt']  = $url_carta_gantt;
	
		$data['sidebar']='documentos_estudio';
        	$data['content'] = 'estudios/descarga';
        	$data['title'] = 'Documentación estudio psicosocial';
        	$this->load->view('template', $data);

	}


}
