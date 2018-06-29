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
<option selected value = '<?php echo $json->lastName."/".$json->name.'*'.$json->rut.'*'.$json->location ?>'> <?php echo explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut ?> </option>
        <?php   	} else {	?>
<option value = '<?php echo $json->lastName."/".$json->name.'*'.$json->rut.'*'.$json->location ?>'> <?php echo explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut ?> </option>

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


<tr id="tr_resumen" style="display:none;">
        <td><h4> Resumen</h4></td>
</tr>
<tr id="tr_fecha" style="text-align:right; display:none;" >
        <td>Fecha contrato &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="fecha" type="text" class="input-semi-large" name="fecha"  readonly> </td>
</tr>
<tr id="tr_acumulado" style="text-align:right; display:none;">
        <td>Dias acumulados &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="acumulado" type="text" class="input-semi-large" name="acumulado"  readonly> </td>
</tr>
<!--
<tr style="text-align:right;">
        <td>Dias máximos &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="maximos" type="text" class="input-semi-large" name="maximo"  readonly> </td>
</tr>
-->
</table>
<br></br>
<table>
<tr id="tr_periodos" style="display:none;">
        <td><h4> Periodos</h4></td>
</tr>
</table>
<div id="periodos"> </div>

<h4>
<a href="#"  id="link_historial" onclick="return mostrarHistorial();">Mostrar Periodos »</a>
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
	 <tr><td class="actions"> <a href="#" id ="solic_medico" onclick= "window.location= '/tramites/iniciar/'+<?php echo proceso_vacation?>+'/' + document.getElementById('rut_trabajador').value " class="btn btn-primary preventDoubleRequest"><i class="icon-file icon-white"></i> Iniciar Solicitud</a></td>
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
var idCampoFecha  = "fecha";
var idCampoAcumulado  ="acumulado";
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
	if(rut!=""){
		var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
				document.getElementById("tr_resumen").style.display="";
				document.getElementById("tr_fecha").style.display="";
				document.getElementById("tr_acumulado").style.display="";
				
    				var json = JSON.parse(this.responseText);
				var fecha    	= json.fecha_ingreso;
                                var acumulado   = json.acumulado;
				var acumuladoMax = json.disponibleMax;
				var periodos	= json.periods;
				
				 //rellenar campos con los valores del http request
                  	      	document.getElementById(idCampoFecha).value = fecha;
                       	 	document.getElementById(idCampoAcumulado).value = acumulado;
				
				document.getElementById("periodos").innerHTML = "";
	
				if(acumulado==0){
					document.getElementById("iniciarSolicitud").style.display = "none";
 	                                document.getElementById("msg").innerHTML = "<h4>El trabajador no tiene dias acumulados</h4>";
				}
				else{
					document.getElementById("iniciarSolicitud").style.display = "inline";
					document.getElementById("msg").innerHTML = "";
				}
				var size = periodos.length;
				
				if(size > 0){
					document.getElementById("tr_periodos").style.display="";
					
					for(var i=size -1; i >=0; i--){
						var date_init = new Date (periodos[i].initDate);

                                                var day = date_init.getDate();
                                                if (day < 10) day = "0" + day;

                                                var month = date_init.getMonth() + 1;
                                                if (month < 10) month = "0" + (month);
                                          
                                                var fecha_inicio = day + "/"+ month + "/" + date_init.getFullYear();
                                                
                                                var date_end;

						if(periodos[i].endDate==null){
                                                        date_end = new Date (periodos[i].initDate);
                                                        date_end.setFullYear(date_end.getFullYear() +1);        
                                                }else
                                                        date_end = new Date (periodos[i].endDate);
                                                day = date_end.getDate();
                                                if (day < 10) day = "0" + day;

                                                 month = date_end.getMonth() + 1;
                                                if (month < 10) month = "0" + (month);
                                                        
                                                var fecha_termino = day + "/"+ month + "/" + date_end.getFullYear();						
						var fecha = fecha_inicio+' - '+fecha_termino;
						
						var row = '<h5><a id="link_historial_'+i+'" onclick="return mostrarHistorial('+i+');" >'+ fecha+' +</a></h5>';
						
						if(periodos[i].endDate==null){
							var total    = periodos[i].basicAvailable + periodos[i].progressiveAvailable;
							 
							var tabla    = 'A la fecha <br><table><tbody><tr><td>Basicos </td><td></td> <td></td></tr><tr><td style="border-bottom: 1px solid #1a1a1a;"> Progresivos </td><td style="border-bottom: 1px solid #1a1a1a;" ><td><td></td> </tr> <tr><td>total </td><td></td> <td> '+periodos[i].avaible+' <td></tr> </tbody></table>';
							
							tabla +='<br>Al finalizar el periodo <br><table><tbody><tr><td>Basicos </td><td></td><td>'+periodos[i].basicAvailable+' </td></tr><tr><td style="border-bottom: 1px solid #1a1a1a;"> Progresivos </td><td style="border-bottom: 1px solid #1a1a1a;"><td><td>'+periodos[i].progressiveAvailable +'</td> </tr> <tr><td>Total </td><td></td> <td> '+ total+' <td></tr> </tbody></table>';
						}
						else
							var tabla    = '<table ><tbody><tr><td>Basicos </td> <td></td><td> '+periodos[i].basicAvailable +' </td></tr><tr><td style="border-bottom: 1px solid #1a1a1a;">Progresivos </td><td></td> <td style="border-bottom: 1px solid #1a1a1a;">'+ (periodos[i].progressiveAvailable)   +'<td></tr> <tr><td>Total </td><td style="border-bottom: 1px solid #1a1a1a;"></td> <td> '+periodos[i].avaible+' <td></tr> </tbody></table>';
						row    += '<div class="historial_'+i+'" style="display: none;" >'+tabla+' <table class="table"><thead id="rows_'+i+'"></thead></table></div>';
	
						document.getElementById("periodos").innerHTML +=row;
				
						var request = periodos[i].vacationRequest;
						var size_request = request.length;
						var row_id= "rows_"+i;
						if(size_request>0){				
							document.getElementById(row_id).innerHTML = '<tr><th>F. Básico</th><th>F. Progresivo</th><th>Desde</th><th>Hasta</th><th>Total</th><th>Detalle</th><th>Eliminar</th></tr>';
						}
						else{
							document.getElementById(row_id).innerHTML = '<tr><th> No hay solicitudes a la fecha</th></tr>';
						}

						//var request = periodos[i].vacationRequest;
			
						if(size_request>0){
							for(var e = size_request -1 ;e >=0;e--){

								if(request[e].active==true){
									var fecha_i= formato_fecha(new Date (request[e].initDate));
									var fecha_f= formato_fecha(new Date (request[e].endDate));
									var total  = request[e].progressive + request[e].basic;	
									var row_p = "<tr><td>"+ request[e].basic +"</td><td>"+ request[e].progressive +"</td><td>"+ fecha_i +"</td><td>"+ fecha_f +"</td> <td>"+ total +"</td> ";					 
									if(request[e].idTramite){
										
										dv     = String(rut.split("-")[1]);        
                                                        			rut_sd = String(rut.split("-")[0]);
                                                        			if(dv=='K')
											dv = 10;
										
                                                        			check_user(request[e].idTramite,request[e].id);
										row_p +="<td id ="+'view_'+request[e].idTramite+'_'+ request[e].id+"><a class='btn btn-info' href='#' onclick =' return detail("+request[e].idTramite+");' ><i class='icon-eye-open icon-white'></i></a></td>";
										row_p +="<td id = "+'b_' +request[e].idTramite +'_'+ request[e].id+"><a class='btn btn-danger' href='#' onclick = 'return eliminarTramite("+request[e].idTramite +","+request[e].id+","+ rut_sd +","+dv+");'><i class='icon-white icon-trash'></i></a> </td></tr>";
									
									}
									else
										row_p+="<td></td><td></td></tr>";
									document.getElementById(row_id).innerHTML += row_p;
								}

                                               		} 
						
						}	
					}					
				}else{
					document.getElementById("link_historial").style.display = "none";
                                        document.getElementById("rows").innerHTML = '';
				}
			}
		}
		xhttp.open("GET", urlapi + "users/"+rut+"/vacationperiod?loadRequest=true", true);
                xhttp.send();
	}
	
}

