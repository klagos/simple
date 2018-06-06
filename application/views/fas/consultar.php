<?php
require_once(FCPATH."procesos.php");
?>

<h2 style="line-height: 28px;">
	<?php $title ?>
<br><br>

</h2>

	<h4>Trabajador a consultar</h4>  <br> <select  size="35" style="width:380px" data-placeholder="Seleccione por rut o nombre" class="chosen"  id="consulta_admin_days">
                
		<option value="null"> </option>
	<?php
                foreach ($json_list_users  as $json){
			//seleccionar trabajador cuando se vuelve a consulta luego de pedir un dia admin
			if ($json->rut == explode("=",$_SERVER['REQUEST_URI'])[1]){ 
        ?>                
<option selected value = '<?php echo $json->lastName."/".$json->name.'*'.$json->rut.'*'.$json->location.'*'.(($json->hasBCI)?'si' :'no') ?>'> <?php echo explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut ?> </option>
        <?php   	} else {	?>
<option value = '<?php echo $json->lastName."/".$json->name.'*'.$json->rut.'*'.$json->location.'*'.(($json->hasBCI)?'si' :'no')  ?>'> <?php echo explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut ?> </option>

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


<tr id="tr_beneficios" style="display:none;">
        <td><h4> Beneficios  </h4></td>
</tr>
<tr style="text-align: right; display:none;" id="tr_medico" >
        <td>Médicos &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>
                <select class="select-semi-large" name="medico" id="medico" required>
                <option value=""> </option>
                <option value=10>Médico</option>
                <option value=20>Prótesis y Ortesis</option>
                <option value=30>Medicamentos</option>
                <option value=31>Zonas Extremas</option>
                <option value=40>Complementario</option>
                <option value=50>Honorarios Médico quirúrgico</option>
                <option value=60>Parto Normal</option>
                <option value=70>Complemento médico</option>
                <option value=80>Dental</option>
                <option value=90>Ortodoncia</option>
                <option value=100>Maxilofacial</option>
         </td>
</tr>
<tr style="text-align:right; display:none;" id="tr_sociales">
        <td>Sociales &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>
                <select class="select-semi-large" name="social" id="social" required>
                <option value=""> </option>
                <option value=10>Ayuda Escolar - Pre Básica</option>
                <option value=11>Ayuda Escolar - Básica</option>
                <option value=12>Ayuda Escolar - Media</option>
                <option value=13>Ayuda Escolar - Superior</option>
                <option value=20>Ayuda Social</option>
                <option value=30>Ayuda Natalidad</option>
                <option value=40>Ayuda Traslado</option>
                <option value=50>Fondo para Enfermedades</option>
                <option value=60>Bono por fallecimiento</option>
         </td>
</tr>

<tr id="tr_solicitudes" style="display:none;">
        <td><h4> Resumen</h4></td>
</tr>
<tr style="text-align:right; display:none;" id="tr_asignado">
        <td>Asignado &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="asignado" type="text" class="input-semi-large" name="asignado"  readonly> </td>
</tr>
<tr style="text-align:right; display:none;" id="tr_pendiente">
        <td>Pendiente &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="pendiente" type="text" class="input-semi-large" name="pendiente"  readonly> </td>
</tr>
<tr style="text-align:right; display:none;" id="tr_reembolso">
        <td>Reembolsado &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="rembolso" type="text" class="input-semi-large" name="rembolso"  readonly> </td>
</tr>
<tr style="text-align: right; display:none;" id="tr_saldo">
        <td>Saldo &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="saldo" type="text" class="input-semi-large" name="saldo"  readonly> </td>
</tr>

<tr style="text-align: right; display:none;" id="tr_acumulado">
        <td>Acumulado sin pagar &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="acumulado" type="text" class="input-semi-large" name="acumulado"  readonly> </td>
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
	 <tr><td class="actions"> <a href="#" id ="solic_medico" onclick= "window.location= '/tramites/iniciar/'+<?php echo proceso_fas_medicos?>+'/' + document.getElementById('rut_trabajador').value " class="btn btn-primary preventDoubleRequest"><i class="icon-file icon-white"></i> Iniciar Solicitud</a></td>
	<td class="actions"> <a href="#" id  ="solic_social" onclick= "window.location= '/tramites/iniciar/'+<?php echo proceso_fas_social?>+'/' + document.getElementById('rut_trabajador').value " class="btn btn-primary preventDoubleRequest"><i class="icon-file icon-white"></i> Iniciar Solicitud</a></td>
	</tr>
    </tbody>
</table>

<div id="msg"> </div>
<div id="modal" class="modal hide fade" > </div>

<script>

//parametros
var idCampoRutUser = "consulta_admin_days";
var idCampoRut = "rut_trabajador";
var idCampoName = "nombre_trabajador";
var idCampoLocation = "localidad_trabajador";
var idCampoMedico  = "medico";
var idCampoSocial  ="social";
var urlapi = '<?php echo urlapi?>';



//permitir un match de mas de 1 palabra (por 2 apellidos por ej)
$("#"+idCampoRutUser).chosen({ search_contains: true});

//ocultar historial
document.getElementById("link_historial").style.display = "none";

//ocultar boton para iniciar solicitud
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
	var valorSelected =  document.getElementById(idCampoRutUser).value.split("*");
	document.getElementById(idCampoRut).value =  valorSelected[1];  //concat("-".concat(valorSelected[2]));
	document.getElementById(idCampoName).value =  valorSelected[0].split("/")[1] +  " " + valorSelected[0].split("/")[0];
	document.getElementById(idCampoLocation).value =  valorSelected[2].toUpperCase();
	
	var rut = document.getElementById(idCampoRut).value;
	
	//Mostrar campos de beneficios	
	document.getElementById("tr_beneficios").style.display=""; 
	if(valorSelected[3]=="si"){
		document.getElementById("tr_medico").style.display="none";
	}
	else{
		document.getElementById("tr_medico").style.display="";	
	}
	document.getElementById("tr_sociales").style.display="";	
	//OCULTAR
	document.getElementById("tr_solicitudes").style.display="none";
        document.getElementById("tr_asignado").style.display="none";                                
        document.getElementById("tr_reembolso").style.display="none";
	document.getElementById("tr_pendiente").style.display="none";
        document.getElementById("tr_saldo").style.display="none";
	document.getElementById("tr_acumulado").style.display="none";
	document.getElementById("link_historial").style.display = "none";
	//document.getElementById("iniciarSolicitud").style.display = "inline";
	document.getElementById(idCampoMedico).selectedIndex = 0;
	document.getElementById(idCampoSocial).selectedIndex = 0;
}

document.getElementById(idCampoSocial).onchange = function(){
	var code = document.getElementById(idCampoSocial).value;
        var rut  = document.getElementById(idCampoRut).value;
	
	if(code!="" && code>0){
		//obtener historial del usuario seleccionado    
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				
				document.getElementById("tr_solicitudes").style.display="";
                                document.getElementById("tr_asignado").style.display="";
                                document.getElementById("tr_pendiente").style.display="";                               
                                document.getElementById("tr_reembolso").style.display="";
				document.getElementById("tr_saldo").style.display="none";	
				document.getElementById(idCampoMedico).selectedIndex = 0;
				
				//document.getElementById("solic_social").style.display ="inline";
				if(document.getElementById("solic_social").style.display=='none')
                                                        document.getElementById("solic_social").style.display = "inline";
				
				document.getElementById("iniciarSolicitud").style.display = "inline";
				document.getElementById("solic_medico").style.display ="none";
				

				var json = JSON.parse(this.responseText);
                                var asignado    = json.asignado;
                                var total       = json.pagado;
                                var pendiente   = json.pendiente;
                                var historial   = json.socialHistory;

				document.getElementById("asignado").value       =asignado;
                                document.getElementById("pendiente").value      =pendiente;
                                document.getElementById("rembolso").value       =total;
				
				if(code >=10 && code <= 13){
                                        document.getElementById("tr_acumulado").style.display="";
					document.getElementById("acumulado").value = json.acumuladoSinPagar;
				}
                                else
                                        document.getElementById("tr_acumulado").style.display="none";                               	


				var size = historial.length;
				if(size>0){
                                        document.getElementById("link_historial").style.display = "inline";
                                	document.getElementById("rows").innerHTML = '<tr><th>Reembolso</th><th>Fecha</th><th>Estado</th><th>Editar</th><th>Detalle</th><th>Eliminar</th></tr>';

					for(var i=size -1; i >= 0; i--){
                                                var date= new Date (json.socialHistory[i].date);

                                                var day = date.getDate();
                                                if (day < 10) day = "0" + day;

                                                var month = date.getMonth() + 1;
                                                if (month < 10) month = "0" + (month);
                                                
                                                var fecha = day + "-"+ month + "-" + date.getFullYear();
                                                var value = json.socialHistory[i].value;
                                                var row = "<tr><td>"+ value +"</td><td>"+fecha+"</td>";
                                                row += (json.socialHistory[i].paid ==true)?"<td>Pagado</td>":(json.socialHistory[i].frozenPaid==true)?"<td>Congelado</td>": "<td>Pendiente</td>"; 
                                                

						if(json.socialHistory[i].frozenPaid==true){
							//check_user(json.socialHistory[i].idTramite);
                                                        //row+="<td id ="+'edit_'+json.socialHistory[i].idTramite +"><a class='btn btn-primary' href='#' onclick =' return edit("+json.socialHistory[i].idTramite+");' ><i class='icon-pencil icon-white'></i></a></td>";
							row+="<td></td>";
                                                }
						else
                                                        row+="<td></td>";
	
                                                if(json.socialHistory[i].idTramite!=null){
                                                        check_user(json.socialHistory[i].idTramite);
                                                        row+="<td id ="+json.socialHistory[i].idTramite +"><a class='btn btn-info' href='#' onclick =' return detail("+json.socialHistory[i].idTramite+");' ><i class='icon-eye-open icon-white'></i></a></td>";
                                                }
                                                else
                                                        row+="<td></td>";
                                                

                                                if(json.socialHistory[i].idTramite!=null && json.socialHistory[i].paid ==false){
                                                        dv  = String(rut.split("-")[1]);        
                                                        rut = String(rut.split("-")[0]);
                                                        
                                                        check_user(json.socialHistory[i].idTramite);
                                                        row +="<td id = "+'b_' +json.socialHistory[i].idTramite +"><a class='btn btn-danger' href='#' onclick = 'return eliminarTramiteSocial("+json.socialHistory[i].idTramite +","+json.socialHistory[i].id+","+ rut +","+dv+");'><i class='icon-white icon-trash'></i></a> </td></tr>";
                                                }
                                                else
                                                        row +="<td></td></tr>";
                                        
                                                
                                                document.getElementById("rows").innerHTML += row;
                                        }

				}else{
                                        document.getElementById("link_historial").style.display = "none";
                                        document.getElementById("rows").innerHTML = '';
                                }
				 
			}
		}
		xhttp.open("GET", urlapi + "users/"+rut+"/socialbenefit/"+ code +"/request ", true);
                xhttp.send();
	}

}


