<?php
require_once(FCPATH."procesos.php");
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

                
	$json_ws = apcu_fetch('json_list_users_vacation');
        if (!$json_ws){
                //Obtener data de usuarios
                $url = urlapi . "users/list/small?parameter=name,lastname,location,rut";
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
                apcu_add('json_list_users_vacation',$json_ws,1800);
        }
	
	$data['json_list_users'] = $json_ws;
        $data['title']='Consultar';
	$data['sidebar']='vacation_consultar';
        $data['content'] = 'vacation/consultar';
       
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

                        if($tramite!=null){
                                $user_id = UsuarioSesion::usuario()->id;
				
                                //if($tramite->usuarioHaParticipado($user_id)){
                                        $fecha = new DateTime ();
                                        $proceso = $tramite->Proceso;
                                        // Auditar
                                        $registro_auditoria = new AuditoriaOperaciones ();
                                        $registro_auditoria->fecha = $fecha->format ( "Y-m-d H:i:s" );
                                        $registro_auditoria->operacion = 'Eliminación vacaciones con id request '.$request_id ;
                                        $registro_auditoria->motivo = $this->input->post('descripcion');

                                        $registro_auditoria->usuario= UsuarioSesion::usuario()->nombres .' '. UsuarioSesion::usuario()->apellido_paterno.' '.UsuarioSesion::usuario()->apellido_materno.' '.UsuarioSesion::usuario()->email;

                                        $registro_auditoria->proceso = $proceso->nombre;
                                        $registro_auditoria->cuenta_id = 1;

                                        $tramite_array['proceso'] = $proceso->toArray(false);

                                        $tramite_array['tramite'] = $tramite->toArray(false);
                                        unset($tramite_array['tramite']['proceso_id']);


                                        $registro_auditoria->detalles = json_encode($tramite_array);

                                        $data = array();
                                        $url = urlapi."vacation/".$tramite_id."/deletevacationrequest";
                                        $ch = curl_init($url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                                        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));

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

  public function check_user($tramite_id){
	$tramite = Doctrine::getTable ( 'Tramite' )->find ( $tramite_id );
 	$user_id = UsuarioSesion::usuario()->id;
	if($tramite->usuarioHaParticipado($user_id)) 
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


 public function request_descargar(){
          //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
	
	$mes   =($this->input->get('mes'))?$this->input->get('mes'):null;
	
	$url = urlapi . "/vacation/".$mes."/request";
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

 public function provision_descargar(){
          //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }

        $mes   =($this->input->get('mes'))?$this->input->get('mes'):null;

        $url = urlapi . "/vacation/".$mes."/provision";
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

public function request_all_descargar(){
          //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }


        $url = urlapi . "/vacation/vacationrequestdownloaded";
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
