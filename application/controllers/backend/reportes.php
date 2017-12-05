<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reportes extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();
        
//        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='gestion'){
        if(!in_array('super', explode(',',UsuarioBackendSesion::usuario()->rol) ) && !in_array( 'gestion',explode(',',UsuarioBackendSesion::usuario()->rol))
                && !in_array( 'reportes',explode(',',UsuarioBackendSesion::usuario()->rol))){
            echo 'No tiene permisos para acceder a esta seccion.';
            exit;
        }
    }
    
    public function index(){
        //$procesos=Doctrine::getTable('Proceso')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);
        
        $procesos= Doctrine_Query::create()
            ->from('Proceso p, p.Cuenta c')
            ->where('c.id = ?',UsuarioBackendSesion::usuario()->cuenta_id)
            ->orderBy('p.nombre asc')
            ->execute();
        
        $data['procesos']=$procesos;
        $data['title'] = 'Gestión';
        $data['content'] = 'backend/reportes/index';

        $this->load->view('backend/template', $data);
        
    }

    public function listar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        $reportes=Doctrine_query::create()->from('Reporte r')->where('r.proceso_id = ?',$proceso_id)->orderBy('r.id desc')->execute();

        if(!is_null(UsuarioBackendSesion::usuario()->procesos) && !in_array($proceso_id,explode(',',UsuarioBackendSesion::usuario()->procesos))){
          echo 'Usuario no tiene permisos para ver los reportes';
          exit;
        }

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }
        $data['proceso'] = $proceso;
        $data['reportes'] = $reportes;

        $data['title'] = 'Documentos';
        $data['content'] = 'backend/reportes/listar';
        $data['rol']=UsuarioBackendSesion::usuario()->rol;
        
        $this->load->view('backend/template', $data);
    }


    public function crear($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if(!is_null(UsuarioBackendSesion::usuario()->procesos) && !in_array($proceso_id,explode(',',UsuarioBackendSesion::usuario()->procesos))){
          echo 'Usuario no tiene permisos';
          exit;
        }

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id || (!in_array('super',explode(",",UsuarioBackendSesion::usuario()->rol)) && !in_array('reportes',explode(",",UsuarioBackendSesion::usuario()->rol))) ) {
            echo 'No tiene permisos para crear este documento';
            exit;
        }

        $data['edit'] = FALSE;
        $data['proceso'] = $proceso;
        $data['title'] = 'Edición de Documento';
        $data['content'] = 'backend/reportes/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if(!is_null(UsuarioBackendSesion::usuario()->procesos) && !in_array($reporte->Proceso->id,explode(',',UsuarioBackendSesion::usuario()->procesos))){
          echo 'Usuario no tiene permisos';
          exit;
        }

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para editar este documento';
            exit;
        }

        $data['reporte'] = $reporte;
        $data['edit'] = TRUE;
        $data['proceso'] = $reporte->Proceso;
        $data['title'] = 'Edición de Reporte';
        $data['content'] = 'backend/reportes/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar_form($reporte_id = NULL) {
        $reporte = NULL;
        if ($reporte_id) {
            $reporte = Doctrine::getTable('Reporte')->find($reporte_id);
        } else {
            $reporte = new Reporte();
            $reporte->proceso_id = $this->input->post('proceso_id');
        }

        if(!is_null(UsuarioBackendSesion::usuario()->procesos) && !in_array($reporte->Proceso->id,explode(',',UsuarioBackendSesion::usuario()->procesos))){
          echo 'Usuario no tiene permisos';
          exit;
        }

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este documento.';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('campos', 'Campos', 'required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $reporte->nombre = $this->input->post('nombre');
            $reporte->campos = $this->input->post('campos');
            $reporte->save();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/reportes/listar/' . $reporte->Proceso->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if(!is_null(UsuarioBackendSesion::usuario()->procesos) && !in_array($reporte->Proceso->id,explode(',',UsuarioBackendSesion::usuario()->procesos))){
          echo 'Usuario no tiene permisos';
          exit;
        }

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este documento.';
            exit;
        }

        $proceso = $reporte->Proceso;
        $reporte->delete();

        redirect('backend/reportes/listar/' . $proceso->id);
    }
    
    public function ver($reporte_id){
    	
    	$reporte = Doctrine::getTable('Reporte')->find($reporte_id);

      if(!is_null(UsuarioBackendSesion::usuario()->procesos) && !in_array($reporte->Proceso->id,explode(',',UsuarioBackendSesion::usuario()->procesos))){
          echo 'Usuario no tiene permisos para ver el reporte';
          exit;
        }
    	
    	if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
    		echo 'Usuario no tiene permisos';
    		exit;
    	}
    	
    	// Reporte del proceso
    	$proceso = $reporte->Proceso;
    	
    	$tramites_completos=0;
    	$tramites_vencidos=0;
    	$tramites_pendientes=0;        
        $etapas_cantidad=0;
    	
    	
    	
    	
    	
    	// Parametros
    	$query = $this->input->get('query');
    	$created_at_desde=$this->input->get('created_at_desde');
    	$created_at_hasta=$this->input->get('created_at_hasta');
    	$pendiente=$this->input->get('pendiente')!==false?$this->input->get('pendiente'):-1;
    	$formato=$this->input->get('formato');
    	$filtro=$this->input->get('filtro');
    	$offset = $this->input->get ( 'offset' );
    	$per_page = 50;
    	 
    	$params = array();
    	if($created_at_desde)
            array_push($params,'created_at >= '. "'".date('Y-m-d',strtotime($created_at_desde))."'");
        if($created_at_hasta)
            array_push($params,'created_at <= ' . "'".date('Y-m-d',strtotime($created_at_hasta))."'");
        if($pendiente!=-1)
        	array_push($params, 'pendiente = '. $pendiente);
        
        if ($query) {
        	$this->load->library('sphinxclient');
        	$this->sphinxclient->setServer($this->config->item('sphinx_host'),$this->config->item('sphinx_port'));
        	$this->sphinxclient->setFilter('proceso_id', array($reporte->Proceso->id));
        	$result = $this->sphinxclient->query(json_encode($query), 'tramites');
        	if($result['total']>0){
        		$matches = array_keys($result['matches']);
        		array_push($params,'t.id IN ('.implode(',',$matches).')');
        	}else{
        		$params = array('0');
        	}

        }
        
        
        
       		if ($formato == "pdf"){
       			$reporte_tabla = $reporte->getReporteAsMatrix($params);
       			$this->load->library('pdf');
       			$data['tramites_vencidos']= $tramites_vencidos;
       			$data['tramites_pendientes'] = $tramites_pendientes;
       			$data['tramites_completos'] = $tramites_completos;
       			$data['promedio_tramite'] = $promedio_tramite;                       
       			$data['reporte'] = $reporte_tabla;
       			$data['title'] = $reporte->nombre.' - Proceso "'.$reporte->Proceso->nombre.'"';
       			$html = $this->load->view('backend/reportes/pdf', $data, true);
       			$pdf = $this->pdf->load();
       			$pdf->WriteHtml($html);
       			$pdf->Output('reporte.pdf', 'D');
       			 
       			
       			
       		} else if($formato == "xls") {
       			
       			$reporte_tabla = $reporte->getReporteAsMatrix($params);
       			$CI=& get_instance();
       			
       			$CI->load->library('Excel_XML');
       			
       			
       			$CI->excel_xml->addArray($reporte_tabla);
       			$CI->excel_xml->generateXML('reporte');
       			return;
       		}

       		$ntramites = count($reporte->getReporteAsMatrix($params))-1;
       		
       		$reporte_tabla = $reporte->getReporteAsMatrix($params, $per_page, $offset);
       		
       		$this->load->library('pagination');
       		$this->pagination->initialize(array(
       				'base_url'=>site_url('backend/reportes/ver/'.$reporte_id.'?query='.$query.'&pendiente='.$pendiente.'&created_at_desde='.$created_at_desde.'&created_at_hasta='.$created_at_hasta),
       				'total_rows'=>$ntramites,
       				'per_page'=>$per_page
       		));

      foreach($proceso->Tramites as $tramite){
        
            $etapas_cantidad = Doctrine_query::create()->from('Etapa e')->
                    where('e.tramite_id = ?',$tramite->id)->count();
        
            if ($tramite->pendiente == 0){
                    $tramites_completos++;
                    
            }else if($etapas_cantidad > 0){   
                
                if ($tramite->getTareasVencidas()->count() > 0)
                    $tramites_vencidos++;

                $tramites_pendientes++;
            } 
      }
      
      $promedio_tramite = $proceso->getDiasPorTramitesAvg();
      $promedio_tramite = $promedio_tramite[0]['avg'];
       		
       		
       		$data['tramites_vencidos']= $tramites_vencidos;
       		$data['tramites_pendientes'] = $tramites_pendientes;
       		$data['tramites_completos'] = $tramites_completos;
       		$data['promedio_tramite'] = $promedio_tramite;                
       		$data['filtro'] = $filtro;
	        $data['query'] = $query;
	    	$data['reporte_tabla'] = $reporte_tabla;
	    	$data['reporte'] = $reporte;
	    	$data['pendiente']=$pendiente;
	    	$data['created_at_desde']=$created_at_desde;
	    	$data['created_at_hasta']=$created_at_hasta;
	    	$data['title'] = $reporte->nombre.' - Proceso "'.$reporte->Proceso->nombre.'"';
	    	$data['content'] = 'backend/reportes/ver';
	    	
	    	$this->load->view('backend/template', $data);
        
    }

}