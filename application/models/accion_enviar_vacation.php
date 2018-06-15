<?php
require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');

class AccionEnviarVacation extends Accion {

    public function displayForm() {


        $display = '<label>Fecha Inicial</label>';
        $display.='<input type="text" name="extra[date_init]" value="' . (isset($this->extra->date_init) ? $this->extra->date_init : '') . '" />';
	$display.= '<label>Tipo Fecha Final</label>';
        $display.='<input type="text" name="extra[date_end]" value="' . (isset($this->extra->date_end) ? $this->extra->date_end : '') . '" />';
	$display.= '<label>Cantidad dias solicitados</label>';
        $display.='<input type="text" name="extra[cantidad]" value="' . (isset($this->extra->cantidad) ? $this->extra->cantidad : '') . '" />';
	$display.= '<label>Rut Trabajador</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';	
	
        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        //$CI->form_validation->set_rules('extra[date_request]', 'Fecha Solicitud', 'required');
        //$CI->form_validation->set_rules('extra[type]', 'Tipo Solicitud', 'required');
	//$CI->form_validation->set_rules('extra[rut]', 'Rut', 'required');
    }

    public function ejecutar(Etapa $etapa) {
	$regla=new Regla($this->extra->date_init);
        $date_init=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->date_end);
        $date_end=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->cantidad);
        $cantidad=$regla->getExpresionParaOutput($etapa->id);
	$cantidad= isset($cantidad)?$cantidad:'';
	$cantidad= ($cantidad!='')?$cantidad:1;        

	$tramite_id = $etapa->tramite_id;
	
	$json   = new stdClass();
	$json->fecha_inicial = $date_init;
	$json->rut 	     = $rut;
	$json->fecha_final   = $date_end;
	$json->idTramite     = $tramite_id;
	$json->requestDays   = $cantidad;

	$json = json_encode($json);
        $json = '['.$json.']';
	
	
        $ch = curl_init();
	$url = urlapi."/users/list/vacationrequest";
	
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
