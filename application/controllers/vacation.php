<?php
require_once(FCPATH."procesos.php");
require_once('authorization.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Vacation extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function consultar(){
	

	  //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
        	$this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }		

        $oa = new Authorization();
        $token = $oa->getToken();
        
         
	    $json_ws = apcu_fetch('json_list_users_vacation');
    
        //if (!$json_ws){
        if (!$json_ws){
           
            //Obtener data de usuarios
            $url = urlapi . "users/list/small?parameter=name,lastname,location,rut";
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
            apcu_add('json_list_users_vacation',$json_ws,1800);
        }

    	$data['json_list_users'] = $json_ws;
        $data['title']='Consultar';
    	$data['sidebar']='vacation_consultar';
        $data['content'] = 'vacation/consultar';
        $data['token']=$token;
           
        $this->load->view('template', $data);
    }

    public function ajax_auditar_eliminar_tramite_vacation($tramite_id,$request_id,$rut){
        $tramite = Doctrine::getTable("Tramite")->find($tramite_id);
        $data['tramite'] = $tramite;
        $data['requerimiento'] = $request_id;
        $data['rut'] = $rut;
        $this->load->view ( 'vacation/ajax_auditar_eliminar_tramite_vacation', $data );
    }
  
    public function borrar_tramite_vacation($tramite_id,$request_id,$rut) {

                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }

                $this->form_validation->set_rules ( 'descripcion', 'Razón', 'required' );
		
                $respuesta = new stdClass ();
                if ($this->form_validation->run () == TRUE){

                        $tramite = Doctrine::getTable ( 'Tramite' )->find ( $tramite_id );
			
			$etapas         = $tramite->getEtapasTramites();
                        $countEtapas    = count($etapas); 
                        for($i = $countEtapas -1; $i >= 0; $i--){
                            $id = $etapas[$i]->id;
                            $d = Doctrine::getTable('DatoSeguimiento')->findByNombreEtapa("fecha_inicial",$id);
                            if($d!=false){
                            	$fi = $d["valor"];
				

                            }
                            $d = Doctrine::getTable('DatoSeguimiento')->findByNombreEtapa("fecha_final",$id);
                            if($d!=false){
                                $ff = $d["valor"]; 
			

                            }                
                        }

                        if($tramite!=null){

                            $oa = new Authorization();
                            $token = $oa->getToken();
                            $user_id = UsuarioSesion::usuario()->id;
				
                                //if($tramite->usuarioHaParticipado($user_id)){
                                        $fecha = new DateTime ();
                                        $proceso = $tramite->Proceso;
                                        // Auditar
                                        $registro_auditoria = new AuditoriaOperaciones ();
                                        $registro_auditoria->fecha = $fecha->format ( "Y-m-d H:i:s" );
                                        $registro_auditoria->operacion = 'Eliminación Request: '.$request_id.', Tramite: '.$tramite_id.', del Usuario: '.$rut .', Fecha Ini: '.$fi.', Fecha fin:'.$ff;
                                        $registro_auditoria->motivo = $this->input->post('descripcion');
						
                                        $registro_auditoria->usuario= UsuarioSesion::usuario()->nombres .' '. UsuarioSesion::usuario()->apellido_paterno.' '.UsuarioSesion::usuario()->apellido_materno.' '.UsuarioSesion::usuario()->email;

                                        $registro_auditoria->proceso = $proceso->nombre;
                                        $registro_auditoria->cuenta_id = 1;

                                        $tramite_array['proceso'] = $proceso->toArray(false);

                                        $tramite_array['tramite'] = $tramite->toArray(false);
                                        unset($tramite_array['tramite']['proceso_id']);


                                        $registro_auditoria->detalles = json_encode($tramite_array);

                                        $data = array();
                                        $url = urlapi."vacation/".$tramite_id."/user/".$rut."/deletevacationrequestbytramite";
                                        $ch = curl_init($url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                                        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
                                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                            "cache-control: no-cache",
                                           "Authorization: Bearer ".$token
                                        ));
                                        $response = curl_exec($ch);

                                        if ($response){
                                                $tramite->delete ();
                                                $registro_auditoria->save();
                                        }

                                        $respuesta->validacion = TRUE;
                                        $respuesta->redirect = site_url('vacation/consultar?rut='.$rut);

                                //}
				/*else{
					ChromePhp::log('El usuario no participo en el tramite');
				//El usuario no realizo esta solicitud
					$respuesta->validacion = FALSE;
                                	$respuesta->errores = validation_errors();
           				
                                }*/
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

    public function detail($tramite_id){
	 //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }

        $tramite = Doctrine::getTable("Tramite")->find($tramite_id);
        $etapa   = $tramite->getUltimaEtapa();
	$url = 'etapas/ver_sinpermiso/'.$etapa->id;
	redirect($url);
  }
  
 public function print_tramite($tramite_id){
     //Verificamos que el usuario ya se haya logeado 
    if (!UsuarioSesion::usuario()->registrado) {
            $this->session->set_flashdata('redirect', current_url());
            redirect('tramites/disponibles');
    }

    $tramite = Doctrine::getTable("Tramite")->find($tramite_id);
   if($tramite!=null){
    	$dato = Doctrine::getTable('file')->findOneByTipoAndTramite('documento',$tramite_id);
            $filename = $dato[0]->filename;
	$path = 'uploads/documentos/'.$filename;

	if(file_exists($path)){
		$this->load->helper('download');
		$data = file_get_contents ( $path );
		force_download ( $path, $data );	
	}else
		 ChromePhp::log("ERROR PATH");
	}
  }

	

  public function check_user($tramite_id){
	$tramite = Doctrine::getTable ( 'Tramite' )->find ( $tramite_id );
 	$user_id = UsuarioSesion::usuario()->id;
	$super_user = Doctrine::getTable('GrupoUsuarios')->hasUserGrupo(UsuarioSesion::usuario()->id,"vacation_super_admin");
	
	if($tramite->usuarioHaParticipado($user_id)  || $super_user)
		$data['result']=true;
	else{	
		$permisoLicencia = Doctrine::getTable('GrupoUsuarios')->cantGruposUsuaros(UsuarioSesion::usuario()->id,"MODULO_VACATION");
		$data['result']=($permisoLicencia==2)?true:false;
	}
	header("Content-Type: application/json");
	echo json_encode($data);
 }


 public function provision(){
	  //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }

 	$data['title']='Provision';
        $data['sidebar']='vacation_provision';
        $data['content'] = 'vacation/provision';

        $this->load->view('template', $data);

 }

 
 public function reporte_masivo(){
	 //Verificamos que el usuario ya se haya logeado 
	if (!UsuarioSesion::usuario()->registrado) {
        	$this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
        
        //Variables de la query
       	$proceso_id = proceso_vacation;
       	$contador = 0;
	$rowtramites = [];
	
	$rowtramites = Doctrine::getTable('Tramite')->getTramitesById($proceso_id);
	
	$CI =& get_instance();
        $CI->load->library('Excel');
        $object = new PHPExcel();

	$table_columns = array("TRAMITE","RUT","NOMBRE","FECHA INICIO","FECHA TERMINO","CANTIDAD DIAS");

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
                $fecha_inicio="";
		$fecha_termino="";
		$cantidad_dias="";

		$idTramite = $tramite['id'];
                $num_etapas = count($tramite["Etapas"]);
                for($i = 0; $i< $num_etapas ; $i++){
                        foreach ($tramite["Etapas"][$i]["DatosSeguimiento"] as $tra_nro){
					if($tra_nro["nombre"] == 'rut')
                                                $rut = str_replace('"','',$tra_nro["valor"]);
					
					if($tra_nro["nombre"] == 'nombre_trabajador')
                                                $nombre = str_replace('"','',$tra_nro["valor"]);
					
					if($tra_nro["nombre"] == 'fecha_inicial')
                                                $fecha_inicio = str_replace('"','',$tra_nro["valor"]);
				
					if($tra_nro["nombre"] == 'fecha_final')
                                                $fecha_termino = str_replace('"','',$tra_nro["valor"]);
					
					if($tra_nro["nombre"] == 'dias_requeridos')
                                                $cantidad_dias = str_replace('"','',$tra_nro["valor"]);
				}	
			}

		$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $idTramite);
		$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rut);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $nombre);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $fecha_inicio);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $fecha_termino);
               	$object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $cantidad_dias);
	
		$excel_row++;
	}
	
	$now	= new DateTime();
        $fecha	= $now->format('d-m-Y');

	$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="request_all_vacation_'.$fecha.'.xls"');
	header_remove('Set-Cookie');
        $object_writer->save('php://output');
 }



