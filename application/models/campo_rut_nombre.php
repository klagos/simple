<?php
require_once('campo.php');
class CampoTextRut extends Campo {
    
    protected function display($modo, $dato, $etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }else{
            $valor_default=json_decode($this->valor_default);
        }

	$url = "http://nexoya.cl:8080/apiSimple/users/list/small";//"http://private-anon-6dbb3df949-apisimple1.apiary-mock.com/users/list/small";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
            ));
	$result=curl_exec($ch);
	curl_close($ch);

	$array_json = (json_decode($result));
		
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

	
	$display.='<select size="35" data-placeholder="rut - nombre"  class="chosen" id= "'.$this->id.'"  name="'.$this->nombre.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'" >';
	$display.='<option value="null"> </option>';	
	if($array_json) for ($cont = 0; $cont < count($array_json); $cont++) {
		$display.='<option value="' .$array_json[$cont]->rut . '"'.($dato && strpos( $dato->valor,$array_json[$cont]->email)!== false ? 'selected' : '') .'>'.$array_json[$cont]->lastName.' '.
																$array_json[$cont]->name.' - '.$array_json[$cont]->rut.'</option>';
	}
        $display.='</select> <input name"'. $this->nombre.'"id="'.$this->nombre.'" type=hidden value""/>';
	
$c = "test";
?>
<script>
var a = '<?php echo $c;?>';

</script>
<?php $a = '<script>a</script>';

echo $a;
?>

<?php	

	$display.='<script>
                        $(document).ready(function(){
                               
			
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


        if($this->extra && $this->extra->ws){
            $display.='
            <script>
                $(document).ready(function(){
                    var defaultValue = "'.($dato && $dato->valor?$dato->valor:$this->valor_default).'";
                    console.log(defaultValue);
                    $.ajax({
                        url: "'.$this->extra->ws.'",
                        dataType: "jsonp",
                        jsonpCallback: "callback",
                        success: function(data){
                            var html="";
                            $(data).each(function(i,el){
                                html+="<option value=\""+el.valor+"\">"+el.etiqueta+"</option>";
                            });

                            $("#'.$this->id.'").append(html).val(defaultValue).change();
                        }
                    });
                });

            </script>';
        }

        return $display;
    }

    public function backendExtraFields(){
        $ws=isset($this->extra->ws)?$this->extra->ws:null;

        $html='<label>URL para cargar opciones desde webservice (Opcional)</label>';
        $html.='<input class="input-xxlarge" name="extra[ws]" value="'.$ws.'" />';
        $html.='<div class="help-block">
                El WS debe ser REST JSONP con el siguiente formato: <a href="#" onclick="$(this).siblings(\'pre\').show()">Ver formato</a><br />
                <pre style="display:none">
callback([
    {
        "etiqueta": "Etiqueta 1",
        "valor": "Valor 1"
    },
    {
        "etiqueta": "Etiqueta 2",
        "valor": "Valor 2"
    },
])
                </pre>
                </div>';

        return $html;
    }
    
    public function backendExtraValidate(){
        $CI=&get_instance();
        //$CI->form_validation->set_rules('datos','Datos','required');
    }

}
