<?php
require_once('accion.php');
require_once('ChromePhp.php');
/*
Esta accion permite procesar un archivo excel. Este archivo contiene una serie de datos de las licencias medicas.
Cada fila se convierte en una instancia del proceso: "Subsidios"
Hay que verificar que en el valor de una licencia no se encuentre en otro proceso. 
te
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
		
		/* PASO 1 : INGRESAR LICENCIA */		
		//Column Trabajador
                $colTrabajadorNombre = 2;
                $colTrabajadorRut = 1;

                //Column Licencia
                $colLicenciaNumero = 3;
                $colLicenciaFechaInicio = 4;
                $colLicenciaFechaTermino = 5;
                $colLicenciaOrganismoSalud = 6;
		
		/* PASO 2 : PAGO SUBSIDIO */
		$col_Subsidio_Fecha = 0;
		$col_Subsidio_PagadoAnt = 7;
		$col_Subsidio_Anticipo  = 8;
		$col_Subsidio_Meses_ant = 9;
		$col_Subsidio_Dias	= 10;
		$col_Subsidio_Complement= 11;
		$col_Subsiodio_Observaci= 15;	

		/* PASO 3 : RETORNO SUBSIDIO */		
		$col_Retorno_fecha	= 12;
		$col_Retorno_pago	= 13;
		$col_Retorno_saldo	= 14;	

		//Datos del proceso a insertar
		$idProceso = 2;
		
		//Read values
		log_message('info',"Read values");
		for ($row = 2; $row <= $highestRow; ++ $row){			
			/*Reviso si existe el valor de la licencia*/
			$cell = $sheet->getCellByColumnAndRow($colLicenciaNumero, $row);
                        $val  = $cell->getValue();
			if($val!=null){
			/*Reviso si la licencia no fue agregada con anterioridad*/
				if(!$this->verifyLicencia($val,$idProceso)){
					//TRAMITE
                                	$tramite=new Tramite();
                                	$tramite->iniciar($idProceso);
                                	
					/* PASO 1: INGRESO LICENCIA */
					$idEtapa = $tramite->getEtapasActuales()->get(0)->id;
					$etapaIngreso = $tramite->getEtapasActuales()->get(0);
										
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
                                                $datoLO->valor  = rtrim($val);
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
					 $datoLTA = new DatoSeguimiento();
                                         $datoLTA->nombre = 'ingreso_continuidad';
                                         $datoLTA->valor  = 'avanzar';
                                         $datoLTA->etapa_id=$idEtapa;
                                         $datoLTA->save();		
					
					//Cerramos la etapa y la avanzamos					
					$etapaIngreso->avanzar();


					/* PASO 2: PAGO SUBSIDIO */

					//Reviso que exista al menos un parametro
					$idEtapa	= $tramite->getEtapasActuales()->get(0)->id;
                                        $etapaPago 	= $tramite->getEtapasActuales()->get(0);
					

					//fecha
					$cell = $sheet->getCellByColumnAndRow($col_Subsidio_Fecha,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
						$val = PHPExcel_Style_NumberFormat::toFormattedString($val, 'DD-MM-YYYY');
                                                $datoLSF = new DatoSeguimiento();
                                                $datoLSF->nombre = 'fecha_pago_subsidio';
                                                $datoLSF->valor  = $val;
                                                $datoLSF->etapa_id=$idEtapa;
                                                $datoLSF->save();
                                        }
					//pago anterior
					$cell = $sheet->getCellByColumnAndRow($col_Subsidio_PagadoAnt,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                $datoLSPA = new DatoSeguimiento();
                                                $datoLSPA->nombre = 'pagado_anterior_subsidio';
                                                $datoLSPA->valor  = $val;
                                                $datoLSPA->etapa_id=$idEtapa;
                                                $datoLSPA->save();
                                        }
					//anticipo subsidio
					$cell = $sheet->getCellByColumnAndRow($col_Subsidio_Anticipo,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                $datoLSPAN = new DatoSeguimiento();
                                                $datoLSPAN->nombre = 'anticipo_subsidio';
                                                $datoLSPAN->valor  = $val;
                                                $datoLSPAN->etapa_id=$idEtapa;
                                                $datoLSPAN->save();
                                        }
					//meses anteriores
                                        $cell = $sheet->getCellByColumnAndRow($col_Subsidio_Meses_ant,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                $datoLSPMA = new DatoSeguimiento();
                                                $datoLSPMA->nombre = 'meses_anteriores_subsidio';
                                                $datoLSPMA->valor  = $val;
                                                $datoLSPMA->etapa_id=$idEtapa;
                                                $datoLSPMA->save();
                                        }
					//dias no cubiertos
                                        $cell = $sheet->getCellByColumnAndRow($col_Subsidio_Dias,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                $datoLSPD = new DatoSeguimiento();
                                                $datoLSPD->nombre = 'dias_no_cubiertos_subsidio';
                                                $datoLSPD->valor  = $val;
                                                $datoLSPD->etapa_id=$idEtapa;
                                                $datoLSPD->save();
                                        }
					//Complemento
					$cell = $sheet->getCellByColumnAndRow($col_Subsidio_Complement,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                $datoLSPC = new DatoSeguimiento();
                                                $datoLSPC->nombre = 'complemento_subsidio';
                                                $datoLSPC->valor  = $val;
                                                $datoLSPC->etapa_id=$idEtapa;
                                                $datoLSPC->save();
                                        }
					//Observacion
                                        $cell = $sheet->getCellByColumnAndRow($col_Subsiodio_Observaci,$row);
                                        $val  = $cell->getValue();
                                        if($val!=null){
                                                $datoLSPO = new DatoSeguimiento();
                                                $datoLSPO->nombre = 'observacion_pago_sub';
                                                $datoLSPO->valor  = $val;
                                                $datoLSPO->etapa_id=$idEtapa;
                                                $datoLSPO->save();
                                        }

					$datoLTP = new DatoSeguimiento();
                                        $datoLTP->nombre = 'pago_continuidad';
                                        $datoLTP->valor  = 'avanzar';
                                        $datoLTP->etapa_id=$idEtapa;
                                        $datoLTP->save();

					//Cerramos la etapa y la avanzamos                                      
                                        $etapaPago->avanzar();	
					
					/* PASO 3: RETORNO SUBSIDIO */
					
					$cell 	= $sheet->getCellByColumnAndRow($col_Retorno_fecha,$row);
                                        $fecha  = $cell->getValue();
					
					$cell	= $sheet->getCellByColumnAndRow($col_Retorno_pago,$row);
                                        $retorno= $cell->getValue();

					$cell   = $sheet->getCellByColumnAndRow($col_Retorno_saldo,$row);
                                        $saldo	= $cell->getValue();
					
					//Reviso que exista al menos un parametro
					if($fecha!=null ||  $retorno!=null || $saldo!=null){
						$idEtapa        = $tramite->getEtapasActuales()->get(0)->id;
                                        	$etapaRetorno   = $tramite->getEtapasActuales()->get(0);
						
						 if($fecha!=null){
                                                	$val = PHPExcel_Style_NumberFormat::toFormattedString($fecha, 'DD-MM-YYYY');
                                                	$datoRSF = new DatoSeguimiento();
                                                	$datoRSF->nombre = 'fecha_retorno_subsidio';
                                                	$datoRSF->valor  = $val;
                                                	$datoRSF->etapa_id=$idEtapa;
                                                	$datoRSF->save();
                                        	}

						if($retorno!=null){
                                                        $datoRSR = new DatoSeguimiento();
                                                        $datoRSR->nombre = 'monto_retorno_subsidio';
                                                        $datoRSR->valor  = $retorno;
                                                        $datoRSR->etapa_id=$idEtapa;
                                                        $datoRSR->save();
                                                }

						if($saldo!=null){
                                                        $datoRSS = new DatoSeguimiento();
                                                        $datoRSS->nombre = 'saldo_retorno_subsidio';
                                                        $datoRSS->valor  = $saldo;
                                                        $datoRSS->etapa_id=$idEtapa;
                                                        $datoRSS->save();
                                                }
						$datoLTP = new DatoSeguimiento();
                                        	$datoLTP->nombre = 'retorno_continuidad';
                                        	$datoLTP->valor  = 'cerrar';
                                        	$datoLTP->etapa_id=$idEtapa;
                                        	$datoLTP->save();
						
						//Cerramos la etapa y la avanzamos                                      
                                        	$etapaRetorno->avanzar();
						
					}
					
				}
				else{
					log_message('info',"Licencia agregada anteriormente");
				}		
			}
		}		

	}	
    } 

    /* El metodo verifica si la licencia ya esta ingresada al sistema*/
    public function verifyLicencia($licencia,$proceso){ 
	$query= Doctrine_Query::create()
        	->from('Tramite t, t.Proceso p, t.Etapas e, e.DatosSeguimiento d')
                ->where('p.activo=1 AND p.id = ?', array($proceso))
		->andWhere("d.nombre = 'numero_licencia' AND d.valor LIKE ?",'%'.$licencia.'%');
	return ($query->count()!=0)?true:false;
    }
   
}
