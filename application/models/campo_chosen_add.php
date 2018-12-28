<?php
require_once('campo.php');
require_once(routecontrollers.'authorization.php');

class CampoChosenAdd extends Campo {
    	
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

	//Obtener data desde WS
	if ($this->extra->ws and !$this->datos){
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

                $display.='<select size="35" style="width:380px" data-placeholder="Seleccione por rut o nombre"  class="chosen" id="'.$this->id.'"  name="'.$this->nombre.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'" >';
                $display.='<option value="null"> </option>';

                foreach ($json_ws as $json){
			if($dato){
                                $display.='<option value="' .$json->lastName."/".$json->name.'_'.$json->rut.'_'.$json->location .(isset($json->day)?'_'.$json->day:'').(isset($json->halfDay)?'_'.$json->halfDay:'').(isset($json->takenDays)?'_'.$json->takenDays:'').(isset($json->pendingDays)?'_'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'_'.$json->pendingHalfDays:''). (isset($json->costCenter)?'_'. $json->costCenter :''). (isset($json->service)? '_'.$json->service : '').(isset($json->email)?'_'.$json->email:'')  .'"'.($json->lastName."/".$json->name.'_'.$json->rut.'_'.$json->location.(isset($json->day)?'_'.$json->day:'').(isset($json->halfDay)?'_'.$json->halfDay:'').(isset($json->takenDays)?'_'.$json->takenDays:'').(isset($json->pendingDays)?'_'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'_'.$json->pendingHalfDays:''). (isset($json->costCenter)?'_'. $json->costCenter :''). (isset($json->service)? '_'.$json->service : '').(isset($json->email)?'_'.$json->email:'')  == $dato->valor ? 'selected' : ' ') .'>'.explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut.'</option>';
                        }else{
                                $display.='<option value="' .$json->lastName."/".$json->name.'_'.$json->rut.'_'.$json->location .(isset($json->day)?'_'.$json->day:'').(isset($json->halfDay)?'_'.$json->halfDay:'').(isset($json->takenDays)?'_'.$json->takenDays:'').(isset($json->pendingDays)?'_'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'_'.$json->pendingHalfDays:''). (isset($json->costCenter)?'_'. $json->costCenter :''). (isset($json->service)? '_'.$json->service : '').(isset($json->email)?'_'.$json->email:'')  .'"'.($json->lastName."/".$json->name.'_'.$json->rut.'_'.$json->location.(isset($json->day)?'_'.$json->day:'').(isset($json->halfDay)?'_'.$json->halfDay:'').(isset($json->takenDays)?'_'.$json->takenDays:'').(isset($json->pendingDays)?'_'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'_'.$json->pendingHalfDays:''). (isset($json->costCenter)?'_'. $json->costCenter :''). (isset($json->service)? '_'.$json->service : '').(isset($json->email)?'_'.$json->email:'')  == $valor_default ? 'selected' : ' ') .'>'.explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut.'</option>';
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
	
	//MODAL y button que despliega modal para agregar un usuario
	$display.='<button id="add-button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"><span class="glyphicon glyphicon-plus"></span></button>
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Usuario</h5>
        <button type="button" id="close-modal" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
<div class="modal-body">
        <div class="validacion"></div>
	  <h5>Datos Personales</h5>
          <div class="form-group">
	    <label for="rut" class="control-label">Rut &nbsp&nbsp</label>
            <input type="text"  name="rut" class="input-semi-large" placeholder="Ingrese RUT con guión y dígito verificador : 16755073-8" id="rut">
          </div>
          <div class="form-group">
            <label for="name" class="control-label">Nombres &nbsp&nbsp</label>
            <input type="text" class="input-semi-large" placeholder="Ingrese ambos nombres Ej:Juan Ignacio" id="name">
          </div>
	  <div class="form-group">
            <label for="lastName" class="control-label">Apellidos &nbsp&nbsp</label>
            <input type="text" class="input-semi-large" placeholder="Ingrese appellido paterno materno Ej: Perez Rodriguez" id="lastName">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary"  data-dismiss="modal">Cerrar</button>
	<button type="button" style="display:none" data-dismiss="modal" id="guardar" onclick="Save()" class="btn btn-primary">Agregar</button>
      </div>
    </div>
  </div>
</div>';
	$display.=
	'<script>
		 $("#'.$this->id.'").chosen({ search_contains: true});
		//enviar post a apiary con los datos ingresados del usuario
		function Save(){
		var url = "http://private-120a8-apisimpleist.apiary-mock.com/users";
		var rut = document.getElementById("rut").value;
		var name = document.getElementById("name").value.toUpperCase();
		var lastName = document.getElementById("lastName").value.toUpperCase();
		fetch(url,{
			method: "post",
			headers: {
				"Content-type": "application/json"
			},
			body: JSON.stringify({name: name, lastName: lastName, rut: rut, isIST: "false"})
		});
		
		//agrega la nueva opción al chosen y lo actualiza
		var opt = document.createElement("option");
		opt.text = name.split(" ")[0]+" "+lastName+" - "+rut;
		opt.setAttribute("value",lastName+"/"+name+"_"+rut);
		document.getElementById("'.$this->id.'").appendChild(opt); 
	
		$("#'.$this->id.'").val(lastName+"/"+name+"_"+rut).change();
		$("#'.$this->id.'").data("chosen").default_text = name.split(" ")[0]+" "+lastName+" - "+rut;	
		$("#'.$this->id.'").trigger("liszt:updated"); //se actualiza chosen
		//limpia los datos del modal	
		var rut = document.getElementById("rut").value = "";
                var name = document.getElementById("name").value = "";
                var lastName = document.getElementById("lastName").value = "";
		}
	

		//verificador de rut
		var Fn = {
	// Valida el rut con su cadena completa "XXXXXXXX-X"
	validaRut : function (rutCompleto) {
		if (!/^[0-9]+[-|‐]{1}[0-9kK]{1}$/.test( rutCompleto ))
			return false;
		var tmp 	= rutCompleto.split("-");
		var digv	= tmp[1]; 
		var rut 	= tmp[0];
		if ( digv == "K" ) digv = "k" ;
		return (Fn.dv(rut) == digv );
	},
	dv : function(T){
		var M=0,S=1;
		for(;T;T=Math.floor(T/10))
			S=(S+T%10*(9-M++%6))%11;
		return S?S-1:"k";
	}
}

		//mostrar boton de agregar cuando todos los campos son llenados y con un rut valido
		$("#name").on("input", function() {
                           if (document.getElementById("rut").value && document.getElementById("name").value && document.getElementById("lastName").value &&
			   Fn.validaRut(document.getElementById("rut").value) && (/^[a-zA-Z ]+$/.test(document.getElementById("name").value)) && (/^[a-zA-Z ]+$/.test(document.getElementById("lastName").value)))
				document.getElementById("guardar").style.display = "inline";
			   else  
				document.getElementById("guardar").style.display = "none"; 
                });
		$("#lastName").on("input", function() {
                           if (document.getElementById("rut").value && document.getElementById("name").value && document.getElementById("lastName").value &&
			   Fn.validaRut(document.getElementById("rut").value) && (/^[a-zA-Z ]+$/.test(document.getElementById("name").value)) && (/^[a-zA-Z ]+$/.test(document.getElementById("lastName").value)))
                                document.getElementById("guardar").style.display = "inline";
                           else
                                document.getElementById("guardar").style.display = "none";
                });
		$("#rut").on("input", function() {
                           if (document.getElementById("rut").value && document.getElementById("name").value && document.getElementById("lastName").value &&
			   Fn.validaRut(document.getElementById("rut").value) && (/^[a-zA-Z ]+$/.test(document.getElementById("name").value)) && (/^[a-zA-Z]+$/.test(document.getElementById("lastName").value)))
                                document.getElementById("guardar").style.display = "inline";
                           else
                                document.getElementById("guardar").style.display = "none";
                });

		//Estilo y posicion del botton plus y del modal
		document.getElementById("add-button").style.margin = "-20px 0px 0px 200px";
		document.getElementById("close-modal").style.margin = "-30px 0px 0px 0px";	
		document.getElementById("rut").style.margin = "0px 0px 10px 10px";
		document.getElementById("name").style.margin = "0px 0px 10px 10px";
		document.getElementById("lastName").style.margin = "0px 0px 10px 10px";


</script>'; 
?>

<?php
	

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
    }

}
