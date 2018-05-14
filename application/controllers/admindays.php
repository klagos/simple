<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admindays extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function consultar(){
	
	  //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
        	$this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }		

                
	$json_ws = apcu_fetch('json_list_users_admin');
        if (!$json_ws){
                //Obtener data de usuarios
                $url = urlapi . "users/list/small/admindays";
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
                apcu_add('json_list_users_admin',$json_ws,1);
        }
	
	$data['json_list_users'] = $json_ws;
	$data['procesos']=Doctrine::getTable('Proceso')->findProcesosDisponiblesParaIniciar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(),'nombre','asc');
        $data['sidebar']='consultar_admin_days';
        $data['content'] = 'admindays/consultar';
        $data['title'] = 'Consulta los días administrativos';
        $this->load->view('template', $data);
   }

  public function ajax_auditar_eliminar_tramite_adminday($tramite_id,$request_id,$rut){
        $tramite = Doctrine::getTable("Tramite")->find($tramite_id);
        $data['tramite'] = $tramite;
        $data['requerimiento'] = $request_id;
	$data['rut'] = $rut;
        $this->load->view ( 'admindays/ajax_auditar_eliminar_tramite_adminday', $data );
  }
  
  public function borrar_tramite_adminday($tramite_id,$request_id,$rut) {


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
                                        $registro_auditoria->operacion = 'Eliminación dia administrativo con id request '.$request_id ;
                                        $registro_auditoria->motivo = $this->input->post('descripcion');

                                        $registro_auditoria->usuario= UsuarioSesion::usuario()->nombres .' '. UsuarioSesion::usuario()->apellido_paterno.' '.UsuarioSesion::usuario()->apellido_materno.' '.UsuarioSesion::usuario()->email;

                                        $registro_auditoria->proceso = $proceso->nombre;
                                        $registro_auditoria->cuenta_id = 1;

                                        $tramite_array['proceso'] = $proceso->toArray(false);

                                        $tramite_array['tramite'] = $tramite->toArray(false);
                                        unset($tramite_array['tramite']['proceso_id']);


                                        $registro_auditoria->detalles = json_encode($tramite_array);

                                        $data = array();
                                        $url = urlapi."users/".$request_id."/admindayrequest";
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
                                        $respuesta->redirect = site_url('admindays/consultar?rut='.$rut);

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
	

}
?>
