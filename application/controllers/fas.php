<?php
require_once(FCPATH."procesos.php");
require_once('authorization.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class FAS extends MY_Controller {

    public function __construct() {
	
	
        parent::__construct();
    }

    public function consultar(){
	
	 if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
	
	$json_ws = apcu_fetch('json_list_users_fas');
        if (!$json_ws){
            $oa = new Authorization();
            $token = $oa->getToken();
                //Obtener data de usuarios
            $url = urlapi . "/users/list/smallfas?parameter=name,hasBCI,lastname,location,rut&bci=0";
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
                apcu_add('json_list_users_fas',$json_ws,60);
        }
	
	$data['json_list_users'] = $json_ws;
        $data['sidebar']= 'fas_consultar';
        $data['content']= 'fas/consultar';
        $data['title']  = 'Consulta de usuarios';
        $this->load->view('template', $data);
   }

   public function ajax_auditar_eliminar_tramite_fas($tramite_id,$request_id,$rut, $request){
        $tramite = Doctrine::getTable("Tramite")->find($tramite_id);
        $data['tramite'] = $tramite;
        $data['requerimiento'] = $request_id;
	$data['rut'] = $rut;
	$data['tipo'] = $request;
        $this->load->view ( 'fas/ajax_auditar_eliminar_tramite_fas', $data );
  }
  
  
  
  public function borrar_tramite_fas($tramite_id,$request_id,$rut,$tipo) {


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
                                        $oa = new Authorization();
                                        $token = $oa->getToken();

                                        $fecha = new DateTime ();
                                        $proceso = $tramite->Proceso;
                                        // Auditar
                                        $registro_auditoria = new AuditoriaOperaciones ();
                                        $registro_auditoria->fecha = $fecha->format ( "Y-m-d H:i:s" );
                                        $registro_auditoria->operacion = 'Eliminación beneficio con id request '.$request_id ;
                                        $registro_auditoria->motivo = $this->input->post('descripcion');

                                        $registro_auditoria->usuario= UsuarioSesion::usuario()->nombres .' '. UsuarioSesion::usuario()->apellido_paterno.' '.UsuarioSesion::usuario()->apellido_materno.' '.UsuarioSesion::usuario()->email;

                                        $registro_auditoria->proceso = $proceso->nombre;
                                        $registro_auditoria->cuenta_id = 1;

                                        $tramite_array['proceso'] = $proceso->toArray(false);

                                        $tramite_array['tramite'] = $tramite->toArray(false);
                                        unset($tramite_array['tramite']['proceso_id']);


                                        $registro_auditoria->detalles = json_encode($tramite_array);

                                        $data = array();
					                    $tipo = ($tipo=='social')?'deletesocialrequest':'deletemedicalrequest';	
                                        $url = urlapi."fas/".$request_id."/".$tipo;
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
                                        $respuesta->redirect = site_url('fas/consultar?rut='.$rut);

                                }else{
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

 public function consolidado(){
	 //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }

	$data['content']= 'fas/consolidado';
        $this->load->view('template', $data);
 }

public function generarconsolidado(){
    $fecha_inicial =($this->input->get('fecha_inicial'))?$this->input->get('fecha_inicial'):null;
    $fecha_final   =($this->input->get('fecha_final'))?$this->input->get('fecha_final'):null;

    $oa = new Authorization();
    $token = $oa->getToken();

    $url = urlapi . "/fas/".$fecha_inicial."/".$fecha_final."/benefitrequest";
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

	$table_columns = array("Rut","Fecha","Monto","Beneficio","Estado pago","Voucher","Tramite nexo");

	 $column = 0;

       foreach($table_columns as $field){
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                        $column++;
        }         
	$excel_row = 2;
	foreach ($json_ws  as $json){
                
                $rut  = $json->rut;
		$fecha = date("d-m-Y", $json->date / 1000); //$dt->format('d-m-Y');
		$monto= $json->value;
		$estado = ($json->paid)?'Pagado':'Pendiente';
		$voucher = $json->voucher;
		$tramite = $json->idTramite;
		$beneficio = $json->nameBenefit;    
                
                $object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$fecha);
		$object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$monto);
		$object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$beneficio);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$estado);
		$object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$voucher);
		$object->getActiveSheet()->setCellValueByColumnAndRow(6,$excel_row,$tramite);
                $excel_row++;
        }
        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="consolidado_fas".xls"');
        ob_end_clean();
	ob_start();
	$object_writer->save('php://output');


}



 public function generarpago(){
	 //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
	
    $oa = new Authorization();
    $token = $oa->getToken();
	$escolar =($this->input->get('escolar'))?$this->input->get('escolar'):null;
	
	$url = urlapi . "/fas/paidBenefit";
        if($escolar=='si')
		$url = $url.'?paidEscolar=true';
	

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

	$table_columns = array("Rut","Nombre","Monto");
	$column = 0;

       foreach($table_columns as $field){
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
                        $column++;
        }	
	
	$excel_row = 2;
	foreach ($json_ws  as $json){
		$name = $json->name.' '.$json->lastName;
		$rut  = $json->rut;
		$total= $json->total;
		
		$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
		$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$name);
		$object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$total);
	 	$excel_row++;
	}
	$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="reporte_pago".xls"');
        ob_end_clean();
	ob_start();
	$object_writer->save('php://output');
 }
 


}
?>
