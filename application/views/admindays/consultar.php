<?php
require_once(FCPATH."procesos.php");
?>

<h2 style="line-height: 28px;">
    Consulta días administrativo - Adicionales

<br><br>

</h2>

	<h4>Trabajador a consultar</h4>  <br> <select  size="35" style="width:380px" data-placeholder="Seleccione por rut o nombre" class="chosen"  id="consulta_admin_days">
                
		<option value="null"> </option>
	<?php
                foreach ($json_list_users  as $json){
			//seleccionar trabajador cuando se vuelve a consulta luego de pedir un dia admin
			if ($json->rut == explode("=",$_SERVER['REQUEST_URI'])[1]){ 
        ?>                
<option selected value = '<?php echo $json->lastName."/".$json->name.'-'.$json->rut.'-'.$json->location .'-'.(($json->day)?$json->day:'0').'-'.(($json->halfDay)?$json->halfDay:'0').(isset($json->takenDays)?'-'.$json->takenDays:'').(isset($json->pendingDays)?'-'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'-'.$json->pendingHalfDays:''). (isset($json->adminDayRequest)?'-'.json_encode($json->adminDayRequest):'').'-'.(($json->hasDay)?'si' :'no'). (isset($json->service)? '-'.$json->service : '').(isset($json->email)?'-'.$json->email:'') ?>'> <?php echo explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut ?> </option>
        <?php   	} else {	?>
<option value = '<?php echo $json->lastName."/".$json->name.'-'.$json->rut.'-'.$json->location .'-'.(($json->day)?$json->day:'0').'-'.(($json->halfDay)?$json->halfDay:'0').(isset($json->takenDays)?'-'.$json->takenDays:'').(isset($json->pendingDays)?'-'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'-'.$json->pendingHalfDays:''). (isset($json->adminDayRequest)?'-'.json_encode($json->adminDayRequest):'').'-'.(($json->hasDay)?'si' :'no'). (isset($json->service)? '-'.$json->service : '').(isset($json->email)?'-'.$json->email:'') ?>'> <?php echo explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut ?> </option>

	<?php		}             
		}  
	 ?>
        </select>

<br>

<table>
<tr>
	<td><h4> Datos Trabajador </h4></td>
</tr>
<br></br>
<tr style="text-align: right;">
	<td>Nombre &nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td> <input id="nombre_trabajador" type="text" class="input-semi-large" name="nombre_trabajador"  readonly></td>	
</tr>
<tr style="text-align: right;">

	<td>Rut &nbsp;&nbsp;&nbsp;&nbsp; </td>
	<td><input id="rut_trabajador" type="text" class="input-semi-large" name="rut_trabajador"  readonly> </td>
</tr>
<tr style="text-align: right;">

	<td>Localidad  &nbsp;&nbsp;&nbsp;&nbsp; </td> 
	<td><input id="localidad_trabajador" type="text" class="input-semi-large" name="localidad_trabajador"  readonly></td>

</tr>
</table>
<table id="table_dias" style="display:none">
<tr>
	<td><h4> Asignados </h4> </td>
</tr>
<tr style="text-align: right;">
	<td>Días &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <input id="dias_asignados" type="text" class="input-semi-large" name="dias_asignados"  readonly></td>
</tr>
<tr style="text-align: right;">

        <td>Medias Jornadas &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="medias_jornadas_asignadas" type="text" class="input-semi-large" name="medias_jornadas_asignadas" readonly></td>
</tr>
<tr>
	<td><h4> Tomados </h4></td>
</tr>
<tr style="text-align: right;">
        <td>Días Tomados  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="dias_tomados" type="text" class="input-semi-large" name="dias_tomados"  readonly></td>

</tr>
<tr>
	<td><h4> Disponibles </h4> </td>
</tr>
<tr style="text-align: right;">
	<td>Días disponibles &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <input id="dias_disponibles" type="text" class="input-semi-large" name="dias_disponibles"  readonly></td>
</tr>
<tr>
        <td>Medias jornadas disponibles &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="medias_jornadas_disponibles" type="text" class="input-semi-large" name="medias_jornadas_disnponibles" readonly> </td>
</tr>
</table>

<br></br>

<h4>
<a href="#"  id="link_historial" onclick="return mostrarHistorial();">Mostrar Historial »</a>
</h4>

