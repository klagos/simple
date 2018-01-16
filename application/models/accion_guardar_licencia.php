<?php
require_once('accion.php');
require_once('ChromePhp.php');

class AccionGuardarLicencia extends Accion {

    public function displayForm() {

        $display= '<label>Rut</label>';
        $display.='<input type="text" name="extra[rut]" value="'.(isset($this->extra->rut) ? $this->extra->rut : '') . '" />';
        $display.= '<label>Numero Licencia</label>';
        $display.='<input type="text" name="extra[numero]" value="'.(isset($this->extra->numero) ? $this->extra->numero : '') . '" />';
        $display.='<label>Fecha Inicio Licencia</label>';
        $display.='<input type="text" name="extra[fecha_inicio]" value="'.(isset($this->extra->fecha_inicio) ? $this->extra->fecha_inicio : '') . '" />';
	$display.='<label>Fecha Termino Licencia</label>';
        $display.='<input type="text" name="extra[fecha_termino]" value="'.(isset($this->extra->fecha_termino) ? $this->extra->fecha_termino : '') . '" />';	
	$display.= '<label>Organismo de Salud</label>';
        $display.='<input type="text" name="extra[organismo_de_salud]" value="'.(isset($this->extra->organismo_de_salud) ? $this->extra->organismo_de_salud : '') . '" />';
	$display.= '<label>Fecha Pago</label>';
	$display.='<input type="text" name="extra[pay_date]" value="'.(isset($this->extra->pay_date) ? $this->extra->pay_date : '') . '" />';
        $display.= '<label>Pagado Anteriormente</label>';
        $display.='<input type="text" name="extra[pay_earlier]" value="'.(isset($this->extra->pay_earlier) ? $this->extra->pay_earlier : '') . '" />';
	$display.= '<label>Anticipo</label>';
        $display.='<input type="text" name="extra[pay_advance]" value="'.(isset($this->extra->pay_advance) ? $this->extra->pay_advance : '') . '" />';
	$display.= '<label>Meses Anteriores</label>';
        $display.='<input type="text" name="extra[pay_month_earlier]" value="'.(isset($this->extra->pay_month_earlier) ? $this->extra->pay_month_earlier : '') . '" />';
        $display.= '<label>Dias No Cubiertos</label>';
        $display.='<input type="text" name="extra[pay_days_not_covered]" value="'.(isset($this->extra->pay_days_not_covered) ? $this->extra->pay_days_not_covered : '') . '" />';
	 $display.= '<label>Complemento</label>';
        $display.='<input type="text" name="extra[pay_complement]" value="'.(isset($this->extra->pay_complement) ? $this->extra->pay_complement : '') . '" />';
	 $display.= '<label>Observación</label>';
        $display.='<input type="text" name="extra[pay_observation]" value="' . (isset($this->extra->pay_observation) ? $this->extra->pay_observation : '') . '" />';
	 $display.= '<label>Fecha Retorno</label>';
        $display.='<input type="text" name="extra[return_date]" value="' . (isset($this->extra->return_date) ? $this->extra->return_date : '') . '" />';
	 $display.= '<label>Monto</label>';
        $display.='<input type="text" name="extra[return_value]" value="' . (isset($this->extra->return_value) ? $this->extra->return_value : '') . '" />';
	 $display.= '<label>Saldo</label>';
        $display.='<input type="text" name="extra[return_saldo]" value="' . (isset($this->extra->return_saldo) ? $this->extra->return_saldo : '') . '" />';
	 $display.= '<label>Observación</label>';
        $display.='<input type="text" name="extra[return_observation]" value="' . (isset($this->extra->return_observation) ? $this->extra->return_observation : '') . '" />';

	
	        
	return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[rut]', 'Rut', 'required');
	$CI->form_validation->set_rules('extra[numero]', 'Numero', 'required');
	$CI->form_validation->set_rules('extra[fecha_inicio]', 'Fecha_Inicio', 'required');
	$CI->form_validation->set_rules('extra[fecha_termino]', 'Fecha_Termino', 'required');	
    }

