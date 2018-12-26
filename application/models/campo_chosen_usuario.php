<?php
require_once('campo.php');
require_once('/var/www/html/simple/application/controllers/authorization.php');
class CampoChosenUsuario extends Campo {
    
    protected function display($modo, $dato, $etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }else{
            $valor_default=json_decode($this->valor_default);
        }
	$display = '<label class="control-label" for="'.$this->id.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '' : ' (Opcional)') . '</label>';
  
	$display.= '<div class="controls">';
       	
	if ($this->extra->ws and !$this->datos){
		if (isset($this->extra->cache) and isset($this->extra->timeLiveCache)) 
			$json_ws = apcu_fetch('json_list_users');
		else
			$json_ws = false;
        	if (!$json_ws){
        	
        	$oa = new Authorization();
	        $token = $oa->getToken();
	        
			$url = $this->extra->ws;
	        	$ch = curl_init($url);
	        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        	curl_setopt($ch, CURLOPT_URL,$url);
	        	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	        	        "Content-Type: application/json",
	        	        "Authorization: Bearer ".$token
	        	    ));
	        	$result=curl_exec($ch);
	       		curl_close($ch);
			$json_ws = json_decode($result);
			if (isset($this->extra->cache) and isset($this->extra->timeLiveCache))
				apcu_add('json_list_users',$json_ws,60*$this->extra->timeLiveCache);
		}

		$display.='<select size="35" style="width:380px" data-placeholder="Selecciona por rut o nombre"  class="chosen" id= "'.$this->id.'"  name="'.$this->nombre.'" ' . ($modo == 'visualizacion' ? 'disabled' : '') . ' data-modo="'.$modo.'" >';
                $display.='<option value="null"> </option>';
                if ($json_ws)
		foreach ($json_ws as $json){
			if($dato){
        	               	$display.='<option value="' .$json->lastName."/".$json->name.'_'.$json->rut.'_'.$json->location .(isset($json->day)?'_'.$json->day:'').(isset($json->halfDay)?'_'.$json->halfDay:'').(isset($json->takenDays)?'_'.$json->takenDays:'').(isset($json->pendingDays)?'_'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'_'.$json->pendingHalfDays:''). (isset($json->costCenter)?'_'. $json->costCenter :''). (isset($json->service)? '_'.$json->service : '').(isset($json->email)?'_'.$json->email:'')  .'"'.($json->lastName."/".$json->name.'_'.$json->rut.'_'.$json->location.(isset($json->day)?'_'.$json->day:'').(isset($json->halfDay)?'_'.$json->halfDay:'').(isset($json->takenDays)?'_'.$json->takenDays:'').(isset($json->pendingDays)?'_'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'_'.$json->pendingHalfDays:''). (isset($json->costCenter)?'_'. $json->costCenter :''). (isset($json->service)? '_'.$json->service : '').(isset($json->email)?'_'.$json->email:'')  == $dato->valor ? 'selected' : ' ') .'>'.explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut.'</option>';
                       	}else{
				$display.='<option value="' .$json->lastName."/".$json->name.'_'.$json->rut.'_'.$json->location .(isset($json->day)?'_'.$json->day:'').(isset($json->halfDay)?'_'.$json->halfDay:'').(isset($json->takenDays)?'_'.$json->takenDays:'').(isset($json->pendingDays)?'_'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'_'.$json->pendingHalfDays:''). (isset($json->costCenter)?'_'. $json->costCenter :''). (isset($json->service)? '_'.$json->service : '').(isset($json->email)?'_'.$json->email:'')  .'"'.($json->lastName."/".$json->name.'_'.$json->rut.'_'.$json->location.(isset($json->day)?'_'.$json->day:'').(isset($json->halfDay)?'_'.$json->halfDay:'').(isset($json->takenDays)?'_'.$json->takenDays:'').(isset($json->pendingDays)?'_'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'_'.$json->pendingHalfDays:''). (isset($json->costCenter)?'_'. $json->costCenter :''). (isset($json->service)? '_'.$json->service : '').(isset($json->email)?'_'.$json->email:'')  == $valor_default ? 'selected' : ' ') .'>'.explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut.'</option>';
                       	}                	
		}
        }else{
		$display.='<select size="35" style="width:380px" data-placeholder="Selecciona por rut o nombre" class="chosen" id= "'.$this->id.'"  name="'.$this->nombre.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'" >';
                $display.='<option value="null"> </option>';

                        if($this->datos) foreach ($this->datos as $d) {
                                if($dato){
                                        $display.='<option value="' . $d->valor . '" ' . ($dato && $d->valor == $dato->valor ? 'selected' : '') . '>' . $d->etiqueta . '</option>';
                                }else{
                                        $display.='<option value="' . $d->valor . '" ' . ($d->valor == $valor_default ? 'selected' : '') . '>' . $d->etiqueta . '</option>';
                                }                                                   	
			}
	}
        $display.='</select> <input name"'. $this->nombre.'"id="'.$this->nombre.'" type=hidden value""/>';
	
	$display .= '';
	if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';
	$display .= '<script>
		 $("#'.$this->id.'").chosen({ search_contains: true});
		   </script>';
	
        return $display;
    }

    public function backendExtraFields(){
        $ws=isset($this->extra->ws)?$this->extra->ws:null;

        $html='<label>URL para cargar opciones desde webservice (Opcional)</label>';
        $html.='<input class="input-xxlarge" name="extra[ws]" value="'.$ws.'" />';
        $html.='<div class="help-block">
                El WS debe ser REST JSONP con el siguiente formato: <a href="#" onclick="$(this).siblings(\'pre\').show()">Ver formato</a><br />
                <pre style="display:none">
{
	placeholder_del_campo: [

        	{	
        		"etiqueta": "Etiqueta 1",
        		"valor": "Valor 1"
    		},
    		{
     			"etiqueta": "Etiqueta 2",
        		"valor": "Valor 2"
    		},
	]
}
                </pre>
                </div>';
	$html.='<label id="cache" class="checkbox"><input type="checkbox" name="extra[cache]" value="1" '.(isset($this->extra->cache)?"checked":"").' /> Leer del caché</label>';
	
	$html .= '<script> $("#cache :checkbox").change(function() {
			        if (!$(this).attr("checked")){
					document.getElementById("label_time").style.display = "none";
					document.getElementById("input_time").style.display = "none";
				} else {
					document.getElementById("label_time").style.display = "block";
                                        document.getElementById("input_time").style.display = "block";
				}
			   });  
		</script>
		
		<label id = "label_time">Tiempo (en minutos) que permanecerán los datos cargados del ws en cache </label>';
	$html.='<input id="input_time" type="text" name="extra[timeLiveCache]" placeholder="Tiempo en minutos" value="'.(isset($this->extra->timeLiveCache)?$this->extra->timeLiveCache:null).'" />';
        return $html;
    }
    
    public function backendExtraValidate(){
        $CI=&get_instance();
        //$CI->form_validation->set_rules('datos','Datos','required');
    }

}
