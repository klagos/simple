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
                apcu_add('json_list_users_vacation',$json_ws,1);
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
		ChromePhp::log($tramite_id);

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
				
                                if($tramite->usuarioHaParticipado($user_id)){
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

                                }else{
					ChromePhp::log('El usuario no participo en el tramite');
				//El usuario no realizo esta solicitud
					$respuesta->validacion = FALSE;
                                	$respuesta->errores = validation_errors();
           				
                                }
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
	else
		$data['result']=false;
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


 public function provision_diasSolicitados(){
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

	
	$table_columns = array("Rut","Dev. Basicos","Dev. Progresivos","Req. Basicos","Req. Progresivos");

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
		
		$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$dev_basico);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$dev_progre);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$req_basico);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$req_progre);
		 $excel_row++;
	}
	
	$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="provision_vacation".xls"');
        $object_writer->save('php://output');

        $data['sidebar']='vacation_provision';
        $data['content'] = 'vacation/provision';

        $this->load->view('template', $data);

 }





	

}
?>
