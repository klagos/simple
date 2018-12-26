<?php
require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');
require_once('/var/www/html/simple/application/controllers/authorization.php');

class AccionGuardarFasMedico extends Accion {

    public function displayForm() {


        $display = '<label>Fecha Solicitud</label>';
        $display.='<input type="text" name="extra[date_request]" value="' . (isset($this->extra->date_request) ? $this->extra->date_request : '') . '" />';
	$display.= '<label>Code</label>';
        $display.='<input type="text" name="extra[code]" value="' . (isset($this->extra->code) ? $this->extra->code : '') . '" />';
	$display.= '<label>Total</label>';
        $display.='<input type="text" name="extra[total]" value="' . (isset($this->extra->total) ? $this->extra->total : '') . '" />';
	$display.= '<label>Congelar pago</label>';
        $display.='<input type="text" name="extra[congelar]" value="' . (isset($this->extra->congelar) ? $this->extra->congelar : '') . '" />';
	
	$display.= '<label>Tipo de requerim.</label>';
        $display.='<input type="text" name="extra[tipo]" value="' . (isset($this->extra->tipo) ? $this->extra->tipo : '') . '" />';
	
	$display.= '<label>Hora.</label>';
        $display.='<input type="text" name="extra[hora]" value="' . (isset($this->extra->hora) ? $this->extra->hora : '') . '" />';

	$display.= '<label>Rut Trabajador</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';	
	
        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        //$CI->form_validation->set_rules('extra[date_request]', 'Fecha Solicitud', 'required');
        //$CI->form_validation->set_rules('extra[type]', 'Tipo Solicitud', 'required');
	$CI->form_validation->set_rules('extra[rut]', 'Rut', 'required');
    }

    public function ejecutar(Etapa $etapa) {
        $regla=new Regla($this->extra->date_request);
        $date_request=$regla->getExpresionParaOutput($etapa->id);
	
        $regla=new Regla($this->extra->code);
        $code =$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->total);
        $total=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->congelar);
        $congelar=$regla->getExpresionParaOutput($etapa->id);
	$congelar=($congelar=='si')?true:false;

	$regla=new Regla($this->extra->tipo);
        $tipo=$regla->getExpresionParaOutput($etapa->id);
       
	$request=($tipo=='social')?'socialbenefitrequest':'medicalbenefitrequest';
	
	$regla=new Regla($this->extra->hora);
        $hora=$regla->getExpresionParaOutput($etapa->id);	
	

	ChromePhp::log($tipo);

	
	if($tipo=='social'){
		ChromePhp::log($date_request);
		 if($code >=10 && $code<=13)
		 	$date_request =  $date_request.' '.$hora;
		else
			$date_request =  $date_request.' 00:00';
		ChromePhp::log($date_request);
	}

	$tramite_id = $etapa->tramite_id;
	//$json= '{"date_request": '.(isset($date_request) ? '"'.$date_request.'"' : '""').', ';
	//$json.= '"code":  '.$code. ',';
	//$json.= '"total": '.$total.',';
        //$json.= '"idTramite":'.$tramite_id.'}';
	$json	= new stdClass();	
	$json->date_request = $date_request;
	$json->code	    = $code;
	$json->value 	    = $total;
	$json->rut          = $rut;
	$json->idTramite    = $tramite_id;
	$json->frozenPaid   = $congelar;

	$json = json_encode($json);
    $json = '['.$json.']';
	
	$oa = new Authorization();
    $token = $oa->getToken();
	
	$url = urlapi."users/list/".$request;
	// $url = "http://private-120a8-apisimpleist.apiary-mock.com/users/list/medicalbenefitrequest";
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json", "Authorization: Bearer ".$token ));
    curl_exec($ch);
    curl_close($ch);

	/*
        $ch = curl_init();
	//$url = urlapi."users/list/medicalbenefitrequest";
	$url = "http://private-120a8-apisimpleist.apiary-mock.com/users/list/medicalbenefitrequest";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ));
        curl_exec($ch);
        curl_close($ch);

	*/
    }

}