//cambia texto del slide
function formato_fecha(date ) {
	var day = date.getDate();
        if (day < 10) day = "0" + day;

        var month = date.getMonth() + 1;
        if (month < 10) month = "0" + (month);
                                                
     	var fecha = day + "-"+ month + "-" + date.getFullYear();
	return  fecha;
}


//$(".historial").slideToggle(0);
//mostrar/ocultar historial
function mostrarHistorial(id) {
	var h= ".historial_"+id;	
	$(h).slideToggle('slow', callbackHistorial);
        return false;
}
/*

function mostrarHistorial() {
        var h= ".historial";        
        $(h).slideToggle('slow', callbackHistorial);
        return false;
}

*/
//cambia texto del slide
function callbackHistorial() {
        //var l = "#link_historial_"+id;
	//var $link = $(l);
        //$(this).is(":visible") ? $link.text("Ocultar Periodos «") : $link.text("Mostrar Periodos »");
}

//Funcion para eliminar el tramite y el request
function eliminarTramite(tramiteId,requestId,rut,dv){
	if(dv==10)
        	dv='K';
	rut = rut + '-' + dv;	
	$("#modal").load(site_url + "vacation/ajax_auditar_eliminar_tramite_vacation/" + tramiteId + "/"+requestId +"/"+rut );
        $("#modal").modal();
        return false;
}



//detail
function detail(tramite) {
	var url = site_url +"fas/detail/"+tramite;
	window.location.href = url ;
}


//Chequea si el usuario participo en el tramite
function check_user(tramite, id){
	var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
		if(this.readyState == 4 ){
			json = this.responseText;
			json = JSON.parse(json);
			console.log(json.result);
			if(!json.result){
				//Boton borrar
				if(document.getElementById('b_'+tramite+'_'+id))
					document.getElementById('b_'+tramite+'_'+id).style.display = "none";
				//Boton view
				if(document.getElementById('view_'+tramite+'_'+id))
                                        document.getElementById('view_'+tramite+'_'+id).style.display = "none"; 	
			}
		}
	};
	xhttp.open("GET", site_url + "/fas/check_user/"+tramite,true);
        xhttp.send();
}

</script>
