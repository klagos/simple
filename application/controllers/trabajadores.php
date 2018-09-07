<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trabajadores extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
		//Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
    	}
	
	//Vista para el generador
    	public function buscar(){
		//Verificamos que el usuario ya se haya logeado 
		if (!UsuarioSesion::usuario()->registrado) {
            		$this->session->set_flashdata('redirect', current_url());
            		redirect('tramites/disponibles');
        	}
		//Lista de usuarios
		$json_ws = apcu_fetch('json_list_users_editar');
        	if (!$json_ws){
                
                	$url = urlapi . "users/list/small?parameter=name,lastname";
                	$json_ws = $this->conectUrl($url);
                	apcu_add('json_list_users_editar',$json_ws,120);
        	}

      		$data['json_list_users'] = $json_ws;		
                $data['sidebar']='trabajadores_edit';
                $data['content'] = 'trabajadores/buscar';
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
	
	public function buscar_user (){
		$rut	=($this->input->get('rut_trabajador'))?$this->input->get('rut_trabajador'):null;
		$rut  	=str_replace('.','', $rut);
		$url    = urlapi."/users/dto/".$rut;	
		$json_usuario = $this->conectUrl($url);
		
		$json_usuario->name =ucwords(mb_strtolower($json_usuario->name,'UTF-8'));
                $json_usuario->lastName =ucwords(mb_strtolower($json_usuario->lastName,'UTF-8'));
		
		//Codigo localidad y centro
		$code_localidad = $json_usuario->locationCode;	
		$code_centro	= $json_usuario->costCenterCode;
		
		 //Lista de gerencias
                $json_gerencias = apcu_fetch('json_list_gerencias');          
                if(!$json_gerencias){
                        $url = "http://private-120a8-apisimpleist.apiary-mock.com/gerencias";
                        $json_gerencias = $this->conectUrl($url);
                        apcu_add('json_list_gerencias',$json_gerencias,120);
                }
		
		//Lista de localidad	
		$json_localidad = apcu_fetch('json_list_localidad');           
                if(!$json_localidad){
                        $url = urlapi."/location/list";
                        $json_localidad = $this->conectUrl($url);
                        apcu_add('json_list_localidad',$json_localidad,120);
                }
		
		//Localidad por defecto
		if($code_localidad!=""){
			foreach ($json_localidad as $item) {
   				if($item->code==$code_localidad){
					$json_usuario->location = $item->name.' - '.$item->code;
					break; 
				}
			}
		}
		
		$json_centro = apcu_fetch('json_list_centro'); 
                if(!$json_centro){
                        $url = urlapi."/costcenter/list";
                        $json_centro = $this->conectUrl($url);
                        apcu_add('json_list_centro',$json_centro,120);
                }

		//Centro de codigo por defecto
                if($code_centro!=""){
                        foreach ($json_centro as $item) {
                                if($item->code==$code_centro){
                                        $json_usuario->costCenter = $item->name.' - '.$item->code;
                                        break;
                                }
                        }
                }
	
		$data['json_gerencias'] = $json_gerencias;  	
		$data['json_usuario'] 	= $json_usuario;
		$data['json_localidad'] = $json_localidad;
		$data['json_centro']	= $json_centro;		
		
                $data['sidebar'] = 'trabajadores_edit';
                $data['content'] = 'trabajadores/resultado';
                $this->load->view('template', $data);	
	}

	/* EDITAR TODOS LOS  DATOS DEL PIE DE FIRMA*/
	public function update(){
				
		//Datos del formulario
		$rut            =($this->input->get('rut'))?$this->input->get('rut'):null;
                $nombre 	=($this->input->get('nombre'))?$this->input->get('nombre'):null;
		$apellido	=($this->input->get('apellido'))?$this->input->get('apellido'):null;
		$gender       	=($this->input->get('gender'))?$this->input->get('gender'):null;
		$birth_day	=($this->input->get('birth_day'))?$this->input->get('birth_day'):null;
		//Datos laborales
		$cargo          =($this->input->get('cargo'))?$this->input->get('cargo'):null;
		$email          =($this->input->get('email'))?$this->input->get('email'):null;
                $contract_date	=($this->input->get('contract_date'))?$this->input->get('contract_date'):null;
		$contractType	=($this->input->get('contractType'))?intval($this->input->get('contractType')):null;	
		$gerencia  	=($this->input->get('gerencia'))?$this->input->get('gerencia'):null;
		$localidad      =($this->input->get('localidad'))?$this->input->get('localidad'):null;
		$centro       	=($this->input->get('centro'))?$this->input->get('centro'):null;
		$celular	=($this->input->get('celular'))?intval($this->input->get('celular')):0;
		$anexo     	=($this->input->get('anexo'))?intval($this->input->get('anexo')):0;
		$codigo		=($this->input->get('codigo'))?intval($this->input->get('codigo')):0;		
		$active 	=($this->input->get('vinculacion'))?$this->input->get('vinculacion'):null;		
		
				
		//Personales
		$json   = new stdClass();
		$json->rut=trim($rut);
		$json->name=$nombre;
		$json->gender=$gender;
		$json->apellido=$apellido;
		$json->birth_day=$birth_day;
		
		//Laborales
		$json->position=$cargo;
                $json->email=$email;
                $json->contract_date=$contract_date;
                $json->management=$gerencia;
                $json->locationCode=trim(explode("-",$localidad)[1]);
		$json->costCenterCode=trim(explode("-",$centro)[1]);
		$json->phone=$celular;
		$json->annexPhone = $anexo;
		$json->contractType = $contractType;
		$json->areaCode=$codigo;
		$json->active = ($active==1)?true:false;		
		
		//Encode	
		$json = json_encode($json);
		$json = '['.$json.']';
		$this->update_user_api($json);
//		$this->buscar();

		$data['name'] = $nombre;
                $data['apellido']   = $apellido;

	
		$data['sidebar']='reporte';
                $data['content'] = 'trabajadores/editado';
                $data['title'] = 'Cambios Guardados';
                $this->load->view('template', $data);

	}

	public function update_user_api($json){
		
		//$url = "http://private-120a8-apisimpleist.apiary-mock.com/users";
                $url  = urlapi."users/list";

		$ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ));
                curl_exec($ch);
                curl_close($ch);
		
	}



	public function reporte(){
                 //Verificamos que el usuario ya se haya logeado
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

                $data['sidebar']='reporte';
                $data['content'] = 'trabajadores/reporte';
                $data['title'] = 'Reporte de Trabajadores';
                $this->load->view('template', $data);
        }


	public function maxNum($row, $excel_row, $cont){
		if($row >= $excel_row && $row >= $cont){
			return $row;	
		}elseif ($excel_row >= $row && $excel_row >= $cont){
			 return $excel_row;
		}
		elseif ($cont >= $row && $cont >= $excel_row ){
			 return $cont;
		}
		
	}

	public function reporte_licencias(){
		
	   //Verificamos que el usuario ya se haya logeado
            if (!UsuarioSesion::usuario()->registrado) {
                    $this->session->set_flashdata('redirect', current_url());
                    redirect('tramites/disponibles');
            }

	    $fecha_inicial   =trim(($this->input->get('fecha_inicial'))?$this->input->get('fecha_inicial'):null);
            $fecha_final     =trim(($this->input->get('fecha_termino'))?$this->input->get('fecha_termino'):null);
            $chbox           = $this->input->get('checkBox');

	    $v="false";
	    $a="false";
	    $l="false";
	    $cont = count($chbox);
	    for($i = 0; $i <$cont ; $i++){
		if($chbox[$i] == "v"){
			$v="true";
		}elseif ($chbox[$i]=="a"){
			$a="true";
		}elseif($chbox[$i] == "l"){
			$l="true";		
			}
	    }
	    $url = "https://www.api.nexoya.cl/request/report?&fi=".$fecha_inicial."&fe=".$fecha_final."&v=".$v."&a=".$a."&l=".$l; 
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

      	    $CI =& get_instance();
            $CI->load->library('Excel');
            $object = new PHPExcel();
	    	
	    $objWorkSheet = $object->createSheet(0);
            $objWorkSheet = $object->setActiveSheetIndex(0);
            $objWorkSheet->setTitle("Resumen");
		//LINEAS HORIZONTALES
	    $object->getDefaultStyle()->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $column = 0;
            $table_columns = array("", "","","Licencias","Vacaciones","Dias Administrativos");
            foreach($table_columns as $field){
                    $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                    if($field == 'Licencias'){
                            $object->getActiveSheet()->mergeCells('D1:H1');
                            $column= $column+5;
                    }
                    elseif($field == 'Vacaciones')  {
                            $object->getActiveSheet()->mergeCells('I1:L1');
                            $column= $column+4;
                    }
		    elseif($field == 'Dias Administrativos'){
			    $object->getActiveSheet()->mergeCells('M1:P1');
                            $column= $column+4;
		    }
                    else
                            $column++;
            }

            $column = 0;
     	    $mayor = 0;
            $table_columns = array("Rut","Apellidos","Nombres","Fecha Inicio","Fecha Termino","Licencia","Total Dias","Utiles","Fecha inicio","Fecha termino","Total","Utiles","Fecha Inicio","Fecha Termino", "Total","Utiles" );
            foreach($table_columns as $field){
                    $object->getActiveSheet()->setCellValueByColumnAndRow($column,2, $field);
                    $column++;
            }
            $excel_row = 3;
	    $row = 3;
            $cont= 3;
       	    foreach ($json_ws  as $json){
                  $rut    	= $json->rut;
                  $name   	= $json->name;
                  $lastName     = $json->lastName;
		  $array_json  	= $json->listRequestLicenses;
                  $array_json2 	= $json->listRequestVacation;
		  $array_json3  = $json->listRequestAdminDay;
		
		  $object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$lastName);	
                  $object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$name);
		     if($array_json!=null && !empty($array_json) && $l=="true"){
			$excel_row = $row;
                        foreach($array_json as $json_request){
                                    $number = $json_request->number;
                                    $initDate = date('d-m-Y',($json_request->initDate)/1000);
                                    $endDate = date('d-m-Y',($json_request->endDate)/1000);
                                    $days = $json_request->days;
                                    $utilDays = $json_request->utilDays;

					$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                  			$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$lastName);
			                $object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$name);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row, $initDate );
                                        $object->getActiveSheet()->getStyle('D'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$endDate);
                                        $object->getActiveSheet()->getStyle('E'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$number);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(6,$excel_row,$days);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(7,$excel_row,$utilDays);
				$excel_row++;
                        }
				
		}
		$cont = $this->maxNum($row, $excel_row, $cont); // FUNCION PARA OBTENER EL NUMERO MAYOR
		     if($array_json2!=null && !empty($array_json2) && $v=="true"){
			$excel_row = $row;
                        foreach($array_json2 as $json_request2){

                                    $initDate = date('d-m-Y',($json_request2->initDate)/1000);
                                    $endDate = date('d-m-Y',($json_request2->endDate)/1000);
                                    $requestDays = $json_request2->requestDays;
				    $requestUtilDays = $json_request2->requestUtilDays;	

					$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
			                $object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$lastName);
			                $object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$name);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(8,$excel_row, $initDate );
                                        $object->getActiveSheet()->getStyle('I'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(9,$excel_row,$endDate);
                                        $object->getActiveSheet()->getStyle('J'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(10,$excel_row,$requestDays);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(11,$excel_row,$requestUtilDays);
				$excel_row++;
                       	}
		}
		$cont = $this->maxNum($row, $excel_row, $cont);
		     if($array_json3!=null && !empty($array_json3) && $a =="true"){
			$excel_row = $row;
			 foreach($array_json3 as $json_request3){

                                    $initDate = date('d-m-Y',($json_request3->date)/1000);
                                    $endDate = ($json_request3->datefinal!=null)?date('d-m-Y',($json_request3->datefinal)/1000) : $initDate;
                                    $requiredDays = $json_request3->requiredDays;
                                    $utilRequiredDays = $json_request3->utilRequiredDays;

					$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                			$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$lastName);
			                $object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$name);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(12,$excel_row, $initDate );
                                        $object->getActiveSheet()->getStyle('M'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(13,$excel_row,$endDate );
                                        $object->getActiveSheet()->getStyle('N'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(14,$excel_row,$requiredDays);
                                        $object->getActiveSheet()->setCellValueByColumnAndRow(15,$excel_row,$utilRequiredDays);
                                $excel_row++;
                        }
			
		}
		$cont = $this->maxNum($row, $excel_row, $cont);
		$row = $cont;
		$excel_row++;
	    }
	// CONFIGURACION DE LA VISTA DE LA HOJA
		//TIPO DE ESTIPO PARA APLICAR BORDES
	    $style_border = array(
                        'borders' => array(
                                'outline' => array(
                                	'style' => PHPExcel_Style_Border::BORDER_MEDIUM

                	        )
            	    )
            );
	    $col = 0;
		//BOLD
            $object->getActiveSheet()->getStyle('A1:AZ1')->getFont()->setBold( true );
            $object->getActiveSheet()->getStyle('A2:AZ2')->getFont()->setBold( true );
		//TAMAÑO DE LAS CELDAS
            $object->getActiveSheet()->getColumnDimension('A')->setWidth(13);
            $object->getActiveSheet()->getColumnDimension('B')->setWidth(22);
            $object->getActiveSheet()->getColumnDimension('C')->setWidth(27);
 	    $object->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $object->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $object->getActiveSheet()->getColumnDimension('F')->setWidth(11);
            $object->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $object->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $object->getActiveSheet()->getColumnDimension('I')->setWidth(13);
	    $object->getActiveSheet()->getColumnDimension('J')->setWidth(14);
            $object->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $object->getActiveSheet()->getColumnDimension('L')->setWidth(10);
            $object->getActiveSheet()->getColumnDimension('M')->setWidth(13);
            $object->getActiveSheet()->getColumnDimension('N')->setWidth(14);
            $object->getActiveSheet()->getColumnDimension('O')->setWidth(10);
            $object->getActiveSheet()->getColumnDimension('P')->setWidth(10);

		//ALINEACION DE TITULOS
	    $object->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $object->getActiveSheet()->getStyle('A2:Z2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 //DIBUJO DE LAS LINEAS PARA LOS AÑOS
            $object->getActiveSheet()->getStyle('D'.$col.':H'.($excel_row+3))->applyFromArray($style_border);
            $object->getActiveSheet()->getStyle('I'.$col.':L'.($excel_row+3))->applyFromArray($style_border);
            $object->getActiveSheet()->getStyle('M'.$col.':P'.($excel_row+3))->applyFromArray($style_border);
	    
	    $object->removeSheetByIndex(1);
	    $title = date("d-m-Y");
            $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="reporteX'.$title.'".xls"');
            $object_writer->save('php://output');

	}


}
