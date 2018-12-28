<?php
require_once(FCPATH."procesos.php");
require_once('authorization.php');
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
		
		$oa = new Authorization();
        $token = $oa->getToken();
		//$url = "http://private-120a8-apisimpleist.apiary-mock.com/users";
        $url  = urlapi."users/list";

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json",
           "Authorization: Bearer ".$token ));
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

	public function reporte_requerimientos(){
		
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

	    $oa = new Authorization();
        $token = $oa->getToken();

	    $url = urlapi."request/report?&fi=".$fecha_inicial."&fe=".$fecha_final."&v=".$v."&a=".$a."&l=".$l;

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
        $json_ws = json_decode($result);

  	    $CI =& get_instance();
        $CI->load->library('Excel');
        $object = new PHPExcel();
	    	
	    $objWorkSheet = $object->createSheet(1);
        $objWorkSheet = $object->setActiveSheetIndex(1);
        $objWorkSheet->setTitle("Detalle");
		//LINEAS HORIZONTALES
	    $object->getDefaultStyle()->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $column = 0;
        $table_columns = array("","", "","","","","Licencias","Vacaciones","Dias Administrativos");
        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            if($field == 'Licencias'){
                $object->getActiveSheet()->mergeCells('G1:K1');
                $column= $column+5;
            }
            elseif($field == 'Vacaciones')  {
                $object->getActiveSheet()->mergeCells('L1:O1');
                $column= $column+4;
            }
    		elseif($field == 'Dias Administrativos'){
	    		$object->getActiveSheet()->mergeCells('P1:S1');
                $column= $column+4;
    		}
            else	
        	    $column++;
        }

        $column = 0;
 	    $mayor = 0;
        $table_columns = array("Rut","Apellidos","Nombres","Cargo","Centro Costo","Localidad","Fecha Inicio","Fecha Termino","Licencia","Total Dias","Utiles","Fecha inicio","Fecha termino","Total","Utiles","Fecha Inicio","Fecha Termino", "Total","Utiles" );
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
			$costCenter   = $json->costCenter;
			$location     = $json->location;
			$position	= $json->position;
			$array_json  	= $json->listRequestLicenses;
			$array_json2 	= $json->listRequestVacation;
			$array_json3  = $json->listRequestAdminDay;
	       	
		  	$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
			$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$lastName);	
			$object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$name);
			$object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$position);
			$object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$costCenter);
			$object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$location);
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
	                $object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$position);
					$object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$costCenter);
	                $object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$location);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(6,$excel_row, $initDate );
                    $object->getActiveSheet()->getStyle('G'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(7,$excel_row,$endDate);
                    $object->getActiveSheet()->getStyle('H'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(8,$excel_row,$number);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(9,$excel_row,$days);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(10,$excel_row,$utilDays);
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
					$object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$position);
					$object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$costCenter);
	                $object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$location);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(11,$excel_row, $initDate );
                    $object->getActiveSheet()->getStyle('L'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(12,$excel_row,$endDate);
                    $object->getActiveSheet()->getStyle('M'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(13,$excel_row,$requestDays);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(14,$excel_row,$requestUtilDays);
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
					$object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$position);
					$object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$costCenter);
          			$object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$location);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(15,$excel_row, $initDate );
                    $object->getActiveSheet()->getStyle('P'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(16,$excel_row,$endDate );
                    $object->getActiveSheet()->getStyle('Q'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(17,$excel_row,$requiredDays);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(18,$excel_row,$utilRequiredDays);
                    $excel_row++;
                }
			
			}
			$cont = $this->maxNum($row, $excel_row, $cont);
			$row = $cont;
			//	$excel_row++;
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
	    $object->getActiveSheet()->getColumnDimension('D')->setWidth(33);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(33);
	    $object->getActiveSheet()->getColumnDimension('F')->setWidth(33);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(13);
	    $object->getActiveSheet()->getColumnDimension('K')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('L')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('M')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('N')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('O')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('P')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('Q')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('R')->setWidth(13);

		//ALINEACION DE TITULOS
	    $object->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $object->getActiveSheet()->getStyle('A2:Z2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 //DIBUJO DE LAS LINEAS PARA LOS AÑOS
        $object->getActiveSheet()->getStyle('G'.$col.':K'.($excel_row+3))->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle('G'.$col.':O'.($excel_row+3))->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle('P'.$col.':S'.($excel_row+3))->applyFromArray($style_border);
	   
	    //SEGUNDA HOJA
        $objWorkSheet = $object->createSheet(0);
        $objWorkSheet = $object->setActiveSheetIndex(0);
        $objWorkSheet->setTitle("Resumen");
            //LINEAS HORIZONTALES
        $object->getDefaultStyle()->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
 	    $column = 0;
        $table_columns = array("", "","","","","","Licencias","Vacaciones","Dias Administrativos");
        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            if($field == 'Licencias'){
                $object->getActiveSheet()->mergeCells('G1:I1');
                $column= $column+3;
            }
            elseif($field == 'Vacaciones')  {
                $object->getActiveSheet()->mergeCells('J1:K1');
                $column= $column+2;
            }
            elseif($field == 'Dias Administrativos'){
                $object->getActiveSheet()->mergeCells('L1:M1');
                $column= $column+2;
            }
            else
                $column++;
        }
        $column = 0;
        $table_columns = array("Rut","Apellidos","Nombres","Cargo","Centro Costo","Localidad","Total","Util","Habiles","Total","Util","Total","Util");
        foreach($table_columns as $field){
            $object->getActiveSheet()->setCellValueByColumnAndRow($column,2, $field);
            $column++;
        }
        $excel_row = 3;
	    foreach ($json_ws  as $json){
			$rut             = $json->rut;
			$name            = $json->name;
			$lastName        = $json->lastName;
		    $costCenter      = $json->costCenter;
            $location 	   = $json->location;		
		    $position 	   = $json->position;	
		    $summaryLicense  = $json->summaryLicense;
		    $summaryVacation = $json->summaryVacation;				
		    $summaryAdminDay = $json->summaryAdminDay;	

			$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
			$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$lastName);
			$object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$name);
		    $object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$position);
			$object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$costCenter);
			$object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$location);

		
            if($summaryLicense!=null && !empty($summaryLicense) && $l=="true"){
				$object->getActiveSheet()->setCellValueByColumnAndRow(6,$excel_row,$summaryLicense[0]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(7,$excel_row,$summaryLicense[1]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(8,$excel_row,$summaryLicense[2]);
			}
			if($summaryVacation!=null && !empty($summaryVacation) && $v=="true"){
				$object->getActiveSheet()->setCellValueByColumnAndRow(9,$excel_row,$summaryVacation[0]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(10,$excel_row,$summaryVacation[1]);
			}
			if($summaryAdminDay!=null && !empty($summaryAdminDay) && $a=="true"){
				$object->getActiveSheet()->setCellValueByColumnAndRow(11,$excel_row,$summaryAdminDay[0]);
				$object->getActiveSheet()->setCellValueByColumnAndRow(12,$excel_row,$summaryAdminDay[1]);
			}
           $excel_row++;
        }
		//PERSONALIZACION DE 2DA HOJA

	    //BOLD
        $object->getActiveSheet()->getStyle('A1:AZ1')->getFont()->setBold( true );
        $object->getActiveSheet()->getStyle('A2:AZ2')->getFont()->setBold( true );
        //TAMAÑO DE LAS CELDAS
        $object->getActiveSheet()->getColumnDimension('A')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(22);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(27);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(33);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(32);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(28);
        $object->getActiveSheet()->getColumnDimension('G')->setWidth(11);
        $object->getActiveSheet()->getColumnDimension('H')->setWidth(11);
        $object->getActiveSheet()->getColumnDimension('I')->setWidth(11);
        $object->getActiveSheet()->getColumnDimension('J')->setWidth(11);
        $object->getActiveSheet()->getColumnDimension('K')->setWidth(11);
        $object->getActiveSheet()->getColumnDimension('L')->setWidth(11);
	    $object->getActiveSheet()->getColumnDimension('M')->setWidth(11);

            //ALINEACION DE TITULOS
        $object->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('A2:Z2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
             //DIBUJO DE LAS LINEAS PARA LOS AÑOS
        $object->getActiveSheet()->getStyle('G'.$col.':I'.($excel_row+3))->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle('J'.$col.':K'.($excel_row+3))->applyFromArray($style_border);
        $object->getActiveSheet()->getStyle('L'.$col.':M'.($excel_row+3))->applyFromArray($style_border);

	    $object->removeSheetByIndex(1); // ESTA LINEA SE USA PARA ELIMINAR LA HOJA QUE SE GENERA AUTOMATICAMENTE, FUNCIONAN COMO ARREGLOS. AHORA EXISTEN LAS HOJAS [0] Y [2] Y BORRO [1]
	    // SE GENERAL EXCEL 
	    $title = date("d-m-Y");
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel;charset=iso-8859-15');
        header('Content-Disposition: attachment;filename="reporte_solicitudes.xls"');
        ob_end_clean();
	    ob_start();
	    $object_writer->save('php://output');

	}


}
