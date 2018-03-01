<?php
require_once('accion.php');
require_once('ChromePhp.php');
require_once(FCPATH."procesos.php");
/*
Esta accion permite procesar un archivo excel. Este archivo contiene una serie de datos de las licencias medicas.
Cada fila se convierte en una instancia del proceso: "Subsidios"
Hay que verificar que en el valor de una licencia no se encuentre en otro proceso. 
te
*/

class AccionEditarLicencia extends Accion {

    public function displayForm() {	
	$display='<label>Archivo(para más de un archivo separar por comas) </label>';
        $display.='<input type="text" name="extra[adjunto]" value="' . (isset($this->extra->adjunto) ? $this->extra->adjunto : '') . '"/>';
	$display.='<div class="help-block">
                Puede capturar diversos datos en variables <a href="#" onclick="$(this).siblings(\'pre\').show()">Ver ayuda</a><br />
                <pre style="display:none">
                Para capturar la cantidad de licencias editadas, debe crear una variable llamada licencias_editadas que se ejecute antes de la acción.
		Para capturar la cantidad de licencias que no cambiaron, debe crear una variable llamada licencias_no_editadas que se ejecute antes de la acción.
		Para capturar la cantidad de licencias que no se encontraban en el sistema, debe crear una variable llamada licencias_no_existentes que se ejecute antes de la acción.
		Para capturar los números de las licencias que no se encontraban en el sistema, debe crear una variable llamada array_licencias_no_existentes que se ejecute antes de la acción.
		Para capturar los campos a reemplazar, debe crear una variable llamada replace que sea una concatenación de 1s y 0s que representen si se capturó el campo o no.
		</pre>
                </div>';
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
                $colLicenciaFechaInicio = 5;
                $colLicenciaFechaTermino = 6;
                $colLicenciaOrganismoSalud = 7;
		$colLicenciaTipo=4;		
	

		//Datos del proceso a insertar	
		$idProceso = proceso_subsidio_id;

		//contadores
		$contLicenciasEditadas = 0;
		$contLicenciasNoExistentes = 0;	
		$contLicenciasNoEditadas = 0;

		//array con numero de licencias que ya existian
		$array_lic_rech = array();

		//flag para aumentar contLicenciasEditadas
		$flagCont = false;

		//Read values
		log_message('info',"Read values");
		for ($row = 2; $row <= $highestRow; ++ $row){		
			$flagCont = false;	
			/*Reviso si existe el valor de la licencia*/
			$cell = $sheet->getCellByColumnAndRow($colLicenciaNumero, $row);
                        $val  = $cell->getValue();
			if($val!=null){
			/*Reviso si la licencia no fue agregada con anterioridad*/
				if(!$this->verifyLicencia($val,$idProceso)){
					$contLicenciasNoExistentes++;
					//guardar num de licencia
					$array_lic_rech[] = $val;
				}
				else{
					log_message('info',"Licencia agregada anteriormente");
	
					$tramite = [];
	
					//variables de la query
					$proceso_id = proceso_subsidio_id;

                        		$numLicencia  = $val;
					
					if($numLicencia!=null){
                                        	$tramite = Doctrine::getTable('Tramite')->findLicencias($numLicencia,NULL,NULL,NULL, $proceso_id, NULL, NULL);					
						$ctramite = $tramite;	
					}
					$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("replace", $etapa->id);
				        if ($dato){
						//lista de flag para revisar si el campo correspondiente debe reemplazarse
				                $replace = str_split($dato->valor);	
						
						//reemplazar rut
						if ($replace[0]){
                                                        $cell = $sheet->getCellByColumnAndRow($colTrabajadorRut,$row);
                                                        $val  = $cell->getValue();
                                                        if($val!=null){
                                                                 $rut_trabajador = $val;
                                                                 $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("rut_trabajador_subsidio", $tramite[0]["Etapas"][0]["id"]);
                                                                  if ($dato->valor != $rut_trabajador){
                                                                                $dato->valor = $rut_trabajador;
                                                                                $dato->save();
                                                                                $flagCont = true;
                                                                        }
                                                        }
                                                }
	
						//reemplazar fecha de inicio
						if ($replace[1]){
							$cell = $sheet->getCellByColumnAndRow($colLicenciaFechaInicio, $row);
                        	        	        $val  = $cell->getValue();
                        	        	        $fecha_inicio="";
                        	        	        if($val!=null){
                        	        	                if(PHPExcel_Shared_Date::isDateTime($cell)){
                        	        	                        $val = PHPExcel_Style_NumberFormat::toFormattedString($val, 'DD-MM-YYYY');
                        	        	                        $fecha_inicio = $val;
									$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("fecha_inicio_licencia", $tramite[0]["Etapas"][0]["id"]);
									if ($dato->valor != $fecha_inicio){
										$dato->valor = $fecha_inicio;
										$dato->save();
										$flagCont = true;
									}
                        	        	                }
                        	        	        }
						}

						//reemplazar fecha de termino
						if ($replace[2]){	
							$cell = $sheet->getCellByColumnAndRow($colLicenciaFechaTermino, $row);
                                	        	$val  = $cell->getValue();
                                	        	$fecha_termino="";
                                        		if($val!=null){
                                        		        if(PHPExcel_Shared_Date::isDateTime($cell)){
                                        		                $val = PHPExcel_Style_NumberFormat::toFormattedString($val, 'DD-MM-YYYY');
                                        		                $fecha_termino = $val;
                                        		                $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("fecha_termino_licencia", $tramite[0]["Etapas"][0]["id"]);
                                        		                if ($dato->valor != $fecha_termino){
                                        		                        $dato->valor = $fecha_termino;
                                        		                        $dato->save();
										$flagCont = true;
                                        		                }
                                        		        }
                                        		}
						}

						//reemplazar tipo de licencia
						if ($replace[3]){
							$cell = $sheet->getCellByColumnAndRow($colLicenciaTipo,$row);
                                        		$val  = $cell->getValue();
                                        		if($val!=null){
                                               			 $tipo_licencia = $val;
								 $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("tipo_licencia", $tramite[0]["Etapas"][0]["id"]);
								 if ($dato->valor != $tipo_licencia){
                                                                                $dato->valor = $tipo_licencia;
                                                                                $dato->save();
                                                                                $flagCont = true;
                                                                       }
								
                                       			}
						}

						//reemplazar organismo de salud
						if ($replace[4]){
                                                        $cell = $sheet->getCellByColumnAndRow($colLicenciaOrganismoSalud,$row);
                                                        $val  = $cell->getValue();
                                                        if($val!=null){
                                                                 $organismo_salud = $val;
                                                                 $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("organismo_salud_licencia", $tramite[0]["Etapas"][0]["id"]);
                                                                  if ($dato->valor != $organismo_salud){
                                                                                $dato->valor = $organismo_salud;
                                                                                $dato->save();
                                                                                $flagCont = true;
                                                                        }
                                                        }
                                                }

						//reemplazar dias licencia si cambio alguna fecha
						if ($replace[1] or $replace[2]){
							$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("fecha_inicio_licencia", $tramite[0]["Etapas"][0]["id"]);
							$fecha_inicio  = new DateTime($dato->valor);

							$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("fecha_termino_licencia", $tramite[0]["Etapas"][0]["id"]);
	                                        	$fecha_termino = new DateTime($dato->valor);
        	                                	$dias_licencia = intval($fecha_termino->diff($fecha_inicio)->format("%a"))+1;;

                        	                        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("dias_licencia", $tramite[0]["Etapas"][0]["id"]);
                                        	        $dato->valor  = $dias_licencia;
                                                	$dato->save();
						}

		
						if ($flagCont) {
							$contLicenciasEditadas++;

							$tramite = Doctrine::getTable('Tramite')->findLicencias($numLicencia,NULL,NULL,NULL, $proceso_id, NULL, NULL);
						} else 
							$contLicenciasNoEditadas++;
					}
				}		
			}
		}		

	}
	
