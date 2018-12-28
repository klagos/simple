<?php
require_once('campo.php');
require_once(routecontrollers.'authorization.php');

class CampoChosenUnitario extends Campo {
    
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

        $oa = new Authorization();
        $token = $oa->getToken(); 
       
		//Obtener data de WS	
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
		
                $placeholder = "";
                $json_ws = "";
                $flag_json = false;
                $flag_placeholder = false;

                for ($i = 0; $i < strlen($result); $i++){
                        if ($result[$i] and $flag_placeholder){
                                if ($result[$i] == ":") $flag_placeholder = false;
                                if ($flag_placeholder) $placeholder .= $result[$i];
                        }
                        if ($result[$i] == "[") $flag_json = true; //empieza json
                        if ($flag_json) $json_ws .= $result[$i];
                        if ($result[$i] == "{" and !$placeholder) $flag_placeholder = true;
                        if ($result[$i] == "]") $flag_json = false; //termina json
                }
                $placeholder = preg_replace('/\n+/', '',$placeholder); //eliminar espacios de placeholder
		$json_ws = json_decode($json_ws);
		
		//SELECT
		$display.='<select size="35" style="width:380px" data-placeholder='.$placeholder.'  class="chosen" id= "'.$this->id.'"  name="'.$this->nombre.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'" >';
                $display.='<option value="null"> </option>';

                foreach ($json_ws as $json){
			if($dato){
        	               	$display.='<option value="' .$json->name.'"'.($json->name == $dato->valor ? 'selected' : ' ') .'>'.$json->name.'</option>';
                       	}else{
                        $display.='<option value="' .$json->name.'"'.($json->name  == $valor_default ? 'selected' : ' ') .'>'.$json->name.'</option>';
                       	}                	
		}
        }else{
		$display.='<select size="35" style="width:380px" data-placeholder="Selecciona una opción" class="chosen" id= "'.$this->id.'"  name="'.$this->nombre.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'" >';
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
		
	if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';


        return $display;
    }

    public function backendExtraFields(){
        $ws=isset($this->extra->ws)?$this->extra->ws:null;

        $html='<label>URL para cargar opciones desde webservice (Opcional)</label>';
        $html.='<input class="input-xxlarge" name="extra[ws]" value="'.$ws.'" />';
        $html.='<div class="help-block">
                El WS debe ser REST JSONP con el siguiente formato: <a href="#" onclick="$(this).siblings(\'pre\').show()">Ver formato</a><br />
                <pre style="display:none">
placeholder_del_campo
[
    {
        "etiqueta": "Etiqueta 1",
        "valor": "Valor 1"
    },
    {
        "etiqueta": "Etiqueta 2",
        "valor": "Valor 2"
    },
]
                </pre>
                </div>';

        return $html;
    }
    
    public function backendExtraValidate(){
        $CI=&get_instance();
        //$CI->form_validation->set_rules('datos','Datos','required');
    }

}
