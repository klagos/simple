<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cuentas extends MY_Controller {

    public function __construct() {
        parent::__construct();

        
        
        if(!UsuarioSesion::usuario()->registrado){
            echo 'Usuario no registrado en el sistema.';
        }
    }

    public function editar() {
	//Verificamos que el usuario ya se haya logeado 
	if (!UsuarioSesion::usuario()->registrado) {
    		$this->session->set_flashdata('redirect', current_url());
    		redirect('tramites/disponibles');
	}
        $data['usuario']=UsuarioSesion::usuario();
        $data['redirect']=$this->session->flashdata('redirect');
       	$data['sidebar']='mi_cuenta'; 
        $data['content'] = 'cuenta/editar';
        $data['title'] = 'Edita tu información';
        $this->load->view('template', $data);
    }
    
    public function editar_form(){
	//Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
        $this->form_validation->set_rules('nombres','Nombre','required');
        $this->form_validation->set_rules('apellido_paterno','Apellido Paterno','required');
        $this->form_validation->set_rules('apellido_materno','Apellido Materno','required');
        $this->form_validation->set_rules('email','Correo electrónico','required|valid_email|callback_check_email');
        
        $respuesta=new stdClass();
        if($this->form_validation->run()==TRUE){
            $usuario=UsuarioSesion::usuario();
            $usuario->nombres=$this->input->post('nombres');
            $usuario->apellido_paterno=$this->input->post('apellido_paterno');
            $usuario->apellido_materno=$this->input->post('apellido_materno');
            $usuario->email=$this->input->post('email');
            if($usuario->cuenta_id)
                $usuario->vacaciones=$this->input->post('vacaciones');

            $usuario->save();
            
            $respuesta->validacion=TRUE;
            $redirect=$this->input->post('redirect');
            if(!$redirect)
                $respuesta->redirect=site_url();
            else
                $respuesta->redirect=$redirect;
            
        }else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }
        
        echo json_encode($respuesta);
    }
    
    public function editar_password() {
	//Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }
        $data['usuario']=UsuarioSesion::usuario();
        $data['redirect']=$this->input->server('HTTP_REFERER');
	$data['sidebar']='editar_pass';
        $data['content'] = 'cuenta/editar_password';
        $data['title'] = 'Edita tu información';
        $this->load->view('template', $data);
    }
    
    public function editar_password_form(){
	//Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }

        $this->form_validation->set_rules('password_old','Contraseña antigua','required|callback_check_password');
        $this->form_validation->set_rules('password_new','Contraseña nueva','required|min_length[6]');
        $this->form_validation->set_rules('password_new_confirm','Confirmar contraseña nueva','required|matches[password_new]');

        $respuesta=new stdClass();
        if($this->form_validation->run()==TRUE){
            $usuario=UsuarioSesion::usuario();
            $usuario->password=$this->input->post('password_new');
            $usuario->save();
            
            $respuesta->validacion=TRUE;
            $respuesta->redirect=$this->input->post('redirect');
            
        }else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }
        
        echo json_encode($respuesta);
    }

    function check_password($password){
	//Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }

        $autorizacion=UsuarioSesion::validar_acceso(UsuarioSesion::usuario()->usuario,$this->input->post('password_old'));
        
        if($autorizacion)
            return TRUE;
        
        $this->form_validation->set_message('check_password','Usuario y/o contraseña incorrecta.');
        return FALSE;
        
    }
    
    function check_email($email) {
	//Verificamos que el usuario ya se haya logeado 
        if (!UsuarioSesion::usuario()->registrado) {
                $this->session->set_flashdata('redirect', current_url());
                redirect('tramites/disponibles');
        }

        $usuario = Doctrine::getTable('Usuario')->findOneByEmailAndOpenId($email,0);

        if (!$usuario || $usuario==UsuarioSesion::usuario())
            return TRUE;

        $this->form_validation->set_message('check_email', 'Correo electrónico ya esta en uso por otro usuario.');
        return FALSE;
    }

}