	//guardar cantidad de licencias editadas
	$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("licencias_editadas", $etapa->id);
   	if ($dato) {
		$dato->valor = $contLicenciasEditadas; 
		$dato->save();
	}

	//guardar cantidad de licencias que no se editaron
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("licencias_no_editadas", $etapa->id);
        if ($dato) {
                $dato->valor = $contLicenciasNoEditadas;
                $dato->save();
        }

	//guardar cantidad de licencias que no estan en el sistema
	$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("licencias_no_existentes", $etapa->id);
        if ($dato) {
		$dato->valor = $contLicenciasNoExistentes; 
		$dato->save();
	}

	//guardar array de los numeros de licencias que no estan en el sistema
	$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("array_licencias_no_existentes", $etapa->id);
	if ($dato){
		$dato->valor =  $array_lic_rech;
		$dato->save();
	}
	
    } 

    /* El metodo verifica si la licencia ya esta ingresada al sistema*/
    public function verifyLicencia($licencia,$proceso){ 
	$query= Doctrine_Query::create()
        	->from('Tramite t, t.Proceso p, t.Etapas e, e.DatosSeguimiento d')
                ->where('p.activo=1 AND p.id = ?', array($proceso))
		->andWhere("d.nombre = 'numero_licencia' AND d.valor LIKE ?",$licencia);
		
	$row = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	return (count($row)!=0)?true:false;
    }
   
}
