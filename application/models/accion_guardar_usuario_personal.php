<?php
require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');

class AccionGuardarUsuarioPersonal extends Accion {

    public function displayForm() {


        $display = '<label>Rut</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';
	$display.= '<label>Nombres</label>';
        $display.='<input type="text" name="extra[nombres]" value="' . (isset($this->extra->nombres) ? $this->extra->nombres : '') . '" />';
	$display.= '<label>Apellido Paterno</label>';
        $display.='<input type="text" name="extra[apellido_paterno]" value="' . (isset($this->extra->apellido_paterno) ? $this->extra->apellido_paterno : '') . '" />';
	$display.= '<label>Apellido Materno</label>';
        $display.='<input type="text" name="extra[apellido_materno]" value="' . (isset($this->extra->apellido_materno) ? $this->extra->apellido_materno : '') . '" />';
	$display.= '<label>Sexo</label>';
        $display.='<input type="text" name="extra[sexo]" value="' . (isset($this->extra->sexo) ? $this->extra->sexo : '') . '" />';
	$display .= '<label>Fecha de Nacimiento</label>';
        $display.='<input type="text" name="extra[fecha_nacimiento]" value="' . (isset($this->extra->fecha_nacimiento) ? $this->extra->fecha_nacimiento : '') . '" />';
        $display.= '<label>Nacionalidad</label>';
        $display.='<input type="text" name="extra[nacionalidad]" value="' . (isset($this->extra->nacionalidad) ? $this->extra->nacionalidad : '') . '" />';
        $display.= '<label>Profesi&oacute;n</label>';
        $display.='<input type="text" name="extra[profesion]" value="' . (isset($this->extra->profesion) ? $this->extra->profesion : '') . '" />';
        $display.= '<label>Estado Civil</label>';
        $display.='<input type="text" name="extra[estado_civil]" value="' . (isset($this->extra->estado_civil) ? $this->extra->estado_civil : '') . '" />';
        $display.= '<label>Tel&eacute;fono</label>';
        $display.='<input type="text" name="extra[telefono]" value="' . (isset($this->extra->telefono) ? $this->extra->telefono : '') . '" />';
 	$display .= '<label>Correo Electr&oacute;nico</label>';
        $display.='<input type="text" name="extra[correo]" value="' . (isset($this->extra->correo) ? $this->extra->correo : '') . '" />';
 	$display.= '<label>Domicilio</label>';
        $display.='<input type="text" name="extra[domicilio]" value="' . (isset($this->extra->domicilio) ? $this->extra->domicilio : '') . '" />';
        $display.= '<label>Ciudad</label>';
        $display.='<input type="text" name="extra[ciudad]" value="' . (isset($this->extra->ciudad) ? $this->extra->ciudad : '') . '" />';
        $display.= '<label>Comuna</label>';
        $display.='<input type="text" name="extra[comuna]" value="' . (isset($this->extra->comuna) ? $this->extra->comuna : '') . '" />';
        $display .= '<label>Regi&oacute;n</label>';
        $display.='<input type="text" name="extra[region]" value="' . (isset($this->extra->region) ? $this->extra->region : '') . '" />';
	
        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
	$CI->form_validation->set_rules('extra[rut]', 'Rut', 'required');
	$CI->form_validation->set_rules('extra[nombres]','Nombres','required');
	$CI->form_validation->set_rules('extra[apellido_paterno]', 'Apellido Paterno', 'required');
        $CI->form_validation->set_rules('extra[apellido_materno]','Apellido Materno','required');
	$CI->form_validation->set_rules('extra[sexo]', 'Sexo', 'required');
        $CI->form_validation->set_rules('extra[fecha_nacimiento]','Fecha de Nacimiento','required');
        $CI->form_validation->set_rules('extra[nacionalidad]', 'Nacionalidad', 'required');
        $CI->form_validation->set_rules('extra[profesion]','Profesi&oacute;n','required');
	$CI->form_validation->set_rules('extra[estado_civil]', 'Estado Civil', 'required');
        $CI->form_validation->set_rules('extra[telefono]','Tel&eacute;fono','required');
        $CI->form_validation->set_rules('extra[domicilio]', 'Domicilio', 'required');
        $CI->form_validation->set_rules('extra[ciudad]','Ciudad','required');
        $CI->form_validation->set_rules('extra[comuna]', 'Comuna', 'required');
        $CI->form_validation->set_rules('extra[region]','Regi&oacute;n','required');

    }

    public function ejecutar(Etapa $etapa) {
        $regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->nombres);
        $nombres=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->apellido_paterno);
        $apellido_paterno=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->apellido_materno);
        $apellido_materno=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->sexo);
        $sexo=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->fecha_nacimiento);
        $fecha_nacimiento=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->nacionalidad);
        $nacionalidad=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->profesion);
        $profesion=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->estado_civil);
        $estado_civil=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->telefono);
        $telefono=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->correo);
        $correo=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->domicilio);
        $domicilio=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->ciudad);
        $ciudad=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->comuna);
        $comuna=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->region);
        $region=$regla->getExpresionParaOutput($etapa->id);        

	$tramite_id = $etapa->tramite_id;
	
	$json = new stdClass();
	$json->rut = $rut;
	$json->nombres = $nombres;
	$json->apellido_paterno = $apellido_paterno;
	$json->apelido_materno = $apellido_materno;
	$json->sexo = $sexo;
	$json->fecha_nacimiento = $fecha_nacimiento;
	$json->nacionalidad = $nacionalidad;
	$json->profesion = $profesion;
	$json->estado_civil = $estado_civil;
	$json->telefono = $telefono;
	$json->correo = $correo;
	$json->domicilio = $domicilio;
	$json->ciudad = $ciudad;
	$json->comuna = $comuna;
	$json->region = $region;
	$json->idTramite = $tramite_id;
	$json = json_encode($json);
	$json = '['.$json.']';
	
	ChromePhp::log($json);
	
        $ch = curl_init();
	$url = urlapi."users/".$rut."/admindayrequest";
	//$url = "http://private-120a8-apisimpleist.apiary-mock.com/"."users/".$rut."/admindayrequest";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
                    ));
        curl_exec($ch);
        curl_close($ch);
    }

}
