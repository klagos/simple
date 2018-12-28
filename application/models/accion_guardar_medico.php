<?php
require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');
require_once(routecontrollers.'authorization.php');

class AccionGuardarMedico extends Accion {

    public function displayForm() {

        $display = '<label>Nombre</label>';
        $display.='<input type="text" name="extra[nombre]" value="' . (isset($this->extra->nombre) ? $this->extra->nombre : '') . '" />';
	    $display.= '<label>Apellido</label>';
        $display.='<input type="text" name="extra[apellido]" value="' . (isset($this->extra->apellido) ? $this->extra->apellido : '') . '" />';
	    $display.= '<label>Especialidad</label>';
        $display.='<input type="text" name="extra[especialidad]" value="' . (isset($this->extra->especialidad) ? $this->extra->especialidad : '') . '" />';
	    $display.= '<label>Rut</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';	
	
        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[nombre]', 'Nombre', 'required');
        $CI->form_validation->set_rules('extra[apellido]', 'Apellido', 'required');
        $CI->form_validation->set_rules('extra[rut]', 'Rut', 'required');
    }

    public function ejecutar(Etapa $etapa) {
        $regla=new Regla($this->extra->nombre);
        $nombre=$regla->getExpresionParaOutput($etapa->id);
		
        $regla=new Regla($this->extra->apellido);
        $apellido=$regla->getExpresionParaOutput($etapa->id);
	
	    $regla=new Regla($this->extra->especialidad);
        $especialidad=$regla->getExpresionParaOutput($etapa->id);

	    $regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);
	
    	$json = new stdClass();
    	$json->rut  = $rut;
        $json->name = $nombre;
        $json->lastName = $apellido;
	    $json->specialty= $especialidad;	
	
	    $json = json_encode($json);
        $json = '['.$json.']';

        $oa = new Authorization();
        $token = $oa->getToken();
	    $url = urlapi."medico";	
	
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json", 
            "Authorization: Bearer ".$token
        ));
        curl_exec($ch);
        curl_close($ch);
    }

}
