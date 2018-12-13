<?php
require_once('accion.php');
require_once('ChromePhp.php');

class AccionGuardarLicencia extends Accion {

    public function displayForm() {

        $display= '<label>Rut</label>';
        $display.='<input type="text" name="extra[rut]" value="'.(isset($this->extra->rut) ? $this->extra->rut : '') . '" />';
        $display.= '<label>Numero Licencia</label>';
        $display.='<input type="text" name="extra[numero]" value="'.(isset($this->extra->numero) ? $this->extra->numero : '') . '" />';
        $display.='<label>Fecha Recepcion Licencia</label>';
        $display.='<input type="text" name="extra[fecha_recepcion]" value="'.(isset($this->extra->fecha_recepcion) ? $this->extra->fecha_recepcion : '') . '" />';
	$display.='<label>Fecha Inicio Licencia</label>';
        $display.='<input type="text" name="extra[fecha_inicio]" value="'.(isset($this->extra->fecha_inicio) ? $this->extra->fecha_inicio : '') . '" />';
	$display.='<label>Fecha Termino Licencia</label>';
        $display.='<input type="text" name="extra[fecha_termino]" value="'.(isset($this->extra->fecha_termino) ? $this->extra->fecha_termino : '') . '" />';	
	$display.='<label>Dias</label>';
        $display.='<input type="text" name="extra[days]" value="'.(isset($this->extra->days) ? $this->extra->days : '') . '" />';
	$display.='<label>Tipo</label>';
        $display.='<input type="text" name="extra[tipo]" value="'.(isset($this->extra->tipo) ? $this->extra->tipo : '') . '" />';
	$display.='<label>Tipo de reposo</label>';
        $display.='<input type="text" name="extra[tipo_reposo]" value="'.(isset($this->extra->tipo_reposo) ? $this->extra->tipo_reposo : '') . '" />';
	$display.='<label>Estado</label>';
        $display.='<input type="text" name="extra[estado]" value="'.(isset($this->extra->estado) ? $this->extra->estado : '') . '" />';
	$display.='<label>Lugar de reposo</label>';
	$display.='<input type="text" name="extra[lugar_reposo]" value="'.(isset($this->extra->lugar_reposo) ? $this->extra->lugar_reposo : '') . '" />';
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
	$display.= '<label>Dias No Cubiertos Anterior</label>';
        $display.='<input type="text" name="extra[pay_days_not_covered_ant]" value="'.(isset($this->extra->pay_days_not_covered_ant) ? $this->extra->pay_days_not_covered_ant : '') . '" />';
	$display.= '<label>Complemento</label>';
        $display.='<input type="text" name="extra[pay_complement]" value="'.(isset($this->extra->pay_complement) ? $this->extra->pay_complement : '') . '" />';
	$display.= '<label>Complemento anterior</label>';
        $display.='<input type="text" name="extra[pay_complement_ant]" value="'.(isset($this->extra->pay_complement_ant) ? $this->extra->pay_complement_ant : '') . '" />';
	$display.= '<label>Observación</label>';
        $display.='<input type="text" name="extra[pay_observation]" value="' . (isset($this->extra->pay_observation) ? $this->extra->pay_observation : '') . '" />';
	$display.= '<label>Fecha Retorno</label>';
        $display.='<input type="text" name="extra[return_date]" value="' . (isset($this->extra->return_date) ? $this->extra->return_date : '') . '" />';
	$display.= '<label>Monto Retorno</label>';
        $display.='<input type="text" name="extra[return_value]" value="' . (isset($this->extra->return_value) ? $this->extra->return_value : '') . '" />';
	$display.= '<label>Monto Actual Retorno</label>';
        $display.='<input type="text" name="extra[return_value_act]" value="' . (isset($this->extra->return_value_act) ? $this->extra->return_value_act : '') . '" />';
	$display.= '<label>Formato</label>';
        $display.='<input type="text" name="extra[formato]" value="' . (isset($this->extra->formato) ? $this->extra->formato : '') . '" />';
	$display.= '<label>Rut Medico</label>';
        $display.='<input type="text" name="extra[rut_medico]" value="'.(isset($this->extra->rut_medico) ? $this->extra->rut_medico : '') . '" />';	
	$display.= '<label>Saldo</label>';
        $display.='<input type="text" name="extra[return_saldo]" value="' . (isset($this->extra->return_saldo) ? $this->extra->return_saldo : '') . '" />';
	$display.= '<label>Observacion</label>';
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
	
	$regla=new Regla($this->extra->rut_medico);
        $rut_medico=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->numero);
        $numero=$regla->getExpresionParaOutput($etapa->id);
	
