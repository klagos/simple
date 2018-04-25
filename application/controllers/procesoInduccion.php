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
	

	public function resumen(){
		 //Verified login
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
                
		$CI =& get_instance();
                $CI->load->library('Excel');
                $objPHPExcel=null;

		$inputFileName = "uploads/resources/procesoInduccion/nomina.xlsx";
		
		try {
                        log_message('info',"Load excel: ".$inputFileName);
                        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFileName);
                } catch(Exception $e) {
                        log_message('error',"Load excel: ".$filename." failed");
                        die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                }

		$num_rows    = 219;
                $num_columns = 8;

		//diccionario donde keys son el nombre de las columnas y values el numero de columna   
		$sheet = $objPHPExcel->getSheet(0); 
                $columns = [];
                for ($i = 2; $i < $num_rows + 1; $i ++){
                        $cell = $sheet->getCellByColumnAndRow(0, $i);
                        $val  = trim($cell->getValue());
                        $columns[$val] = $i;
                }

		$id_proceso = proceso_induccion_id;
		
		//Leemos todos los tramites del proceso
		$rowtramites = Doctrine::getTable('Tramite')->getDocumentosProcesoEstudios($id_proceso);
        	foreach($rowtramites as $tramite){
            		$rut_trabajdor = false;
			$nombre_responsable = false;

       			foreach ($tramite->getValorDatoSeguimiento() as $tra_nro){
        			if($tra_nro->nombre == 'rut_trabajador')
                			$rut_trabajdor = $tra_nro->valor;

				if($tra_nro->nombre == 'nombre_responsable')
					$nombre_responsable = $tra_nro->valor;
        		}

            		if($rut_trabajdor && isset($columns[$rut_trabajdor])){
                		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($num_columns-1, $columns[$rut_trabajdor],"Sí");
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($num_columns, $columns[$rut_trabajdor], $nombre_responsable);
            		}
        	}
		
		//Descargar excel 	
		$object_writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="resumen.xls"');
                $object_writer->save('php://output');	
	
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
