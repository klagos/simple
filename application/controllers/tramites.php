<?php
require_once(FCPATH."procesos.php");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tramites extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        redirect('etapas/inbox');
    }

    /*public function participados() {
        $data['tramites']=Doctrine::getTable('Tramite')->findParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio());        
        $data['sidebar']='participados';
        $data['content'] = 'tramites/participados';
        $data['title'] = 'Bienvenido';
        $this->load->view('template', $data);
    }*/
	public function participados($offset=0) {
        $this->load->library('pagination');
        $this->load->helper('form');
        $this->load->helper('url');

        $query = $this->input->post('query');
        $matches="";
        $rowtramites="";
        $contador="0";
        $resultotal="false";
        $perpage=50;

        if ($query) { 
            $this->load->library('sphinxclient');
            $this->sphinxclient->setServer ( $this->config->item ( 'sphinx_host' ), $this->config->item ( 'sphinx_port' ) );
            $this->sphinxclient->SetLimits($offset, 10000);
            $result = $this->sphinxclient->query(json_encode($query), 'tramites');                         
           
            if($result['total'] > 0 ){
                $resultotal="true";             
            }else{               
                $resultotal="false";
            }
        }
       
       /*
        $statement = Doctrine_Manager::getInstance()->connection();
        $results = $statement->execute("Select * from dato_seguimiento where nombre='desc_proceso_tramite' limit 1");
        $datos=$results->fetchAll();
        foreach($datos as $d){  echo $d['valor'];  }    
        */
        if($resultotal=='true'){
                $matches = array_keys($result['matches']);
                $contador= Doctrine::getTable('Tramite')->findParticipadosMatched(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(),$matches,$query)->count();                               
                $rowtramites= Doctrine::getTable('Tramite')->findParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(), $perpage,$offset,$matches,$query);    
        }else{
                $rowtramites= Doctrine::getTable('Tramite')->findParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(), $perpage, $offset,'0',$query);
                $contador= Doctrine::getTable('Tramite')->findParticipadosALL(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio())->count();
        }
        
        $config['base_url'] = site_url('tramites/participados');
        $config['total_rows'] = $contador;  
        $config['per_page'] = $perpage;       
        $config['full_tag_open'] = '<div class="pagination pagination-centered"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['page_query_string']=false;
        $config['query_string_segment']='offset';
        $config['first_link'] = 'Primero';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Último';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '»';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '«';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';      

        $this->pagination->initialize($config);
        $data['tramites']=$rowtramites;
        $data['query'] = $query;
        $data['sidebar']='participados';
        $data['content'] = 'tramites/participados';
        $data['title'] = 'Bienvenido';
        
        $data['links'] = $this->pagination->create_links(); 
        $this->load->view('template', $data);
    }

    public function disponibles() {

        //$orderby=$this->input->get('orderby')?$this->input->get('orderby'):'nombre';
        //$direction=$this->input->get('direction')?$this->input->get('direction'):'asc';
        
        $data['procesos']=Doctrine::getTable('Proceso')->findProcesosDisponiblesParaIniciar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(),'nombre','asc');
        
        //$data['orderby']=$orderby;
        //$data['direction']=$direction;
        $data['sidebar']='disponibles';
        $data['content'] = 'tramites/disponibles';
        $data['title'] = 'Trámites disponibles a iniciar';
        $this->load->view('template', $data);
    }

    public function iniciar($proceso_id) {
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);
        //echo UsuarioSesion::usuario()->id;
        //exit;
        if(!$proceso->canUsuarioIniciarlo(UsuarioSesion::usuario()->id)){
            echo 'Usuario no puede iniciar este proceso';
            exit;
        }
        
        //Vemos si es que usuario ya tiene un tramite de proceso_id ya iniciado, y que se encuentre en su primera etapa.
        //Si es asi, hacemos que lo continue. Si no, creamos uno nuevo
        $tramite=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.Tramite.Etapas hermanas')
                ->where('t.pendiente=1 AND p.activo=1 AND p.id = ? AND e.usuario_id = ?',array($proceso_id, UsuarioSesion::usuario()->id))
                ->groupBy('t.id')
                ->having('COUNT(hermanas.id) = 1')
                ->fetchOne();
        
        if(!$tramite){
            $tramite=new Tramite();
            $tramite->iniciar($proceso->id);
        }  
        
    
        $qs=$this->input->server('QUERY_STRING');
        redirect('etapas/ejecutar/'.$tramite->getEtapasActuales()->get(0)->id.($qs?'?'.$qs:''));
    }
    
    public function eliminar($tramite_id){
        $tramite=Doctrine::getTable('Tramite')->find($tramite_id);
                
        if($tramite->Etapas->count()>1){
            echo 'Tramite no se puede eliminar, ya ha avanzado mas de una etapa';
            exit;
        }
        
        if(UsuarioSesion::usuario()->id!=$tramite->Etapas[0]->usuario_id){
            echo 'Usuario no tiene permisos para eliminar este tramite';
            exit;
        }
        
        $tramite->delete();
        redirect($this->input->server('HTTP_REFERER'));
    }
   
   //Funcion que muestra los documentos del estudio psicolaboral	
    public function docestudio(){
	 //Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
         	redirect('tramites/disponibles');
        }

	$idProcesoDocumentacion =proceso_estudio_documentacion_id;
	$canDescargarDocEstudio=Doctrine::getTable('Proceso')->canDesargarDocEstudio(UsuarioSesion::usuario()->id);
	if($canDescargarDocEstudio){
		$tramiteDoc	= Doctrine::getTable('Tramite')->getDocumentosProcesoEstudios($idProcesoDocumentacion);
		$rowEtapas 	= $tramiteDoc[0]->getEtapasTramites();
		$sizeEtapas	= count($rowEtapas);
		
		//Nombre de los documentos dentro del formulario
		$url_formato_acta = null;
		$url_instructivo  = null;
		$url_instructivo_trabajadores = null;
		$url_registro_entrega_codigos = null;		
		$url_taller_trabajadores_mail = null;
		$url_taller_trabajadores_presencial= null;		

		$pos_formato = 0;
		for ($i = $sizeEtapas-1  ; $i >=0 ; $i--) {
			$etapa 	  = $rowEtapas[$i];
    			$paso = $etapa->getPasoEjecutable(0);
			foreach ($paso->Formulario->Campos as $c){
				$string = $c->displayConDatoSeguimiento($etapa->id, 'visualizacion');
				
				//Formato de acta
				if ((strpos($string, 'formato_acta') !== false)  && !$url_formato_acta ) {
					$s = "";
	                                preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
					$url_formato_acta= $s['href'][0];
				}

				//Instructivo_aplicacion
				if ((strpos($string, 'instructivo_aplicacion') !== false)  && !$url_instructivo) {
                                        $s = "";
                                        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                        $url_instructivo= $s['href'][0];
				}

				//Instructivo para trabajadores
                                if ((strpos($string, 'instructivo_trabajadores') !== false)  && !$url_instructivo_trabajadores) {
                                        $s = "";
                                        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                        $url_instructivo_trabajadores= $s['href'][0];
                                }
				
				//Registro entrega de codigos
                                if ((strpos($string, 'registro_entrega_codigos') !== false)  && !$url_registro_entrega_codigos){
                                        $s = "";
                                        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                        $url_registro_entrega_codigos= $s['href'][0];
                                }
				
				//Taller informativo trabajadores
                                if ((strpos($string, 'taller_trabajadores_mail') !== false)  && !$url_taller_trabajadores_mail) {
                                        $s = "";
                                        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                        $url_taller_trabajadores_mail= $s['href'][0];
                                }

				//Taller informativo trabajadores
                                if ((strpos($string, 'taller_trabajadores_mail') !== false)  && !$url_taller_trabajadores_presencial) {
                                        $s = "";
                                        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $string, $s);
                                        $url_taller_trabajadores_presencial= $s['href'][0];
                                }	
			}
		}
	}
	
	$data['some_doc']=true;
	if(!$url_formato_acta && !$url_instructivo && !$url_instructivo_trabajadores && !$url_registro_entrega_codigos && !$url_taller_trabajadores_mail &&!$url_taller_trabajadores_presencial)
		$data['some_doc']=false;	
	$data['url_formato_acta'] = $url_formato_acta;
        $data['url_instructivo']  = $url_instructivo;
	$data['url_instructivo_trabajadores']=$url_instructivo_trabajadores;
	$data['url_registro_entrega_codigos']=$url_registro_entrega_codigos;
 	$data['url_taller_trabajadores_mail']=$url_taller_trabajadores_mail;
 	$data['url_taller_trabajadores_presencial']=$url_taller_trabajadores_presencial;
	
	$data['sidebar']='licencia_pago';
        $data['content'] = 'tramites/docestudio';
        $data['title'] = 'Documentación estudio psicosocial';
        $this->load->view('template', $data);


    }


}