    public function ejecutar(Etapa $etapa) {
        $regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);
	
	$regla=new Regla($this->extra->numero);
        $numero=$regla->getExpresionParaOutput($etapa->id);
	
	$regla=new Regla($this->extra->fecha_inicio);
        $fecha_inicio=$regla->getExpresionParaOutput($etapa->id);
	
	$regla=new Regla($this->extra->fecha_termino);
        $fecha_termino=$regla->getExpresionParaOutput($etapa->id);

	if(isset($this->extra->organismo_de_salud)){
            $regla=new Regla($this->extra->organismo_de_salud);
            $organismo_de_salud=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->pay_date)){
            $regla=new Regla($this->extra->pay_date);
            $pay_date=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->pay_earlier)){
            $regla=new Regla($this->extra->pay_earlier);
            $pay_earlier=$regla->getExpresionParaOutput($etapa->id);
        }	
	if(isset($this->extra->pay_advance)){
            $regla=new Regla($this->extra->pay_advance);
            $pay_advance=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->pay_month_earlier)){
            $regla=new Regla($this->extra->pay_month_earlier);
            $pay_month_earlier=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->pay_days_not_covered)){
            $regla=new Regla($this->extra->pay_days_not_covered);
            $pay_days_not_covered=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->pay_complement)){
            $regla=new Regla($this->extra->pay_complement);
            $pay_complement=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->pay_observation)){
            $regla=new Regla($this->extra->pay_observation);
            $pay_observation=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->return_date)){
            $regla=new Regla($this->extra->return_date);
            $return_date=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->return_value)){
            $regla=new Regla($this->extra->return_value);
            $return_value=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->return_saldo)){
            $regla=new Regla($this->extra->return_saldo);
            $return_saldo=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->return_observation)){
            $regla=new Regla($this->extra->return_observation);
            $return_observation=$regla->getExpresionParaOutput($etapa->id);
        }
  	
	$json = '{"rut": "'.$rut.'", "number": '.$numero.', "init_date": "'.$fecha_inicio.'", "end_date": "'.$fecha_termino.'", ';
        $json.= '"health_agency": '.(isset($organismo_de_salud) ? '"'.$organismo_de_salud.'"' : '""').', ';
	$json.= '"pay_date": '.(isset($pay_date) ? '"'.$pay_date.'"' : '""').', ';
	$json.= '"pay_earlier": '.(isset($pay_earlier) ? ($pay_earlier == null ? '0' : $pay_earlier) : '0').', ';
	$json.= '"pay_advance": '.(isset($pay_advance) ? ($pay_advance == null ? '0' : $pay_advance) : '0').', ';
	$json.= '"pay_month_earlier": '.(isset($pay_month_earlier) ? ($pay_month_earlier == null ? '0' : $pay_month_earlier) : '0').', ';
	$json.= '"pay_days_not_covered": '.(isset($pay_days_not_covered) ? ($pay_days_not_covered == null ? '0' : $pay_days_not_covered) : '0').', ';
	$json.= '"pay_complement": '.(isset($pay_complement) ? ($pay_complement == null ? '0' : $pay_complement) : '0').', ';
	$json.= '"pay_observation": '.(isset($pay_observation) ? '"'.$pay_observation.'"' : '""').', ';
	$json.= '"return_date": '.(isset($return_date) ? '"'.$return_date.'"' : '""').', ';
	$json.= '"return_value": '.(isset($return_value) ? ($return_value == null ? '0' : $return_value) : '0').', ';
	$json.= '"return_saldo": '.(isset($return_saldo) ? ($return_saldo == null ? '0' : $return_saldo) : '0').', ';
	$json.= '"return_observation": '.(isset($return_observation) ? '"'.$return_observation.'"' : '""').'}';
	ChromePhp::log($json);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "private-120a8-apisimple1.apiary-mock.com/Licenses"); 
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

