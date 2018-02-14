<?php
require_once('accion.php');
require_once('ChromePhp.php');

class AccionEnviarAdminDays extends Accion {

    public function displayForm() {


        $display = '<label>Fecha Solicitud</label>';
        $display.='<input type="text" name="extra[date_request]" value="' . (isset($this->extra->date_request) ? $this->extra->date_request : '') . '" />';
        $display.= '<label>Tipo Solicitud</label>';
        $display.='<input type="text" name="extra[type]" value="' . (isset($this->extra->type) ? $this->extra->type : '') . '" />';
	$display.= '<label>Rut Trabajador</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';	
	
        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[date_request]', 'Fecha Solicitud', 'required');
        $CI->form_validation->set_rules('extra[type]', 'Tipo Solicitud', 'required');
	$CI->form_validation->set_rules('extra[rut]', 'Rut', 'required');
    }

    public function ejecutar(Etapa $etapa) {
        $regla=new Regla($this->extra->date_request);
        $date_request=$regla->getExpresionParaOutput($etapa->id);
        $regla=new Regla($this->extra->type);
        $type=$regla->getExpresionParaOutput($etapa->id);
	$regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);
        
	$json= '{"date_request": '.(isset($date_request) ? '"'.$date_request.'"' : '""').', ';
        $json.= '"type": '.(isset($type) ? $type : '0').'}';
	
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://nexoya.cl:8080/api/users/".$rut."/admindayrequest");
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
