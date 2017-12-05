<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Portada extends MY_BackendController {
    
    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();
        

    }

    public function index() {
        $usuario=UsuarioBackendSesion::usuario();
        
//        if($usuario->rol=='super' || $usuario->rol=='gestion')
        if(in_array('super',explode(",",UsuarioBackendSesion::usuario()->rol)) || in_array('gestion',explode(",",UsuarioBackendSesion::usuario()->rol ))
                || in_array('reportes',explode(",",UsuarioBackendSesion::usuario()->rol)))
            redirect('backend/gestion');
//        else if ($usuario->rol=='modelamiento')
        else if (in_array('modelamiento',explode(",",UsuarioBackendSesion::usuario()->rol)))
            redirect('backend/procesos');
//        else if($usuario->rol=='operacion' || $usuario->rol=='seguimiento')
        else if(in_array('operacion',explode(",",UsuarioBackendSesion::usuario()->rol)) || in_array('seguimiento',explode(",",UsuarioBackendSesion::usuario()->rol ) ) )
            redirect('backend/seguimiento');
//        else if($usuario->rol=='configuracion')
        else if (in_array('configuracion',explode(",",UsuarioBackendSesion::usuario()->rol)))
            redirect('backend/configuracion');
        else if (in_array('desarrollo',explode(",",UsuarioBackendSesion::usuario()->rol)))
            redirect('backend/api');
    }
    
  
}