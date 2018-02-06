<?php
require_once('campo.php');
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
        
	$display.='<script>function setMaximumSelected(amount,element) {
			var itemsSelected = [];
			for (var i=0;i<element.options.length;i++) {
				if (element.options[i].selected) itemsSelected[itemsSelected.length]=i;
			}
			if (itemsSelected.length>3) {
				itemsSelected = element.itemsSelected.split(",");
				for (i=0;i<element.options.length;i++) {
					element.options[i].selected = false;
				}
				for (i=0;i<itemsSelected.length;i++) {
					element.options[itemsSelected[i]].selected = true;
				}			
			} else {
				element.itemsSelected=itemsSelected.toString();
			}
		}</script>';
	
	if ($this->extra->ws and !$this->datos){
		$url = $this->extra->ws;
        	$ch = curl_init($url);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        	curl_setopt($ch, CURLOPT_URL,$url);
        	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        	        "Content-Type: application/json"
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
		
		$display.='<select size="35" style="width:270px" data-placeholder="Seleccione por rut o nombre"  class="chosen" id= "'.$this->id.'"  name="'.$this->nombre.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'" >';
                $display.='<option value="null"> </option>';

                foreach ($json_ws as $json){
			if($dato){
        	               	$display.='<option value="' .$json->lastName."/".$json->name.'-'.$json->rut.'-'.$json->location .'-'.$json->costCenter.'-'.$json->service.'"'.($json->lastName."/".$json->name.'-'.$json->rut.'-'.$json->location.'-'.$json->costCenter.'-'.$json->service  == $dato->valor ? 'selected' : ' ') .'>'.explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut.'</option>';
                       	}else{
                        $display.='<option value="' .$json->lastName."/".$json->name.'-'.$json->rut.'-'.$json->location .'-'.$json->costCenter.'-'.$json->service. '"'.($json->lastName."/".$json->name.'-'.$json->rut.'-'.$json->location.'-'.$json->costCenter.'-'.$json->service == $valor_default ? 'selected' : ' ') .'>'.explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut.'</option>';
                       	}                	
		}
        }else{
		$display.='<select size="35" style="width:270px" data-placeholder="Selecciona por rut o nombre" class="chosen" id= "'.$this->id.'"  name="'.$this->nombre.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'" >';
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
	
?>

<?php	

	$display.='<script>
			 $("#'.$this->id.'").chosen({ search_contains: true});
                        $(document).ready(function(){
                               
				$(".'.$this->id.'").chosen({ search_contains: true});	
				$(".chosen").chosen({
                                        allow_single_deselect: true,
					max_selected_options: 1
                                });
        
				function setMaximumSelected(amount,element) {
			var itemsSelected = [];
			for (var i=0;i<element.options.length;i++) {
				if (element.options[i].selected) itemsSelected[itemsSelected.length]=i;
			}
			if (itemsSelected.length>3) {
				itemsSelected = element.itemsSelected.split(",");
				for (i=0;i<element.options.length;i++) {
					element.options[i].selected = false;
				}
				for (i=0;i<itemsSelected.length;i++) {
					element.options[itemsSelected[i]].selected = true;
				}			
			} else {
				element.itemsSelected=itemsSelected.toString();
			}
		}
	});
        </script>';

	

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

        return $html;
    }
    
    public function backendExtraValidate(){
        $CI=&get_instance();
        //$CI->form_validation->set_rules('datos','Datos','required');
    }

}
