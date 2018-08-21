<?php
require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');

class AccionGuardarConvenio extends Accion {

    public function displayForm() {
	
	$display= '<label>Rut Trabajador</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';	
	$display.='<label>Número de convenio</label>';
        $display.='<input type="text" name="extra[num_convenio]" value="' . (isset($this->extra->num_convenio) ? $this->extra->num_convenio : '') . '" />';	
        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
	$CI->form_validation->set_rules('extra[rut]', 'rut', 'required');
	$CI->form_validation->set_rules('extra[num_convenio]', 'convenio', 'required');
    }

    public function ejecutar(Etapa $etapa) {
	
	$regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);
	
	$regla=new Regla($this->extra->num_convenio);
	$num_convenio=$regla->getExpresionParaOutput($etapa->id);
	
	$json = new stdClass();	
	$json->rut          = $rut;
	$json->id	    = $num_convenio;	
	
	$json = json_encode($json);
        $json = '['.$json.']';
		
	$url = urlapi."users/list/agreement";
	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ));
        curl_exec($ch);
        curl_close($ch);
	
     }
}
