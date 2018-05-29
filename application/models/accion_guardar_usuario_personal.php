<?php
require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');

class AccionGuardarUsuarioPersonal extends Accion {

    public function displayForm() {


        $display = '<label>Fecha Solicitud</label>';
        $display.='<input type="text" name="extra[date_request]" value="' . (isset($this->extra->date_request) ? $this->extra->date_request : '') . '" />';
	$display.= '<label>Tipo Solicitud final</label>';
        $display.='<input type="text" name="extra[date_request_final]" value="' . (isset($this->extra->date_request_final) ? $this->extra->date_request_final : '') . '" />';
	$display.= '<label>Tipo Solicitud</label>';
        $display.='<input type="text" name="extra[type]" value="' . (isset($this->extra->type) ? $this->extra->type : '') . '" />';
	$display.= '<label>Cantidad dias solicitados</label>';
        $display.='<input type="text" name="extra[cantidad]" value="' . (isset($this->extra->cantidad) ? $this->extra->cantidad : '') . '" />';
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

	$regla=new Regla($this->extra->date_request_final);
        $date_request_final=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->type);
        $type=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->cantidad);
        $cantidad=$regla->getExpresionParaOutput($etapa->id);
	$cantidad= isset($cantidad)?$cantidad:'';
	$cantidad= ($cantidad!='')?$cantidad:1;        

	$tramite_id = $etapa->tramite_id;
	$json= '{"date_request": '.(isset($date_request) ? '"'.$date_request.'"' : '""').', ';
	$json.= '"datefinal_request": '.(isset($date_request_final) ? '"'.$date_request_final.'"' : '""').', ';
	$json.= '"requiredDays": '.$cantidad.','; 
        $json.= '"type": '.(isset($type) ? $type : '0').',"idTramite":'.$tramite_id.'}';
	
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
