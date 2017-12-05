<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cuentas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        UsuarioManagerSesion::force_login();
    }

    public function index()
    {
        $data['cuentas'] = Doctrine::getTable('Cuenta')->findAll();
        
        $data['title'] = 'Cuentas';
        $data['content'] = 'manager/cuentas/index';
        
        $this->load->view('manager/template', $data);
    }
    
    public function editar($cuenta_id = null)
    {
        if ($cuenta_id) {
            $cuenta = Doctrine::getTable('Cuenta')->find($cuenta_id);
            
            $service = new Connect_services();
            $service->setCuenta($cuenta_id);
            $service->load_data();            
            $calendar = $service;
        } else {
            $cuenta = new Cuenta();
            $calendar = new Connect_services();
        }
        
        $data['cuenta'] = $cuenta;
        $data['calendar'] = $calendar;
        $data['title'] = $cuenta->id ? 'Editar' : 'Crear';
        $data['content'] = 'manager/cuentas/editar';
        
        $this->load->view('manager/template', $data);
    }
    
    public function editar_form($cuenta_id = null)
    {
        Doctrine_Manager::connection()->beginTransaction();
        
        try {
            
            if ($cuenta_id)
                $cuenta = Doctrine::getTable('Cuenta')->find($cuenta_id);
            else
                $cuenta = new Cuenta();

            $this->form_validation->set_rules('nombre', 'Nombre', 'required|url_title');
            $this->form_validation->set_rules('nombre_largo', 'Nombre largo', 'required');
            $this->form_validation->set_rules('domain', 'Dominio', 'required');

            $respuesta = new stdClass();
            if ($this->form_validation->run() == true) {
                $cuenta->nombre = $this->input->post('nombre');
                $cuenta->nombre_largo = $this->input->post('nombre_largo');
                $cuenta->mensaje = $this->input->post('mensaje');
                $cuenta->logo = $this->input->post('logo');
                $cuenta->save();
                $cuenta_id = (int)$cuenta->id;
            
                if ($cuenta_id > 0) {

                    // Calendar
                    $service = new Connect_services();
                    $service->setAppkey($this->config->item('appkey'));
                    $service->setDomain($this->input->post('domain'));
                    $service->setCuenta($cuenta_id);
                    $service->save();

                    Doctrine_Manager::connection()->commit();

                    $this->session->set_flashdata('message','Cuenta guardada con éxito.');
                    $respuesta->validacion = true;
                    $respuesta->redirect = site_url('manager/cuentas');

                } else {
                    $respuesta->validacion = false;
                    $respuesta->errores = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>Ocurrió un error al guardar los datos.</div>';
                    Doctrine_Manager::connection()->rollback();
                }
            } else {
                $respuesta->validacion = false;
                $respuesta->errores = validation_errors();
                Doctrine_Manager::connection()->rollback();
            }
        } catch (Exception $ex) {
            $respuesta->validacion = false;
            $respuesta->errores = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>'.$ex->getMessage().'</div>';
            Doctrine_Manager::connection()->rollback();
        }        

        echo json_encode($respuesta);
    }
    
    public function eliminar($cuenta_id)
    {
        $cuenta = Doctrine::getTable('Cuenta')->find($cuenta_id);
        $cuenta->delete();
        
        $this->session->set_flashdata('message', 'Cuenta eliminada con éxito.');
        redirect('manager/cuentas');
    }
}