document.getElementById(idCampoMedico).onchange = function(){
	var code = document.getElementById(idCampoMedico).value;
	var rut  = document.getElementById(idCampoRut).value;  
	if(code!="" && code>0){
		 //obtener historial del usuario seleccionado    
        	var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                	if (this.readyState == 4 && this.status == 200) {
                        	
			 	
				document.getElementById("tr_solicitudes").style.display="";
				document.getElementById("tr_asignado").style.display="";
				document.getElementById("tr_pendiente").style.display=""; 				
				document.getElementById("tr_reembolso").style.display="";
				document.getElementById("tr_saldo").style.display="";		
        			document.getElementById(idCampoSocial).selectedIndex = 0;
			
				
				var json = JSON.parse(this.responseText);
				var asignado 	= json.asignado;
				var total       = json.pagado;
				var pendiente	= json.pendiente;
				var historial 	= json.history;
				var saldo 	= json.saldo;
				
				document.getElementById("asignado").value       =asignado;
                                document.getElementById("pendiente").value      =pendiente;
				document.getElementById("rembolso").value       =total;
                                document.getElementById("saldo").value          =saldo;
				
				//SHOW INICIAR SOLICITUD
				if(saldo >0){
					document.getElementById("iniciarSolicitud").style.display = "inline";
					if(document.getElementById("solic_medico").style.display=='none')
							document.getElementById("solic_medico").style.display = "inline";
					document.getElementById("solic_social").style.display ="none";
					document.getElementById("msg").innerHTML = "";	
				}
				else{
					document.getElementById("iniciarSolicitud").style.display = "none";
     		                        document.getElementById("msg").innerHTML = "<h4>El trabajador no tiene saldo para este ítem.</h4>";
				}				

				//SHOW HISTORIAL
				var size = historial.length;
				if(size>0){
					document.getElementById("link_historial").style.display = "inline";
					document.getElementById("rows").innerHTML = '<tr><th>Reembolso</th><th>Fecha</th><th>Estado</th> <th >Ver detalle</th> <th>Eliminar</th></tr>';
					
					for(var i=size -1; i >= 0; i--){
						var date= new Date (json.history[i].date);

						var day = date.getDate();
                                        	if (day < 10) day = "0" + day;

                                        	var month = date.getMonth() + 1;
                                        	if (month < 10) month = "0" + (month);
						
						var fecha = day + "-"+ month + "-" + date.getFullYear();
						var value = json.history[i].value;
						var row = "<tr><td>"+ value +"</td><td>"+fecha+"</td>";
						row += (json.history[i].paid ==true)?"<td>Pagado</td>":"<td>Pendiente</td>"; 
						
						if(json.history[i].idTramite!=null){
							check_user(json.history[i].idTramite);
							row+="<td id ="+json.history[i].idTramite +"><a class='btn btn-info' href='#' onclick =' return detail("+json.history[i].idTramite+");' ><i class='icon-eye-open icon-white'></i></a></td>";
						}
						else
							row+="<td></td>";
						

						if(json.history[i].idTramite!=null && json.history[i].paid ==false){
							dv  = String(rut.split("-")[1]);        
                                                	rut = String(rut.split("-")[0]);
							
							check_user(json.history[i].idTramite);
							row +="<td id = "+'b_' +json.history[i].idTramite +"><a class='btn btn-danger' href='#' onclick = 'return eliminarTramite("+json.history[i].idTramite +","+json.history[i].id+","+ rut +","+dv+");'><i class='icon-white icon-trash'></i></a> </td></tr>";
						}
						else
							row +="<td></td></tr>";
					
						
						document.getElementById("rows").innerHTML += row;
					}
				}else{
					document.getElementById("link_historial").style.display = "none";
                                 	document.getElementById("rows").innerHTML = '';
				}
				
				
			}
		}
		xhttp.open("GET", urlapi + "users/"+rut+"/medicalbenefit/"+ code +"/request ", true);
                xhttp.send();
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

//Funcion para eliminar el tramite y el request
function eliminarTramite(tramiteId,requestId,rut,dv){
	rut = rut + '-' + dv;
	var request = 'medico';	
	$("#modal").load(site_url + "fas/ajax_auditar_eliminar_tramite_fas/" + tramiteId + "/"+requestId +"/"+rut +"/"+request );
        $("#modal").modal();
        return false;
}

//Funcion para eliminar el tramite y el request
function eliminarTramiteSocial(tramiteId,requestId,rut,dv){
        rut = rut + '-' + dv;
	var request = 'social';   
        $("#modal").load(site_url + "fas/ajax_auditar_eliminar_tramite_fas/" + tramiteId + "/"+requestId +"/"+rut +"/"+request );
        $("#modal").modal();
        return false;
}


//detail
function detail(tramite) {
	var url = site_url +"fas/detail/"+tramite;
	window.location.href = url ;
}

//modificar
function edit(tramite) {
        var url = site_url +"fas/edit/"+tramite;
        window.location.href = url ;
}

//Chequea si el usuario participo en el tramite
function check_user(tramite){
	var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
		if(this.readyState == 4 ){
			json = this.responseText; 
			console.log(json);
			json = JSON.parse(json);
			if(!json.result){
				document.getElementById(tramite).style.display = "none";
				//Boton borrar
				if(document.getElementById('b_'+tramite))
					document.getElementById('b_'+tramite).style.display = "none";
				//Boton editar
				if(document.getElementById('edit_'+tramite))
                                        document.getElementById('b_'+tramite).style.display = "none"; 	
			}
		}
	};
	xhttp.open("GET", site_url + "/fas/check_user/"+tramite,true);
        xhttp.send();
}

</script>