<div class="historial">
    <table class="table">
        <thead id = "rows">
        </thead>
    </table>
</div>

<br></br>

<table id="iniciarSolicitud" class="table">
    <tbody>
        <?php foreach ($procesos as $p): ?>
	    <?php if ($p->nombre == "Solicitud de días administrativos"){?>
            <tr>
                <td class="actions">
                    <?php if($p->canUsuarioIniciarlo(UsuarioSesion::usuario()->id)):?>
                    <a href="#" onclick= "window.location= '/tramites/iniciar/'+<?php echo proceso_dias_admin_id?>+'/' + document.getElementById('rut_trabajador').value" class="btn btn-primary preventDoubleRequest"><i class="icon-file icon-white"></i> Iniciar Solicitud</a>
                    <?php else: ?>
                        <?php if($p->getTareaInicial()->acceso_modo=='claveunica'):?>
                        <a href="<?=site_url('autenticacion/login_openid')?>?redirect=<?=site_url('tramites/iniciar/'.$p->id)?>" ><img style="max-width: none;" src="<?=base_url('assets/img/claveunica-medium.png')?>" alt="ClaveUnica" /></a>
                        <?php else:?>
                        <a href="<?=site_url('autenticacion/login')?>?redirect=<?=site_url('tramites/iniciar/'.$p->id)?>" class="btn btn-primary"><i class="icon-white icon-off"></i> Autenticarse</a>
                        <?php endif ?>
                    <?php endif ?>
                </td>
            </tr>
	<?php } ?>
        <?php endforeach; ?>
    </tbody>
</table>

<div id ="msg"> </div>
<div id="modal" class="modal hide fade" > </div>

<script>

//parametros
var idCampoRutUser = "consulta_admin_days";
var idCampoRut = "rut_trabajador";
var idCampoName = "nombre_trabajador";
var idCampoLocation = "localidad_trabajador";
var idCampoDiasAsig = "dias_asignados";
var idCampoMedJor = "medias_jornadas_asignadas";
var idCampoDiasTom = "dias_tomados";
var idCampoDiasDis = "dias_disponibles";
var idCampoMedJorDis = "medias_jornadas_disponibles";
var urlapi = '<?php echo urlapi?>';

//permitir un match de mas de 1 palabra (por 2 apellidos por ej)
$("#"+idCampoRutUser).chosen({ search_contains: true});

//ocultar historial
document.getElementById("link_historial").style.display = "none";

//ocultar boton para iniciar solicitud de dias administrativos
document.getElementById("iniciarSolicitud").style.display = "none";

//rellenar campos con el trabajador seleccionado
//cuando se vuelve a consulta luego de pedir un dia admin
if (document.getElementById(idCampoRutUser).value) {
	cargarDatos();
}
//si se elige un valor, llama a la funcion
document.getElementById(idCampoRutUser).onchange = function(){
	cargarDatos();
	}

