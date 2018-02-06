<?php
require_once('campo.php');
class CampoTextAdd extends Campo {
	
    protected function display($modo, $dato, $etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }else{
            $valor_default=json_decode($this->valor_default);
        }

	//GET RUT-NAME DATA
	$url = 'http://private-120a8-apisimpleist.apiary-mock.com/users/list/small';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
            ));
	$result=curl_exec($ch);
	curl_close($ch);

	$rutUser_json = (json_decode($result));

	//GET LOCATION DATA
	$url = 'http://private-120a8-apisimpleist.apiary-mock.com/location/list';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
            ));
        $result=curl_exec($ch);
        curl_close($ch);

        $location_json = (json_decode($result));
	
	//GET COST CENTER DATA
	$url = 'http://private-120a8-apisimpleist.apiary-mock.com/costcenter/list';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
            ));
        $result=curl_exec($ch);
        curl_close($ch);

        $costCenter_json = (json_decode($result));

	 //GET SERVICE DATA
        $url = 'http://private-120a8-apisimpleist.apiary-mock.com/service/list';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
            ));
        $result=curl_exec($ch);
        curl_close($ch);

        $service_json = (json_decode($result));
		
        $display = '<label class="control-label" for="'.$this->id.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '' : ' (Opcional)') . '</label>';
  
	$display.= '<div class="controls">';
        
	//Campo select
	$display.='<select size="35" style="width:270px" data-placeholder="Selecciona por rut o nombre"  class="chosen" id= "'.$this->id.'"  name="'.$this->nombre.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'" >';
	$display.='<option value="null"> </option>';	
	if($rutUser_json) for ($cont = 0; $cont < count($rutUser_json); $cont++) {
		if($dato){
		$display.='<option value="' .$rutUser_json[$cont]->lastName." ".$rutUser_json[$cont]->name.'-'.$rutUser_json[$cont]->rut.'-'.$rutUser_json[$cont]->location .'-'.$rutUser_json[$cont]->costCenter.'-'.$rutUser_json[$cont]->service. '"'.($rutUser_json[$cont]->lastName." ".$rutUser_json[$cont]->name.'-'.$rutUser_json[$cont]->rut.'-'.$rutUser_json[$cont]->location == $dato->valor ? 'selected' : ' ') .'>'.$rutUser_json[$cont]->lastName.' '.$rutUser_json[$cont]->name.' - '.$rutUser_json[$cont]->rut.'</option>';
		}else{
		$display.='<option value="' .$rutUser_json[$cont]->lastName." ".$rutUser_json[$cont]->name.'-'.$rutUser_json[$cont]->rut.'-'.$rutUser_json[$cont]->location . '-'.$rutUser_json[$cont]->costCenter.'-'.$rutUser_json[$cont]->service.'"'.($rutUser_json[$cont]->lastName." ".$rutUser_json[$cont]->name.'-'.$rutUser_json[$cont]->rut.'-'.$rutUser_json[$cont]->location == $valor_default ? 'selected' : ' ') .'>'.$rutUser_json[$cont]->lastName.' '.$rutUser_json[$cont]->name.' - '.$rutUser_json[$cont]->rut.'</option>';

		}
	}
	$display.='</select>';

	//button que despliega modal para agregar un usuario
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
            <input type="text" class="input-semi-large" placeholder="Ingrese ambos nombres" id="name">
          </div>
	  <div class="form-group">
            <label for="lastName" class="control-label">Apellidos &nbsp&nbsp</label>
            <input type="text" class="input-semi-large" placeholder="Ingrese appellido paterno materno" id="lastName">
          </div>
	  <h5>Datos Administrativos</h5>
	  <div >
            <label id="loc-label" for="location" class="control-label">Localidad &nbsp&nbsp </label>
            <select class="chosen" id="location"  data-placeholder="Selecciona una localidad">
	    <option value ="null"></option>';
	
	foreach ($location_json as $loc) $display .= '<option value="'.$loc->code.'">'.$loc->name.'</option>';
	
	$display.='    </select>
          </div>
	<br>
	  <div class="form-group">
            <label for="costCenter" class="control-label">Centro de costo &nbsp&nbsp </label>
            <select class="chosen" id="costcenter" data-placeholder="Selecciona un cost center">
            <option value ="null"></option>';

        foreach ($costCenter_json as $costC) $display .= '<option value="'.$costC->code.'">'.$costC->name.'</option>';
        $display.='    </select>
          </div>
	<br>
	<div class="form-group">
            <label for="service" class="control-label">Servicio &nbsp&nbsp </label>
            <select class="chosen" id="service" data-placeholder="Selecciona un servicio">
            <option value ="null"></option>';

        foreach ($service_json as $serv) $display .= '<option value="'.$serv->name.'">'.$serv->name.'</option>';
        $display.='    </select>
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
		//evitar superposicion del modal con select
		$("#exampleModal").modal("show");
		$("#exampleModal").modal("hide");

		//enviar post a apiary con los datos ingresados del usuario
		function Save(){
		var url = "http://private-120a8-apisimpleist.apiary-mock.com/users";
		var rut = document.getElementById("rut").value;
		var name = document.getElementById("name").value;
		var lastName = document.getElementById("lastName").value;
		var location = document.getElementById("location").value;
		var costCenter = document.getElementById("costcenter").value;
		var service = document.getElementById("service").value;
		fetch(url,{
			method: "post",
			headers: {
				"Content-type": "application/json"
			},
			body: JSON.stringify({name: name, lastName: lastName, rut: rut, locationCode: location, costCenterCode: costCenter, service: service})
		});
		window.location.reload(true);
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
			   (document.getElementById("location").value != "null") && (document.getElementById("costcenter").value != "null") &&
			   Fn.validaRut(document.getElementById("rut").value) && (/^[a-zA-Z ]+$/.test(document.getElementById("name").value)) && (/^[a-zA-Z ]+$/.test(document.getElementById("lastName").value)))
				document.getElementById("guardar").style.display = "inline";
			   else  
				document.getElementById("guardar").style.display = "none"; 
                });
		$("#lastName").on("input", function() {
                           if (document.getElementById("rut").value && document.getElementById("name").value && document.getElementById("lastName").value &&
                           (document.getElementById("location").value != "null") && (document.getElementById("costcenter").value != "null") &&
			   Fn.validaRut(document.getElementById("rut").value) && (/^[a-zA-Z ]+$/.test(document.getElementById("name").value)) && (/^[a-zA-Z ]+$/.test(document.getElementById("lastName").value)))
                                document.getElementById("guardar").style.display = "inline";
                           else
                                document.getElementById("guardar").style.display = "none";
                });
		$("#rut").on("input", function() {
                           if (document.getElementById("rut").value && document.getElementById("name").value && document.getElementById("lastName").value &&
                           (document.getElementById("location").value != "null") && (document.getElementById("costcenter").value != "null") && 
			   Fn.validaRut(document.getElementById("rut").value) && (/^[a-zA-Z ]+$/.test(document.getElementById("name").value)) && (/^[a-zA-Z]+$/.test(document.getElementById("lastName").value)))
                                document.getElementById("guardar").style.display = "inline";
                           else
                                document.getElementById("guardar").style.display = "none";
                });
	 	$(document).on("change","#location",function(){
               	 	if (document.getElementById("rut").value && document.getElementById("name").value && document.getElementById("lastName").value &&
                           (document.getElementById("location").value != "null") && (document.getElementById("costcenter").value != "null") &&
			Fn.validaRut(document.getElementById("rut").value) && (/^[a-zA-Z ]+$/.test(document.getElementById("name").value)) && (/^[a-zA-Z ]+$/.test(document.getElementById("lastName").value)))
                                document.getElementById("guardar").style.display = "inline";
                           else
                                document.getElementById("guardar").style.display = "none";
         	});	
		$(document).on("change","#costcenter",function(){
                        if (document.getElementById("rut").value && document.getElementById("name").value && document.getElementById("lastName").value &&
                           (document.getElementById("location").value != "null") && (document.getElementById("costcenter").value != "null") &&
			   Fn.validaRut(document.getElementById("rut").value) && (/^[a-zA-Z ]+$/.test(document.getElementById("name").value)) && (/^[a-zA-Z ]+$/.test(document.getElementById("lastName").value)))
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
    }

}