public function reporte(){
        
	 //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
	
    $oa = new Authorization();
    $token = $oa->getToken();
	
	$url = urlapi . "/users/list/vacationperiod";
        //$url = "https://www.api.nexoya.cl/users/list/vacationperiod";
	$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "cache-control: no-cache",
            "Authorization: Bearer ".$token
        ));
        $result=curl_exec($ch);
        curl_close($ch);
	$json_ws = json_decode($result);
        
	$CI =& get_instance();
        $CI->load->library('Excel');
        $object = new PHPExcel();
	
	$column = 0;
       	$table_columns = array("","","","","","","","Fecha de ingreso","2003 - 2004","2004 - 2005","2005 - 2006","2006 - 2007","2007 - 2008","2008 - 2009","2009 - 2010","2010 - 2011","2011 - 2012","2012 - 2013","2013 - 2014","2014 - 2015","2015 - 2016","2016 - 2017","2017 - 2018","2018 - 2019");
	foreach($table_columns as $field){
                $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
     		if($field == 'Fecha de ingreso'){
			$object->getActiveSheet()->mergeCells('H1:J1');
			$column= $column+4;
		}
		elseif($field == "2003 - 2004"){
			$object->getActiveSheet()->mergeCells('L1:M1');
			$column=$column+2;
		}
		elseif($field == "2004 - 2005"){
                        $object->getActiveSheet()->mergeCells('N1:O1');
                        $column=$column+2;
                }
		elseif($field == "2005 - 2006"){
                        $object->getActiveSheet()->mergeCells('P1:Q1');
                        $column=$column+2;
                }
		elseif($field == "2006 - 2007"){
                        $object->getActiveSheet()->mergeCells('R1:S1');
                        $column=$column+2;
                }
		elseif($field == "2007 - 2008"){
                        $object->getActiveSheet()->mergeCells('T1:U1');
                        $column=$column+2;
                }
		elseif($field == "2008 - 2009"){
                        $object->getActiveSheet()->mergeCells('V1:W1');
                        $column=$column+2;
                }
		elseif($field == "2009 - 2010"){
                        $object->getActiveSheet()->mergeCells('X1:Y1');
                        $column=$column+2;
                }
		elseif($field == "2010 - 2011"){
                        $object->getActiveSheet()->mergeCells('Z1:AA1');
                        $column=$column+2;
                }
		elseif($field == "2011 - 2012"){
                        $object->getActiveSheet()->mergeCells('AB1:AC1');
                        $column=$column+2;
                }
		elseif($field == "2012 - 2013"){
                        $object->getActiveSheet()->mergeCells('AD1:AE1');
                        $column=$column+2;
                }
		elseif($field == "2013 - 2014"){
                        $object->getActiveSheet()->mergeCells('AF1:AG1');
                        $column=$column+2;
                }
		elseif($field == "2014 - 2015"){
                        $object->getActiveSheet()->mergeCells('AH1:AI1');
                        $column=$column+2;
                }
		elseif($field == "2015 - 2016"){
                        $object->getActiveSheet()->mergeCells('AJ1:AK1');
                        $column=$column+2;
                }
		elseif($field == "2016 - 2017"){
                        $object->getActiveSheet()->mergeCells('AL1:AM1');
                        $column=$column+2;
                }
		elseif($field == "2017 - 2018"){
                        $object->getActiveSheet()->mergeCells('AN1:AO1');
                        $column=$column+2;
                }
		elseif($field == "2018 - 2019"){
                        $object->getActiveSheet()->mergeCells('AP1:AQ1');
                        $column=$column+2;
                }
		else
			$column++;
        }
	
		
	$column = 0;
	$table_columns = array("Rut","Tipo de contrato","Nombre","Apellido","Cargo","Area","Localidad","Dia","Mes","Anio","Antiguedad");	
	foreach($table_columns as $field){
                $object->getActiveSheet()->setCellValueByColumnAndRow($column,2, $field);
                $column++;
        }
	//DIBUJA LAS LINEAS HORIZONTALES DEL DOCUMENTO

	$object->getDefaultStyle()->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	//SET PERIOD
	$cantidad_periodos=16;
	for($i=0 ; $i<$cantidad_periodos;$i++){
		$object->getActiveSheet()->setCellValueByColumnAndRow($column,2, "Basico");
		$column++;
		$object->getActiveSheet()->setCellValueByColumnAndRow($column,2, "Progresivo");
                $column++;
	}
	
	//SET TOTAL
	$table_columns = array("Total Basicos","Total Progresivos","Total");
	foreach($table_columns as $field){
                $object->getActiveSheet()->setCellValueByColumnAndRow($column,2, $field);
                $column++;
        }
	
	//ALIGN CENTER TITLE  
        $object->getActiveSheet()->getStyle('A1:AZ1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$object->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$object->getActiveSheet()->getStyle('J2:AZ2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	//BOLD
	$object->getActiveSheet()->getStyle('A1:AZ1')->getFont()->setBold( true );
	$object->getActiveSheet()->getStyle('A2:AZ2')->getFont()->setBold( true ); 	
	
	$excel_row = 3;
        foreach ($json_ws  as $json){
		$rut 	= $json->rut;
		$name 	= $json->name;
		$lastName 	= $json->lastName;
		$typeContract 	= ($json->typeContract==1)?'Indefinido':(($json->typeContract==2)?'Plazo fijo':'Reemplazo');
		$position 	= $json->position;		
		$area		= $json->area;
		$localidad	= $json->location;		
		$fecha_contrato	= date('d-m-Y', ($json->contractDate)/1000); 
		$antiguedad 	= date("Y") - explode("-", $fecha_contrato)[2]; 		
		$total_basico	= $json->totalBasic;
		$total_progresivo=$json->totalProgressive;
			
		$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
		$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$typeContract);
		$object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$name);
		$object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$lastName);
		$object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$position);
		$object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$area);
		$object->getActiveSheet()->setCellValueByColumnAndRow(6,$excel_row,$localidad);
		$day_temp = explode("-", $fecha_contrato)[0];
		$object->getActiveSheet()->setCellValueByColumnAndRow(7,$excel_row,($day_temp<10)? (int)$day_temp:$day_temp);
		$month_temp = explode("-", $fecha_contrato)[1];
		$object->getActiveSheet()->setCellValueByColumnAndRow(8,$excel_row,($month_temp<10)? (int)$month_temp:$month_temp);
		$object->getActiveSheet()->setCellValueByColumnAndRow(9,$excel_row,explode("-", $fecha_contrato)[2]);
		$object->getActiveSheet()->setCellValueByColumnAndRow(10,$excel_row,$antiguedad);
		
		//ANALIZAR PERIODOS
		foreach ($json->vacationPeriod  as $period){
			$init_date 	= date('Y', ($period->initDate)/1000);
			$basic		= $period->basicAvailable;
			$progressive	= $period->progressiveAvailable;
			
			if($init_date=='2003'){
				$object->getActiveSheet()->setCellValueByColumnAndRow(11,$excel_row,$basic);
				$object->getActiveSheet()->setCellValueByColumnAndRow(12,$excel_row,$progressive);
			}
			elseif($init_date=='2004'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(13,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(14,$excel_row,$progressive);
                        }
			elseif($init_date=='2005'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(15,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(16,$excel_row,$progressive);
                        }
			elseif($init_date=='2006'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(17,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(18,$excel_row,$progressive);
                        }
			elseif($init_date=='2007'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(19,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(20,$excel_row,$progressive);
                        }
			elseif($init_date=='2008'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(21,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(22,$excel_row,$progressive);
                        }
			elseif($init_date=='2009'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(23,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(24,$excel_row,$progressive);
                        }
			elseif($init_date=='2010'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(25,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(26,$excel_row,$progressive);
                        }
			elseif($init_date=='2011'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(27,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(28,$excel_row,$progressive);
                        }
			elseif($init_date=='2012'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(29,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(30,$excel_row,$progressive);
                        }
			elseif($init_date=='2013'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(31,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(32,$excel_row,$progressive);
                        }
			elseif($init_date=='2014'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(33,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(34,$excel_row,$progressive);
                        }
			elseif($init_date=='2015'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(35,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(36,$excel_row,$progressive);
                        }
			elseif($init_date=='2016'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(37,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(38,$excel_row,$progressive);
                        }
			elseif($init_date=='2017'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(39,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(40,$excel_row,$progressive);
                        }
			elseif($init_date=='2018'){
                                $object->getActiveSheet()->setCellValueByColumnAndRow(41,$excel_row,$basic);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(42,$excel_row,$progressive);
                        }		
		}
		
		$object->getActiveSheet()->setCellValueByColumnAndRow(43,$excel_row,$total_basico);
		$object->getActiveSheet()->setCellValueByColumnAndRow(44,$excel_row,$total_progresivo);
		$object->getActiveSheet()->setCellValueByColumnAndRow(45,$excel_row,$total_progresivo+$total_basico);	

	
		$object->getActiveSheet()->getStyle('H'.$excel_row.':AZ'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$object->getActiveSheet()->getStyle('A'.$excel_row.':G'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);	
		$object->getActiveSheet()->getStyle('A'.$excel_row.':G'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);	
		$object->getActiveSheet()->getStyle('A'.$excel_row.':A'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);	
		
		$excel_row++;
	}
	//DIMENSIONAR LAS CELDAS AUTOMATICAMENTE

	foreach(range('A','D') as $columnID) {
	    $object->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
	}
	// SE AJUSTAN COLUMNAS CON TAMAÑO PERSONALIZADO
	$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
	$object->getActiveSheet()->getColumnDimension('F')->setWidth(35);
	$object->getActiveSheet()->getColumnDimension('G')->setWidth(30);
	$object->getActiveSheet()->getColumnDimension('AR')->setWidth(13);
	$object->getActiveSheet()->getColumnDimension('AS')->setWidth(15);
	$object->getActiveSheet()->getColumnDimension('K')->setWidth(11);

	// ESTILO PARA LOS BORDES DE LOS PERIODOS
 	$style_border = array( 
	  'borders' => array(
    		'outline' => array(
	      		'style' => PHPExcel_Style_Border::BORDER_MEDIUM
   			 )	
	 	 )
	);
	
 	 // SE DIBUJAN LAS LINEAS ENTRE LOS PERIODOS
	 $col = 1;
	 $object->getActiveSheet()->getStyle('L'.$col.':M'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('N'.$col.':O'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('P'.$col.':Q'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('R'.$col.':S'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('T'.$col.':U'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('V'.$col.':W'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('X'.$col.':Y'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('Z'.$col.':AA'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('AB'.$col.':AC'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('AD'.$col.':AE'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('AF'.$col.':AG'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('AH'.$col.':AI'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('AJ'.$col.':AK'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('AL'.$col.':AM'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('AN'.$col.':AO'.$excel_row)->applyFromArray($style_border);
	 $object->getActiveSheet()->getStyle('AP'.$col.':AQ'.$excel_row)->applyFromArray($style_border);

	// DIBUJO DE LAS LINEAS EN FECHA INGRESO
 	 $object->getActiveSheet()->getStyle('H'.$col.':J'.$excel_row)->applyFromArray($style_border);

	$title = date("d-m-Y");
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="vacation_reporte_'.$title.'".xls"');
        $object_writer->save('php://output');	
}
 




 public function request_descargar_asig(){
          //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
	$mes   =($this->input->get('mes'))?$this->input->get('mes'):null;
	
    $oa = new Authorization();
        $token = $oa->getToken();
    
	$url = urlapi . "/vacation/".$mes."/request";
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

	
	$table_columns = array("Rut","Dev. Basicos","Dev. Progresivos","Req. Basicos","Req. Progresivos","Festivos");

	$column = 0;

       foreach($table_columns as $field){
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                        $column++;
        }
        $excel_row = 2;
        foreach ($json_ws  as $json){
		 $rut  = $json->rut;
		 $dev_basico = $json->devBasic;
		 $dev_progre = $json->devProgresivo;

		$req_basico = $json->reqBasico;
		$req_progre = $json->reqProgresivo;
		$req_festivos=$json->reqHolidays;  
		
		$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$dev_basico);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$dev_progre);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$req_basico);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$req_progre);
		$object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$req_festivos);
		 $excel_row++;
	}
	
	$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="request_vacation".xls"');
        $object_writer->save('php://output');

        $data['sidebar']='vacation_provision';
        $data['content'] = 'vacation/provision';

        $this->load->view('template', $data);

 }



public function reporte_solicitudes(){

	    if (!UsuarioSesion::usuario()->registrado) {
                    $this->session->set_flashdata('redirect', current_url());
                    redirect('tramites/disponibles');
            }
            ChromePhp::log("ACA");
            $oa = new Authorization();
            $token = $oa->getToken();
            //$url = "https://www.api.nexoya.cl/users/list/vacationrequest";
            $url = urlapi . "/users/list/vacationrequest";
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

	    $objWorkSheet = $object->createSheet(0);
            $objWorkSheet = $object->setActiveSheetIndex(0);
	    $objWorkSheet->setTitle("Resumen");

            $column = 0;
            $table_columns = array("","","","","","","","Fecha de ingreso","","","","","","","","","");
            foreach($table_columns as $field){
                    $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                    if($field == 'Fecha de ingreso'){
                            $object->getActiveSheet()->mergeCells('H1:J1');
                            $column= $column+4;
                    }
                    elseif($field == "2010"){
                            $object->getActiveSheet()->mergeCells('K1');
                            $column=$column;
                    }
                    elseif($field == "2011"){
                            $object->getActiveSheet()->mergeCells('L1');
                            $column=$column;
                    }
                    elseif($field == "2012"){
                            $object->getActiveSheet()->mergeCells('M1');
                            $column=$column;
                    }
                    elseif($field == "2013"){
                            $object->getActiveSheet()->mergeCells('N2');
                            $column=$column;
                    }
                    elseif($field == "2014"){
                            $object->getActiveSheet()->mergeCells('O1');
                            $column=$column;
                    }
                    elseif($field == "2015"){
                            $object->getActiveSheet()->mergeCells('P1');
                            $column=$column;
                    }
                    elseif($field == "2016"){
                            $object->getActiveSheet()->mergeCells('Q1');
                    }
                     elseif($field == "2017"){
                            $object->getActiveSheet()->mergeCells('R1');
                            $column=$column;
                    }
                    elseif($field == "2018"){
                            $object->getActiveSheet()->mergeCells('S1');
                            $column=$column;
                    }
                   
                    else
                            $column++;
            }
            $column = 0;
            $table_columns = array("Rut","Tipo de contrato","Nombre","Apellido","Cargo","Area","Localidad","Dia","Mes","Año","2010","2011","2012","2013","2014","2015","2016","2017","2018","Total","Esperado","Diferencia");
            foreach($table_columns as $field){
                    $object->getActiveSheet()->setCellValueByColumnAndRow($column,2, $field);
                    $column++;
            }
            $excel_row = 3;
		

	$object->getDefaultStyle()->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		// ALINEACION DE TITULOS
	$object->getActiveSheet()->getStyle('T2:V2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('H2:J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('H1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


        foreach ($json_ws  as $json){
                  $rut    = $json->rut;
                  $name   = $json->name;
                  $lastName       = $json->lastName;
                  $typeContract   = ($json->typeContract==1)?'Indefinido':(($json->typeContract==2)?'Plazo fijo':'Reemplazo');
                  $position       = $json->position;
                  $area           = $json->area;
                  $localidad      = $json->location;
                  $fecha_contrato = date('d-m-Y', ($json->contractDate)/1000);
                  $periodo        = $json->totalRequestForYear;

                  $object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$typeContract);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$name);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$lastName);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$position);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$area);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(6,$excel_row,$localidad);
                  $day_temp = explode("-", $fecha_contrato)[0];
                  $object->getActiveSheet()->setCellValueByColumnAndRow(7,$excel_row,($day_temp<10)? (int)$day_temp:$day_temp);
                  $month_temp = explode("-", $fecha_contrato)[1];
                  $object->getActiveSheet()->setCellValueByColumnAndRow(8,$excel_row,($month_temp<10)? (int)$month_temp:$month_temp);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(9,$excel_row,explode("-", $fecha_contrato)[2]);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(10,$excel_row,($periodo[0]!=-1)?$periodo[0]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(11,$excel_row,($periodo[1]!=-1)?$periodo[1]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(12,$excel_row,($periodo[2]!=-1)?$periodo[2]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(13,$excel_row,($periodo[3]!=-1)?$periodo[3]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(14,$excel_row,($periodo[4]!=-1)?$periodo[4]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(15,$excel_row,($periodo[5]!=-1)?$periodo[5]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(16,$excel_row,($periodo[6]!=-1)?$periodo[6]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(17,$excel_row,($periodo[7]!=-1)?$periodo[7]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(18,$excel_row,($periodo[8]!=-1)?$periodo[8]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(19,$excel_row,($periodo[9]!=-1)?$periodo[9]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(20,$excel_row,($periodo[10]!=-1)?$periodo[10]:"-");
                  $object->getActiveSheet()->setCellValueByColumnAndRow(21,$excel_row,$periodo[11]);
                
                  $object->getActiveSheet()->getStyle('H'.$excel_row.':AZ'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $excel_row++;
            }
	
	//TIPO DE ESTIPO PARA APLICAR BORDES
	$style_border = array(
			'borders' => array(
				'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_MEDIUM	
			)
		)
	);
	$col = 1;
	// DIBUJO DE LINEAS EN FECHA INGRESO
	$object->getActiveSheet()->getStyle('H'.$col.':J'.$excel_row)->applyFromArray($style_border);
	// DIBUJO DE LINEAS solicitudes
	$object->getActiveSheet()->getStyle('T'.$col.':V'.$excel_row)->applyFromArray($style_border);
	//DIBUJO DE LAS LINEAS PARA LOS AÑOS
         $object->getActiveSheet()->getStyle('K'.$col.':L'.$excel_row)->applyFromArray($style_border);
         $object->getActiveSheet()->getStyle('L'.$col.':M'.$excel_row)->applyFromArray($style_border);
         $object->getActiveSheet()->getStyle('M'.$col.':N'.$excel_row)->applyFromArray($style_border);
         $object->getActiveSheet()->getStyle('N'.$col.':O'.$excel_row)->applyFromArray($style_border);
         $object->getActiveSheet()->getStyle('O'.$col.':P'.$excel_row)->applyFromArray($style_border);	
         $object->getActiveSheet()->getStyle('P'.$col.':Q'.$excel_row)->applyFromArray($style_border);
         $object->getActiveSheet()->getStyle('R'.$col.':S'.$excel_row)->applyFromArray($style_border);
         $object->getActiveSheet()->getStyle('S'.$col.':T'.$excel_row)->applyFromArray($style_border);
         $object->getActiveSheet()->getStyle('T'.$col.':U'.$excel_row)->applyFromArray($style_border);
	//BOLD
  	 $object->getActiveSheet()->getStyle('A1:AZ1')->getFont()->setBold( true );
         $object->getActiveSheet()->getStyle('A2:AZ2')->getFont()->setBold( true );
	//TAMAÑO DE LAS CELDAS
	 $object->getActiveSheet()->getColumnDimension('A')->setWidth(13);
         $object->getActiveSheet()->getColumnDimension('B')->setWidth(15);
         $object->getActiveSheet()->getColumnDimension('C')->setWidth(27);
         $object->getActiveSheet()->getColumnDimension('D')->setWidth(27);
         $object->getActiveSheet()->getColumnDimension('E')->setWidth(37);
         $object->getActiveSheet()->getColumnDimension('F')->setWidth(35);
         $object->getActiveSheet()->getColumnDimension('G')->setWidth(30);
         $object->getActiveSheet()->getColumnDimension('U')->setWidth(11);
         $object->getActiveSheet()->getColumnDimension('V')->setWidth(12);
	
 //SE GENERA  SEGUNDA HOJA
	$objWorkSheet = $object->createSheet(1);
        $objWorkSheet = $object->setActiveSheetIndex(1);
        $objWorkSheet->setTitle("Detalle");

 	$column = 0;
        $table_columns = array("","","","Solicitud","","","","","","","","","");
        foreach($table_columns as $field){
           $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
           if($field == 'Solicitud'){
               $object->getActiveSheet()->mergeCells('D1:F1');
               $column= $column+4;
              }else
                        $column++;
           }
             

  	$column = 0;
        $table_columns = array("Rut","Nombre","Apellido","Fecha inicio","Fecha termino","Total");
        foreach($table_columns as $field){
                 $object->getActiveSheet()->setCellValueByColumnAndRow($column,2, $field);
                 $column++;
            }
            
	$excel_row = 3;
	$object->getActiveSheet()->getStyle('D1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
	$object->getActiveSheet()->getStyle('D2:F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        foreach ($json_ws  as $json){
              	   $rut    = $json->rut;
                   $name   = $json->name;
                   $lastName       = $json->lastName;
                   $array_json = $json->vacationRequest;

                   $object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                   $object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$name);
                   $object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$lastName);

		if($array_json!=null && !empty($array_json)){		
		   	foreach($array_json as $json_request){
		
                        	    $initDate = date('d-m-Y',($json_request->initDate)/1000);
                        	    $endDate = date('d-m-Y',($json_request->endDate)/1000);
                        	    $totalRequest = $json_request->totalRequest;

                        	        $object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row, $initDate );	
					$object->getActiveSheet()->getStyle('D'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                        	        $object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$endDate);
					$object->getActiveSheet()->getStyle('E'.$excel_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                        	        $object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$totalRequest);
					$object->getActiveSheet()->getStyle('D'.$excel_row.':AZ'.$excel_row.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$excel_row++;
                  	}              
		}
                  $excel_row++;

         }

	$col = 1;
        // DIBUJO DE LINEAS EN FECHA INGRESO
        $object->getActiveSheet()->getStyle('D'.$col.':F'.$excel_row)->applyFromArray($style_border);
	
	//BOLD
        $object->getActiveSheet()->getStyle('A1:AZ1')->getFont()->setBold( true );
        $object->getActiveSheet()->getStyle('A2:AZ2')->getFont()->setBold( true );
        //TAMAÑO DE LAS CELDAS
        $object->getActiveSheet()->getColumnDimension('A')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('B')->setWidth(27);
        $object->getActiveSheet()->getColumnDimension('C')->setWidth(27);
        $object->getActiveSheet()->getColumnDimension('D')->setWidth(13);
        $object->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $object->getActiveSheet()->getColumnDimension('F')->setWidth(10);

	$object->removeSheetByIndex(2); //SE REMUEVE UNA 3ERA HOJA
	$title = date("d-m-Y");
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="reporte_solicitudes'.$title.'".xls"');
        $object_writer->save('php://output');


}
 public function provision_descargar(){
          //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
 
        $oa = new Authorization();
        $token = $oa->getToken();

        $mes   =($this->input->get('mes'))?$this->input->get('mes'):null;
	
        $url = urlapi . "/vacation/".$mes."/provision";
	ChromePhp::log($url);
        
$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"cache-control: no-cache",
                       "Authorization: Bearer ".$token
                ));
        $result=curl_exec($ch);
        curl_close($ch);

        $json_ws = json_decode($result);
        $CI =& get_instance();
        $CI->load->library('Excel');
        $object = new PHPExcel();


        $table_columns = array("Rut","Acum. Basic","Acum. Prog","Total Acum. Anterior","Provisionado Anterior","Req. Basicos","Req. Progresivos","Festivos","Total no trabajados","Acum. Basic actual","Acum. Progre actual", "Total Acum. Actual", "Provisionado Actual");

	$column = 0;

       foreach($table_columns as $field){
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                        $column++;
        }
        $excel_row = 2;
        foreach ($json_ws  as $json){
                
		$rut  = $json->rut;
		if($rut !="" ){
			//1ra parte
			$acum_basic = $json->acumuladoBasic;
			$acum_progre= $json->acumuladoProgresivo;
			$acum_total = $json->acumuladoTotal;
			$acum_function =$json->acumladoFunction;
		
			//2da parte
                	$req_basico = $json->reqBasico;
                	$req_progre = $json->reqProgresivo;
                	$req_festivos=$json->reqHolidays;
			$req_total   =$json->reqTotal;
		
			//3ra parte
			$acum_basic_ac = $json->acumuladoActualBasic;
			$acum_progre_ac= $json->acumuladoActualProgresivo;
			$acum_total_ac = $json->acumuladoActualTotal;
			$acum_function_ac = $json->acumladoActualFunction;
			
			//1ra parte
                	$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                	$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$acum_basic);
			$object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$acum_progre);
			$object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$acum_total);
			$object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$acum_function);
		
			//2da parte	
                	$object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$req_basico);
                	$object->getActiveSheet()->setCellValueByColumnAndRow(6,$excel_row,$req_progre);
                	$object->getActiveSheet()->setCellValueByColumnAndRow(7,$excel_row,$req_festivos);
			$object->getActiveSheet()->setCellValueByColumnAndRow(8,$excel_row,$req_total);
			
			//3ra parte
			$object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row,$acum_basic_ac );
			$object->getActiveSheet()->setCellValueByColumnAndRow(10,$excel_row,$acum_progre_ac);
			$object->getActiveSheet()->setCellValueByColumnAndRow(11,$excel_row,$acum_total_ac);
			$object->getActiveSheet()->setCellValueByColumnAndRow(12,$excel_row,$acum_function_ac);		

                	$excel_row++;
		}
        }

        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="provision_vacation".xls"');
        $object_writer->save('php://output');

        $data['sidebar']='vacation_provision';
        $data['content'] = 'vacation/provision';

        $this->load->view('template', $data);

}


//TODA SOLICITUD NO DESCARGADA
public function request_all_descargar(){
          //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
	
	$fecha_final =trim(($this->input->get('fecha_termino'))?$this->input->get('fecha_termino'):null);

        $oa = new Authorization();
        $token = $oa->getToken();

        $url = urlapi . "/vacation/".$fecha_final."/vacationrequestdownloaded";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "cache-control: no-cache",
                "Authorization: Bearer ".$token
                ));
        $result=curl_exec($ch);
        curl_close($ch);

        $json_ws = json_decode($result);
        $CI =& get_instance();
        $CI->load->library('Excel');
        $object = new PHPExcel();

        $table_columns = array("Rut","Name","HABILES","CORRIDOS","INICIO","TERMINO");

        $column = 0;

       foreach($table_columns as $field){
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                        $column++;
        }

	$excel_row = 2;
        foreach ($json_ws  as $json){
                 $rut  = $json->rut;
                 $name = $json->name;
                 $workday = $json->workDay;

                $totalday = $json->totalDay;
                $fecha_inicio  = $json->initDate;
                $fecha_termino = $json->endDate;

                $object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$workday);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$totalday);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$fecha_inicio);
                $object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$fecha_termino);
                 $excel_row++;
        }
	$now = new DateTime();
	$fecha = $now->format('d-m-Y');
	
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="request_all_vacation_'.$fecha.'.xls"');
        $object_writer->save('php://output');
 }

}
?>
