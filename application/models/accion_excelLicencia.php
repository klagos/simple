<?php
require_once('accion.php');
require_once('ChromePhp.php');
/*
Esta accion permite procesar un archivo excel. Este archivo contiene una serie de datos de las licencias medicas.
Cada fila se convierte en una instancia del proceso: "Subsidios"
Hay que verificar que en el valor de una licencia no se encuentre en otro proceso. 

*/

class AccionExcelLicencia extends Accion {

    public function displayForm() {	
	$display='<label>Archivo(para más de un archivo separar por comas) </label>';
        $display.='<input type="text" name="extra[adjunto]" value="' . (isset($this->extra->adjunto) ? $this->extra->adjunto : '') . '"/>';
        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[adjunto]', 'adjunto', 'required');
    }

    public function ejecutar(Etapa $etapa){		
	$regla=new Regla($this->extra->adjunto);
        $filename=$regla->getExpresionParaOutput($etapa->id);
        $file=Doctrine_Query::create()
                    ->from('File f, f.Tramite t')
                    ->where('f.filename = ? AND t.id = ?',array($filename,$etapa->Tramite->id))
                    ->fetchOne();
        if($file){
		$CI =& get_instance();
   		$CI->load->library('Excel');
		$inputFileName = 'uploads/datos/'.$filename;		
		
		//Load file
		$objPHPExcel=null; 
		try {
			log_message('info',"Load excel: ".$filename);
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
    			$objPHPExcel = $objReader->load($inputFileName);
		} catch(Exception $e) {
    			log_message('error',"Load excel: ".$filename." failed");
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}	
		//Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				
		//Column Trabajador
                $colTrabajadorNombre = 2;
                $colTrabajadorRut = 1;

                //Column Licencia
                $colLicenciaNumero = 3;
                $colLicenciaFechaInicio = 4;
                $colLicenciaFechaTermino = 5;
                $colLicenciaOrganismoSalud = 6;

		//Datos del proceso a insertar
		$idProceso = 2;
		//Read values
		log_message('info',"Read values");
		for ($row = 2; $row <= $highestRow; ++ $row){			
			/*Reviso si la licencia no fue agregada con anterioridad*/
			$cell = $sheet->getCellByColumnAndRow($colLicenciaNumero, $row);
                        $val  = $cell->getValue();
			if($val!=null){
				if(true){
					//TRAMITE
                                	$tramite=new Tramite();
                                	$tramite->iniciar($idProceso);
                                	$idEtapa = $tramite->getEtapasActuales()->get(0)->id;
										
					/**LICENCIA**/
					//numero
					if($val!=null){
						$datoLN = new DatoSeguimiento();
                                        	$datoLN->nombre = 'numero_licencia';
                                        	$datoLN->valor  = $val;
                                        	$datoLN->etapa_id=$idEtapa;
                                        	$datoLN->save();
					}
					//fecha inicio
					$cell = $sheet->getCellByColumnAndRow($colLicenciaFechaInicio, $row);			
					$val  = $cell->getValue();	
					if($val!=null){
						if(PHPExcel_Shared_Date::isDateTime($cell)){
							$val = PHPExcel_Style_NumberFormat::toFormattedString($val, 'DD-MM-YYYY');
							$datoLFI = new DatoSeguimiento();
                                        		$datoLFI->nombre = 'fecha_inicio_licencia';
                                        		$datoLFI->valor  = $val;
                                        		$datoLFI->etapa_id=$idEtapa;
                                        		$datoLFI->save();
						}
					}
					//fecha termino
					$cell = $sheet->getCellByColumnAndRow($colLicenciaFechaTermino,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                if(PHPExcel_Shared_Date::isDateTime($cell)){
                                                        $val = PHPExcel_Style_NumberFormat::toFormattedString($val, 'DD-MM-YYYY');
                                                        $datoLFT = new DatoSeguimiento();
                                                        $datoLFT->nombre = 'fecha_termino_licencia';
                                                        $datoLFT->valor  = $val;
                                                        $datoLFT->etapa_id=$idEtapa;
                                                        $datoLFT->save();
                                                }
                                        }
					//organismo de salud
					$cell = $sheet->getCellByColumnAndRow($colLicenciaOrganismoSalud,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                $datoLO = new DatoSeguimiento();
                                                $datoLO->nombre = 'organismo_salud_licencia';
                                                $datoLO->valor  = $val;
                                                $datoLO->etapa_id=$idEtapa;
                                                $datoLO->save();
                                        }
					
					/*TRABAJADOR*/
					//nombre
                                        $cell = $sheet->getCellByColumnAndRow($colTrabajadorNombre,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                $datoLTN = new DatoSeguimiento();
                                                $datoLTN->nombre = 'nombre_trabajador_subsidio';
                                                $datoLTN->valor  = $val;
                                                $datoLTN->etapa_id=$idEtapa;
                                                $datoLTN->save();
                                        }
					//rut
                                        $cell = $sheet->getCellByColumnAndRow($colTrabajadorRut,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                $datoLTR = new DatoSeguimiento();
                                                $datoLTR->nombre = 'rut_trabajador_subsidio';
                                                $datoLTR->valor  = $val;
                                                $datoLTR->etapa_id=$idEtapa;
                                                $datoLTR->save();
                                        }
					
					//$paso = $tramite->getEtapasActuales()->get(0)->getPasoEjecutable(0);
					$etapa->save();	
				}
				else{
					log_message('info',"Licencia agregada anteriormente");
				}		
			}
		}		

	}
	
    }
   
}