	$regla=new Regla($this->extra->fecha_recepcion);
        $fecha_recepcion=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->fecha_inicio);
        $fecha_inicio=$regla->getExpresionParaOutput($etapa->id);
	
	$regla=new Regla($this->extra->fecha_termino);
        $fecha_termino=$regla->getExpresionParaOutput($etapa->id);
	
	$regla=new Regla($this->extra->days);
	$days=$regla->getExpresionParaOutput($etapa->id);
	
	$regla=new Regla($this->extra->estado);
        $estado=$regla->getExpresionParaOutput($etapa->id);		
	
	if(isset($this->extra->organismo_de_salud)){
            $regla=new Regla($this->extra->organismo_de_salud);
            $organismo_de_salud=$regla->getExpresionParaOutput($etapa->id);
        }
	
	//FORMATO
	$formato = 0;
	if(isset($this->extra->formato)){
            $regla=new Regla($this->extra->formato);
            $formato=$regla->getExpresionParaOutput($etapa->id);
        }

	$regla=new Regla($this->extra->tipo_reposo);
        $tipo_reposo=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->lugar_reposo);
        $lugar_reposo=$regla->getExpresionParaOutput($etapa->id);	
	$lugar_reposo = str_replace('"','',$lugar_reposo);
	$lugar_reposo = str_replace('[','',$lugar_reposo);
        $lugar_reposo = str_replace(']','',$lugar_reposo);	

	if(isset($this->extra->pay_date)){
            $regla=new Regla($this->extra->pay_date);
            $pay_date=$regla->getExpresionParaOutput($etapa->id);
        }
	
	$regla=new Regla($this->extra->tipo);
        $tipo =$regla->getExpresionParaOutput($etapa->id);
        

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
	
	//PAGADO ANTERIORMENTE
	$pay_earlier = $pay_earlier + $pay_advance + $pay_month_earlier;
	
		
	
	if(isset($this->extra->pay_days_not_covered)){
            $regla=new Regla($this->extra->pay_days_not_covered);
            $pay_days_not_covered=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->pay_days_not_covered_ant)){
            $regla=new Regla($this->extra->pay_days_not_covered_ant);
            $pay_days_not_covered_ant=$regla->getExpresionParaOutput($etapa->id);
        }

	$pay_days_not_covered_ant = $pay_days_not_covered_ant + $pay_days_not_covered;

	if(isset($this->extra->pay_complement)){
            $regla=new Regla($this->extra->pay_complement);
            $pay_complement=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->pay_complement_ant)){
            $regla=new Regla($this->extra->pay_complement_ant);
            $pay_complement_ant=$regla->getExpresionParaOutput($etapa->id);
        }
	$pay_complement_ant = $pay_complement_ant + $pay_complement;	

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
	if(isset($this->extra->return_value_act)){
            $regla=new Regla($this->extra->return_value_act);
            $return_value_act=$regla->getExpresionParaOutput($etapa->id);
        }
	//Return value 
	$return_value = $return_value + $return_value_act;
		
	if(isset($this->extra->return_saldo)){
            $regla=new Regla($this->extra->return_saldo);
            $return_saldo=$regla->getExpresionParaOutput($etapa->id);
        }
	if(isset($this->extra->return_observation)){
            $regla=new Regla($this->extra->return_observation);
            $return_observation=$regla->getExpresionParaOutput($etapa->id);
        }
	
	$tramite_id = $etapa->tramite_id;
  	
	$json = '{"rut": "'.$rut.'", "number": '.$numero.', "fecha_inicio": "'.$fecha_inicio.'", "fecha_termino": "'.$fecha_termino.'", ';
        $json.= '"idTramite": '.(isset($tramite_id) ? ($tramite_id == null ? '0' : $tramite_id) : '0').', ';   
	$json.= '"fecha_recepcion": '.(isset($fecha_recepcion) ? '"'.$fecha_recepcion.'"' : '""').', ';
	$json.= '"tipo_reposo": '.(isset($tipo_reposo) ? '"'.$tipo_reposo.'"' : '""').', ';
	$json.= '"placeRepose": '.(isset($lugar_reposo) ? '"'.$lugar_reposo.'"' : '""').', ';	
 	$json.= '"state": '.(isset($estado) ? '"'.$estado.'"' : '""').', ';
	$json.= '"days": '.(isset($days) ? ($days == null ? '0' : $days) : '0').', ';	
	$json.= '"type": '.(isset($tipo) ? ($tipo == null ? '0' : $tipo) : '0').', ';
	$json.= '"formate": '.(isset($formato) ? ($formato == null ? '0' : $formato) : '0').', ';	

	//rutMedico
	if(isset($rut_medico) && $rut_medico!=null )
		$json.= '"rutMedico": '.(isset($rut_medico) ? '"'.$rut_medico.'"' : '""').', ';	

	$json.= '"healthAgency": '.(isset($organismo_de_salud) ? '"'.$organismo_de_salud.'"' : '""').', ';
	$json.= '"fecha_pago": '.(isset($pay_date) ? '"'.$pay_date.'"' : '""').', ';
	$json.= '"payEarlier": '.(isset($pay_earlier) ? ($pay_earlier == null ? '0' : $pay_earlier) : '0').', ';
	$json.= '"payAdvance": '.(isset($pay_advance) ? ($pay_advance == null ? '0' : $pay_advance) : '0').', ';
	$json.= '"payMonthEarlier": '.(isset($pay_month_earlier) ? ($pay_month_earlier == null ? '0' : $pay_month_earlier) : '0').', ';
	$json.= '"payDaysNotCovered": '.(isset($pay_days_not_covered_ant) ? ($pay_days_not_covered_ant == null ? '0' : $pay_days_not_covered_ant) : '0').', ';
	$json.= '"payComplement": '.(isset($pay_complement_ant) ? ($pay_complement_ant == null ? '0' : $pay_complement_ant) : '0').', ';
	$json.= '"payObservation": '.(isset($pay_observation) ? '"'.$pay_observation.'"' : '""').', ';
	$json.= '"fecha_retorno": '.(isset($return_date) ? '"'.$return_date.'"' : '""').', ';
	$json.= '"returnValue": '.(isset($return_value) ? ($return_value == null ? '0' : $return_value) : '0').', ';
	$json.= '"returnSaldo": '.(isset($return_saldo) ? ($return_saldo == null ? '0' : $return_saldo) : '0').', ';
	$json.= '"returnObservation": '.(isset($return_observation) ? '"'.$return_observation.'"' : '""').'}';
	
	$url = urlapi."licenses";
        
	$ch = curl_init();
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

