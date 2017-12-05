<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use Httpful\Request;

class DiaFeriado extends CI_Controller {
    private $base_services='';
    private $context='';

    public function __construct() {
        parent::__construct();
        UsuarioManagerSesion::force_login();
        require_once APPPATH . 'third_party/httpful/bootstrap.php';
        $this->base_services=$this->config->item('base_service');
        $this->context=$this->config->item('context_service');
        $agendaTemplate = Request::init()
                ->expectsJson()
                ->addHeaders(array(
                    'appkey' => $this->config->item('appkey')
                ));
        Request::ini($agendaTemplate);
    }

    public function index() {
        $data['cuentas']='';
        $data['title']='D&iacute;as Feriados';
        $data['content']='manager/diaferiado/index';        
        $this->load->view('manager/template',$data);
    }

    public function EmptyCalendar(){
        $var='{"success": 1,"result": []}';
        echo $var;
    }
    public function diasFeriados(){
        $code=0;
        $mensaje='';
        $data=array();
        //$year=(isset($_GET['year']) && is_numeric($_GET['year']) && $_GET['year']>0 )?$_GET['year']:date('Y');
        try{
            $uri=$this->base_services.''.$this->context.'daysOff';
            log_message('debug', 'diasFeriados URI '.$uri);
            $response = Request::get($uri)->sendIt();
            log_message('debug', 'diasFeriados Response '.$response);
            if(isset($response->body) && is_array($response->body) && isset($response->body[0]->response->code)){
                $code=$response->code;
                $code=$response->body[0]->response->code;
                $mensaje=$response->body[0]->response->message;
                foreach($response->body[1]->daysoff as $item){
                    $tmp=date('d-m-Y',strtotime($item->date_dayoff));
                    $data[]=array('date_dayoff'=>$tmp,'name'=>$item->name,'id'=>$item->id);
                }
            }
        }catch(Exception $err){
            log_message('error', 'diasFeriados '.$err);
            $mensaje=$err->getMessage();
        }
        $array=array('code'=>$code,'message'=>$mensaje,'daysoff'=>$data);
        echo json_encode($array);
    }

    public function ajax_dia_conf_global($fecha){
        $data['fecha'] = $fecha;
        $this->load->view ( 'manager/diaferiado/ajax_dia_calendario', $data );
    }

    public function ajax_agregar_dia_feriado(){
        $code=0;
        $mensaje='';
        $data=array();
        $fecha=(isset($_GET['fecha']) && !empty($_GET['fecha']))?$_GET['fecha']:'';
        $name=(isset($_GET['name']))?$_GET['name']:'';
        if(!empty($fecha)){
            $json='{
                "date_dayoff": "'.$fecha.'",
                "name": "'.$name.'"
                }';
            try{
                $uri=$this->base_services.''.$this->context.'daysOff';
                log_message('debug', 'ajax_agregar_dia_feriado URI '.$uri);
                $response = Request::post($uri)->body($json)->sendIt();
                log_message('debug', 'ajax_agregar_dia_feriado Response '.$response);
                $code=$response->code;
                if(isset($response->body) && is_array($response->body) && isset($response->body[0]->response->code)){
                    $code=$response->body[0]->response->code;
                    $mensaje=$response->body[0]->response->message;
                }else{
                    if(isset($response->body->response->code)){
                        $code=$response->body->response->code;
                        switch($code){
                            case '1080':
                                $mensaje='No se puede agregar este d&iacute;a festivo porque ya existen citas para este d&iacute;a';
                            break;
                            default:
                                $mensaje=$response->body->response->message;
                            break;
                        }
                    }
                }
            }catch(Exception $err){
                log_message('error', 'ajax_agregar_dia_feriado '.$err);
                $mensaje=$err->getMessage();
            }
        }else{
            $mensaje='No se pudo ingresar el d&iacute;a el parametro de fecha a ingresar es incorrecto';
        }
        $array=array('code'=>$code,'mensaje'=>$mensaje,'daysoff'=>$data);
        echo json_encode($array);
    }

    public function ajax_confirmar_eliminar_dia(){
        $data['selecciono'] =(isset($_GET['select']) && isset($_GET['fecha']) && !empty($_GET['fecha']))?$_GET['select']:0;
        $data['fecha']=(isset($_GET['fecha']))?$_GET['fecha']:'';
        $data['id'] =(isset($_GET['id']))?$_GET['id']:'';
        $this->load->view ( 'manager/diaferiado/ajax_confirmar_eliminar_dia', $data );
    }

    public function ajax_eliminar_dia_feriado(){
        $code=0;
        $mensaje='';
        $data=array();
        $id=(isset($_GET['id']) && is_numeric($_GET['id']))?$_GET['id']:0;
        if($id>0){
            try{
                $uri=$this->base_services.''.$this->context.'daysOff/'.$id;
                log_message('debug', 'ajax_eliminar_dia_feriado URI '.$uri);
                $response = Request::delete($uri)->sendIt();
                log_message('debug', 'ajax_eliminar_dia_feriado Response '.$response);
                $code=$response->code;
                if(isset($response->body) && is_array($response->body) && isset($response->body[0]->response->code)){
                    $code=$response->body[0]->response->code;
                    $mensaje=$response->body[0]->response->message;
                }else{
                    if(isset($response->body->response->code)){
                        $code=$response->body->response->code;
                        $mensaje=$response->body->response->message;
                    }
                }
            }catch(Exception $err){
                log_message('error', 'ajax_eliminar_dia_feriado '.$err);
                $mensaje=$err->getMessage();
            }
        }else{
            $mensaje='No se pudo eliminar el d&iacute;a, el parametro de la fecha a eliminar es incorrecto';
        }
        $array=array('code'=>$code,'mensaje'=>$mensaje,'daysoff'=>$data);
        echo json_encode($array);
    }
}
