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

class AccionValidarExcelLicencia extends Accion {

    public function displayForm() {	
	$display='<label>Archivo(para más de un archivo separar por comas) </label>';
        $display.='<input type="text" name="extra[adjunto]" value="' . (isset($this->extra->adjunto) ? $this->extra->adjunto : '') . '"/>';
	$display.='<label>(Para capturar la validación, debe crear una variable llamada is_excel_valid que se ejecute antes de la acción)</label>';
	$display.='<label>(Para capturar el mensaje de error, debe crear una variable llamada error_message que se ejecute antes de la acción)</label>';
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
	
		/* PASO 2 : PAGO SUBSIDIO */
		$col_Subsidio_Fecha = 0;
		$col_Subsidio_PagadoAnt = 8;
		$col_Subsidio_Anticipo  = 9;
		$col_Subsidio_Meses_ant = 10;
		$col_Subsidio_Dias	= 11;
		$col_Subsidio_Complement= 12;
		$col_Subsiodio_Observaci= 16;	

		/* PASO 3 : RETORNO SUBSIDIO */		
		$col_Retorno_fecha	= 13;
		$col_Retorno_pago	= 14;
		$col_Retorno_saldo	= 15;	

		//Datos del proceso a insertar	
		$idProceso = proceso_subsidio_id;

		//arreglo con los tipos validos de licencia
		$tipos_validos = array(1,2,3,4,5,6,7,8);

		//mensaje de error si el excel no es valido
		$error_message = "";
		$errors = array("No se encuentra el número de licencia en la fila ","La licencia "," tiene un error de formato en ", 
			" tiene una fecha de inicio posterior a la fecha de término");	

		//variable que valida el excel
		$validar = true;

		//Read values
		log_message('info',"Read values");
		for ($row = 2; $row <= $highestRow; ++ $row){			
			/*Reviso si existe el valor de la licencia*/
			$cell = $sheet->getCellByColumnAndRow($colLicenciaNumero, $row);
                        $val  = $cell->getValue();
			$numLicencia = $val;
			if($val!=null){
				
				//nombre trabajador
				$cell = $sheet->getCellByColumnAndRow($colTrabajadorNombre,$row);
                                $val  = $cell->getValue();
                                if($val!=null){
					$nombre_trabajador = $val;
					if (!is_string($nombre_trabajador)){
						$validar = false;
						if ($error_message == "") 
							$error_message = $errors[1].$numLicencia.$errors[2]."el nombre del trabajador";
					}
                                } else {
					$validar = false; 
					if ($error_message == "")
						$error_message = $errors[1].$numLicencia." no tiene el nombre del trabajador";
				}
				//fecha inicio
				$cell = $sheet->getCellByColumnAndRow($colLicenciaFechaInicio, $row);			
				$val  = $cell->getValue();
				$fecha_inicio="";	
				if($val!=null){
					if(PHPExcel_Shared_Date::isDateTime($cell)){
						$val = PHPExcel_Style_NumberFormat::toFormattedString($val, 'DD-MM-YYYY');
						$fecha_inicio = $val;
						if (!$this->validar_fecha($fecha_inicio)){
							$validar = false;
							if ($error_message == "") 
								$error_message = $errors[1].$numLicencia.$errors[2]."la fecha de inicio";
						}
					}
				} else {
                                        $validar = false;
                                        if ($error_message == "") 
						$error_message = $errors[1].$numLicencia." no tiene la fecha de inicio";
				}
	
				//fecha termino
				$cell = $sheet->getCellByColumnAndRow($colLicenciaFechaTermino,$row);
                                $val  = $cell->getValue();
				$fecha_termino="";
                                if($val!=null){
                                        if(PHPExcel_Shared_Date::isDateTime($cell)){
                                                $val = PHPExcel_Style_NumberFormat::toFormattedString($val, 'DD-MM-YYYY');
						$fecha_termino = $val;
						if (!$this->validar_fecha($fecha_termino)){
                                                        $validar = false;
                                                        if ($error_message == "") 
								$error_message = $errors[1].$numLicencia.$errors[2]."la fecha de término";
						}
                                        }
                                } else {
                                        $validar = false;
                                        if ($error_message == "") 
						$error_message = $errors[1].$numLicencia." no tiene la fecha de término";
				}
	
				//revisar que la fecha de término sea posterior a la de inicio
				if ($fecha_inicio!="" and $fecha_termino!=""){	
					$fecha_inicio  = new DateTime($fecha_inicio);
                               		$fecha_termino = new DateTime($fecha_termino);
					$dias_licencia = intval($fecha_termino->diff($fecha_inicio)->format("%a"))+1;
					if($fecha_inicio>$fecha_termino){
						$validar = false;
						if ($error_message == "")
							$error_message = $errors[1].$numLicencia.$errors[3];
                                       	}
				}

				//tipo de licencia
                                $cell = $sheet->getCellByColumnAndRow($colLicenciaTipo,$row);
                                $val  = $cell->getValue();
                                if($val!=null){
					$tipo_licencia = rtrim($val);
					if (!in_array($tipo_licencia,$tipos_validos)){
						$validar = false;
						if ($error_message == "") 
							$error_message = $errors[1].$numLicencia.$errors[2]."tipo de licencia";
					}
                                } else {
                                        $validar = false;
                                        if ($error_message == "")
						$error_message = $errors[1].$numLicencia." no tiene el tipo de licencia";
				}
		
				//rut
                                $cell = $sheet->getCellByColumnAndRow($colTrabajadorRut,$row);
                                $val  = $cell->getValue();
                                if($val!=null){
					$rut_trabajador = $val;
					if (!$this->validar_rut($rut_trabajador)){
						$validar = false;
						if ($error_message == "") 
							$error_message = $errors[1].$numLicencia.$errors[2]."el rut del trabajador";
					}
			        } else {
                                        $validar = false;
                                        if ($error_message == "")
						 $error_message = $errors[1].$numLicencia." no tiene el rut del trabajador";
				}

				//organismo de salud
                                $cell = $sheet->getCellByColumnAndRow($colLicenciaOrganismoSalud,$row);
                                $val  = $cell->getValue();
                                if($val!=null){
                                        $organismo_salud  = rtrim($val);
					if (!is_string($organismo_salud)){
                                                $validar = false;
                                                if ($error_message == "")
                                                        $error_message = $errors[1].$numLicencia.$errors[2]."el organismo de salud";
                                        }		
                                } else {
                                        $validar = false;
                                        if ($error_message == "")
                                                 $error_message = $errors[1].$numLicencia." no tiene el organismo de salud";
                                }

			} else {
				//$validar = false;
				//$error_message = $errors[0].$row;
			}
		if (!$validar) break;
			
		}	
		$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("is_excel_valid", $etapa->id);
        	if ($dato) {
                	$dato->valor = $validar;
                	$dato->save();
        	}

		$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("error_message", $etapa->id);
                if ($dato) {
                        $dato->valor = $error_message;
                        $dato->save();
                }
	}
    } 
    public function validar_rut($rut){
	/*validar formato del rut*/
	$match = 1;
	preg_match("/^[1-2]?[0-9]{1}\.[0-9]{3}\.[0-9]{3}-[0-9kK]{1}$/",$rut,$matches);
	if (!$matches){
		$match = 0;
		//match rut sin puntos
		preg_match("/^[1-2]?[0-9]{1}[0-9]{3}[0-9]{3}-[0-9kK]{1}$/",$rut,$matches);
		if (!$matches)
			return false;
	}
	
	/*sacar los puntos al rut*/
	if ($match)
		$rut = implode("",explode(".",$rut)); 
	
	/*obtener digito verificador a través del módulo 11*/
     	$s=1;
	$rutAux = $rut;
     	for($m=0;$rutAux!=0;$rutAux/=10)
        	$s=($s+$rutAux%10*(9-$m++%6))%11;
     	$dv = chr($s?$s+47:75);

	/*validar dígito verificador*/
	return (explode("-",$rut)[1] == $dv);
  }

    public function validar_fecha($date, $format = 'd-m-Y'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
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
