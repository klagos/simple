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
<table style="width: 100%;">
<tr id="tr_periodos" style="display:none;">
        <td style="width: 26%;"><h4>Periodos</h4></td><td align="center" style="width:14%;"><h4>Básicos</h4></td><td align="center" style="width:15%;"><h4>Progresivos</h4></td><td align="center" style="width:10%;"><h4>Total</h4></td> <td align="center" style="width:15%;"><h4>Detalle</h4> <td align="center" style="width:10%;"><h4>Descargar</h4></td><td align="center" style="width:10%;"><h4>Eliminar</h4></td>
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
	document.getElementById(idCampoRut).value =  valorSelected[1];
	document.getElementById(idCampoName).value	=  valorSelected[0].split("/")[1] +  " " + valorSelected[0].split("/")[0];
	document.getElementById(idCampoLocation).value  =  valorSelected[2].toUpperCase();
	
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
	
				if(acumulado==0 && acumuladoMax ==0){
					document.getElementById("iniciarSolicitud").style.display = "none";
 	                                document.getElementById("msg").innerHTML = "<h4>El trabajador no tiene dias acumulados</h4>";
				}
				else{
					document.getElementById("iniciarSolicitud").style.display = "inline";
					document.getElementById("msg").innerHTML = "";
				}
				var size = periodos.length;

				var parts = fecha.split('/');
				var fecha_contrato = new Date(parts[2], parts[1] -1, parts[0]); 
				
				if(size > 0){
					document.getElementById("tr_periodos").style.display="";
					
					for(var i=size -1; i >=0; i--){
						var date_init = new Date (periodos[i].initDate);
							
						if((date_init.getTime() == fecha_contrato.getTime()) 
							|| (date_init.getTime() > fecha_contrato.getTime())){
                                                
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
						var basicos = periodos[i].basicAvailable;
						var progresivos = periodos[i].progressiveAvailable;
						var total    = basicos + progresivos;	
						
						if(periodos[i].endDate==null){
							basicos 	= json.acumuladoPeriodActualBasic;
							progresivos 	= json.acumuladoPeriodActualProgressive;
						}
						var total    = basicos + progresivos; 
						
						//PERIODOS 
						var row = '<table style="width: 100%;"><tr><td style="width: 26%;"><h5> '+fecha+' </h5></td><td align="center" style="width:14%;">'+basicos+'</td><td align="center" style="width:15%;">'+progresivos+'</td><td align="center" style="width:10%;">'+ total+'</td> <td align="center" style="width:15%;"><h5> <a  href="#" id="link_historial_'+i+'" onclick="return mostrarHistorial('+i+');" > Ver detalle +</a></h5></td> <td  align="center" style="width:10%;"></td> <td  align="center" style="width:10%;"></td> </tr></table>';						
						//Ultimo periodo
						if(periodos[i].endDate==null){
							var totalFinal    = periodos[i].basicAvailable + periodos[i].progressiveAvailable;
							 
							var tabla = '<table style="width: 100%;"><tr><td style="width: 26%;"><h5> Al finalizar el periodo </h5></td><td align="center" style="width:14%;">'+periodos[i].basicAvailable+'</td><td align="center" style="width:15%;">'+periodos[i].progressiveAvailable+'</td><td align="center" style="width:10%;">'+ totalFinal+'</td><td style="width:15%;"></td><td style="width:10%;"></td><td style="width:10%;"></td> </tr></table>';
						}else
							var tabla = '';
							
				
						var request 	 = periodos[i].vacationRequest;
						var size_request = request.length;
						
						//REQUEST DEL PERIODO	
						var tabla_request = '';	
						if(size_request>0){ 
							var row_p = '';
							for(var e = size_request -1 ;e >=0;e--){
                                                                if(request[e].active==true){
                                                                        var fecha_i= formato_fecha(new Date (request[e].initDate));
                                                                        var fecha_f= formato_fecha(new Date (request[e].endDate));
                                                                        var total  = request[e].progressive + request[e].basic; 
                                                                        
									row_p+="<tr style='height:23px;'><td align='center' style='width:26%;'>"+ fecha_i+" - "+ fecha_f +"</td><td align='center' style='width:14%;'>"+ request[e].basic +"</td><td align='center' style='width:15%;'>"+ request[e].progressive +"</td><td align='center' style='width:10%;'>"+ total +"</td>";
                                                                        if(request[e].idTramite){
                                                                                
                                                                                dv     = String(rut.split("-")[1]);        
                                                                                rut_sd = String(rut.split("-")[0]);
                                                                                if(dv=='K')
                                                                                        dv = 10;
										//DETALLE
                                                                                row_p +="<td align='center' style='width:15%;' id ="+'view_'+request[e].idTramite+'_'+ request[e].id+"><a class='btn btn-info' href='#' onclick =' return detail("+request[e].idTramite+");' ><i class='icon-eye-open icon-white'></i></a></td>";
										
										//IMPRIMIR
										row_p +="<td align='center' style='width:10%;' id ="+'print_'+request[e].idTramite+'_'+ request[e].id+"><a class='btn btn-success' href='#' onclick =' return print_tramite("+request[e].idTramite+");' ><i class='icon-download-alt icon-white'></i></a></td>";                                        
                                                                        	//Se revisa si el usuario puede eliminar el tramite       	 
										check_user(request[e].idTramite,request[e].id);
										row_p +="<td align='center' style='width:10%;' id = "+'b_' +request[e].idTramite +'_'+ request[e].id+"><a class='btn btn-danger' href='#' onclick = 'return eliminarTramite("+request[e].idTramite +","+request[e].id+","+ rut_sd +","+dv+");'><i class='icon-white icon-trash'></i></a> </td></tr>";
                                                                        }
                                                                        else
                                                                                row_p+="<td align='center' style='width:15%;'></td><td align='center' style='width:10%;'></td> <td align='center' style='width:10%;'></td></tr>";
                                                                        
                                                                } 

                                                        }
							if(row_p!='')	
								tabla_request = '<table style="width: 100%;">'+row_p+'</table>';
							else			
								tabla_request = '<table style="width: 100%;"><tr><h5>No hay solicitudes a la fecha</h5></th></tr></table>';	
						}
						else{
							tabla_request = '<table style="width: 100%;"><tr><h5>No hay solicitudes a la fecha</h5></th></tr></table>';
						}
						//AGREGAMO A ROW TABLA PERIODOS Y REQUEST
						row    += '<div class="historial_'+i+'" style="display: none;" >'+tabla+tabla_request+'</div>';
                                                        
                                                document.getElementById("periodos").innerHTML +=row;
						}//End if verify dates	
					}//Endfor
									
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

$(".historial").slideToggle(0);
//mostrar/ocultar historial
function mostrarHistorial() {
        $(".historial").slideToggle('slow', callbackHistorial);
        return false;
}

//mostrar/ocultar historial
function mostrarHistorial(id) {
	var h= ".historial_"+id;	
	$(h).slideToggle('slow',function(){
		var l = "#link_historial_"+id;
        	var $link = $(l);
        	$(this).is(":visible") ? $link.text("Ocultar detalle -") : $link.text("Ver detalle +");		
	});
        return false;
}
//cambia texto del slide
function callbackHistorial() {}

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
	var url = site_url +"vacation/detail/"+tramite;
	window.location.href = url ;
}

function print_tramite(tramite){
	var url = site_url +"vacation/print_tramite/"+tramite;
        window.location.href = url ;
}

//Chequea si el usuario participo en el tramite
function check_user(tramite, id){
	var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
		if(this.readyState == 4 ){
			json = this.responseText;
			json = JSON.parse(json);
			if(!json.result){
				//Boton borrar
				if(document.getElementById('b_'+tramite+'_'+id))
					document.getElementById('b_'+tramite+'_'+id).innerHTML = "<td align='center' style='width:15%;'></td>";
			}
		}
	};
	xhttp.open("GET", site_url + "/vacation/check_user/"+tramite,true);
        xhttp.send();
}

</script>
