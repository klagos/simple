<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Portada extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('consultas');
        $this->load->helper(array('url','form'));
        $this->load->database();
    }
	
    public function index(){
        $query=0;
        $data = array();
        $data['vacio']='';
        $data['titulo'] = 'Seguimiento de Trámites en Línea';
        $resp='<br/><div class="alert alert-warning"><strong>Sin datos Disponibles</strong></div>';		

        $fecha = $this->input->post('fecha');
        $nrotramite = trim($this->input->post('nrotramite'));

        $this->form_validation->set_rules('fecha', 'Fecha', 'trim|required|numeric|max_length[20]|xss_clean');
        $this->form_validation->set_rules('nrotramite', 'Nro. Trámite', 'trim|required|numeric|max_length[20]|xss_clean');

        if ($this->form_validation->run() == TRUE){			
            $this->form_validation->set_message('required', 'El %s es requerido');
            $this->form_validation->set_message('max_length', 'El %s debe tener no más de %s carácteres');
            $this->form_validation->set_message('numeric', 'El %s debe ser numerico');        	
        }

        if(is_numeric($nrotramite) && is_numeric($fecha)){	
            $query=$this->consultas->listDatoSeguimiento($nrotramite,$fecha,Cuenta::cuentaSegunDominio());        	
            $data['vacio']= $resp;
        }

        $data['tareas']=$query;
        $data['fecha'] =$fecha;
        $data['nrotramite'] =$nrotramite;	
        $this->load->view('consultas/index',$data);
    }
    
    public function ver_etapas($id_etapa){
        $query = $this->consultas->detalleEtapa($id_etapa);
  
        $data['etapa'] = $query[0];
        $this->load->view('consultas/consultas_info', $data);
    }
}