//funcion para rellenar campos con un trabajador seleccionado
function cargarDatos(){
	//se rellenan los campos con el valor elegido
	var valorSelected =  document.getElementById(idCampoRutUser).value.split("-");
	document.getElementById(idCampoRut).value =  valorSelected[1].concat("-".concat(valorSelected[2]));
	document.getElementById(idCampoName).value =  valorSelected[0].split("/")[1] +  " " + valorSelected[0].split("/")[0];
	document.getElementById(idCampoLocation).value =  valorSelected[3].toUpperCase();
	document.getElementById(idCampoDiasAsig).value =  valorSelected[4];
	document.getElementById(idCampoMedJor).value =  valorSelected[5];
	//console.log(document.getElementById(idCampoRutUser).value.split("-"));	
	var json = '';

	if(valorSelected[6]=='si'){	
		
		//Desplegar la tabla
		var lTable = document.getElementById("table_dias");
		lTable.style.display =  "table";
		
		document.getElementById("msg").innerHTML ="";		

		//obtener historial del usuario seleccionado	
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
        	if (this.readyState == 4 && this.status == 200) {
    			json = JSON.parse(this.responseText);
			
			//rellenar campos con los valores del http request
			document.getElementById(idCampoDiasTom).value =  json.takenDays;
        		document.getElementById(idCampoDiasDis).value =  json.pendingDays;
     			document.getElementById(idCampoMedJorDis).value =  json.pendingHalfDays;

			//si no quedan dias disponibles, no se puede iniciar solicitud
        		if (json.pendingDays == 0){
                		document.getElementById("iniciarSolicitud").style.display = "none";
                		document.getElementById("msg").innerHTML = "<h4>Al trabajador no le quedan dias disponibles</h4>";
        		}else{
                		document.getElementById("iniciarSolicitud").style.display = "inline";
                		document.getElementById("msg").innerHTML = "";
        		}
	
			//mostrar historial si existe más de un valor, sino se oculta	
			if (json.history.length > 0){
	        	         document.getElementById("link_historial").style.display = "inline";
	        	         document.getElementById("rows").innerHTML = '<tr><th>Fecha</th><th>Tipo solicitud</th></tr>';
	        	}else{
	        	         document.getElementById("link_historial").style.display = "none";
	        	         document.getElementById("rows").innerHTML = '';
	        	}

				//lista auxiliar para ordenar los dias
				var days = [];
			
       				for (var i=0; i < json.history.length; i ++){

                			var date = new Date(json.history[i].date);
					var day = date.getDate();
					
					days.push(date);
				}
				//ordenar los dias por la mas reciente
				days = days.sort(function(a,b){return a<b});
			
				//rellenar tabla historial con fechas ordenadas
				var sizeHistory = json.history.length;
				for (var i=sizeHistory -1; i > =0; i --){

					//var date = days[i];
					var date= new Date (json.history[i].date);
		
					var day = date.getDate() ;
		                	if (day < 10) day = "0" + day;

		                	var month = date.getMonth() + 1;
        		        	if (month < 10) month = "0" + (month);
        		
			        	var type = "Jornada completa";

             				if (json.history[i].type == 2)
                        			type = "Media jornada AM";
                			
                		        if (json.history[i].type == 3)
                        		        type = "Media jornada PM";
					
					if(i== (sizeHistory-1) && json.history[i].idTramite!=0 ){
						document.getElementById("rows").innerHTML += "<tr><td>"+ day + "-"+ month + "-" + date.getFullYear()+"</td><td>"+type+"</td><td><a class='btn btn-danger' href='#' onclick = 'return eliminarTramite("+json.history[i].idTramite +");'><i class='icon-white icon-trash'></i></a> </td></tr>";
					}
					else
                				document.getElementById("rows").innerHTML += "<tr><td>"+ day + "-"+ month + "-" + date.getFullYear()+"</td><td>"+type+"</td><td></td></tr>";
        			}
	  		}
		};
		//mandar peticion XMLHttp
		xhttp.open("GET", urlapi + "/users/"+document.getElementById(idCampoRut).value+"/admindayhistory", true);
		xhttp.send();
	}
	else{
		var lTable = document.getElementById("table_dias");
    		lTable.style.display = "none";
		document.getElementById("iniciarSolicitud").style.display = "none";
		document.getElementById("link_historial").style.display = "none";
		document.getElementById("msg").innerHTML = "<h4>El trabajador no tiene asignado dias administrativos.</h4>";
		document.getElementById("rows").innerHTML = '';	
	}

}


$(".historial").slideToggle(0);
//mostrar/ocultar historial
function mostrarHistorial() {
        $(".historial").slideToggle('slow', callbackHistorial);
        return false;
    }
//cambia texto del slide
function callbackHistorial() {
        var $link = $("#link_historial");
        $(this).is(":visible") ? $link.text("Ocultar Historial «") : $link.text("Mostrar Historial »");
}

function deleteRequest_t(id_request){
	var xhttp = new XMLHttpRequest();
	xhttp.open("DELETE", urlapi + "users/"+id_request+"/admindayrequest", true);
	
	xhttp.onreadystatechange = function() {
        	if (this.readyState == 4 && this.status == 204) {

		}
	};
	
	//xhttp.open("DELETE", urlapi + "users/"+id_request+"/admindayrequest", true);
        xhttp.send();

}

function deleteRequest(tramiteId){
	
        $('#modal').load('http://www.dev.nexoya.cl/backend/seguimiento/ajax_auditar_eliminar_tramite/1212');
        $('#modal').modal();
        return false;

}

  function eliminarTramite(tramiteId){
        console.log(tramiteId);
	$("#modal").load(site_url + "backend/seguimiento/ajax_auditar_eliminar_tramite/" + tramiteId);
        $("#modal").modal();
        return false;

    }

</script>
