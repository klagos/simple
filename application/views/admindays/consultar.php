<h2 style="line-height: 28px;">
    Consulta días administrativos

<br><br>

<?php
		//Obtener data de usuarios
		$url = "http://nexoya.cl:8080/apiSimple/users/list/small/admindays?history=true";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "Content-Type: application/json"
                    ));
                $result=curl_exec($ch);
                curl_close($ch);

                $json_ws = json_decode($result);
?>
</h2>

	<h4>Trabajador a consultar</h4>  <br> <select size="35" style="width:380px" data-placeholder="Seleccione por rut o nombre"  class="chosen" id="consulta_admin_days">
                
		<option value="null"> </option>
	<?php
                foreach ($json_ws as $json){
        ?>                
<option value = '<?php echo $json->lastName."/".$json->name.'-'.$json->rut.'-'.$json->location .(isset($json->day)?'-'.$json->day:'').(isset($json->halfDay)?'-'.$json->halfDay:'').(isset($json->takenDays)?'-'.$json->takenDays:'').(isset($json->pendingDays)?'-'.$json->pendingDays:'').(isset($json->pendingHalfDays)?'-'.$json->pendingHalfDays:''). (isset($json->costCenter)?'-'. $json->costCenter :''). (isset($json->service)? '-'.$json->service : '').(isset($json->email)?'-'.$json->email:'').(isset($json->adminDayRequest)?'-'.json_encode($json->adminDayRequest):'') ?>'> <?php echo explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut ?> </option>
        <?php                }   ?>
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
                    <a href="<?=site_url('tramites/iniciar/'.$p->id)?>" class="btn btn-primary preventDoubleRequest"><i class="icon-file icon-white"></i> Iniciar Solicitud</a>
                    <?php else: ?>
                        <?php if($p->getTareaInicial()->acceso_modo=='claveunica'):?>
                        <a href="<?=site_url('autenticacion/login_openid')?>?redirect=<?=site_url('tramites/iniciar/'.$p->id)?>"><img style="max-width: none;" src="<?=base_url('assets/img/claveunica-medium.png')?>" alt="ClaveUnica" /></a>
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

<div id ="msg">

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

//permitir un match de mas de 1 palabra (por 2 apellidos por ej)
$("#"+idCampoRutUser).chosen({ search_contains: true});

//ocultar historial
document.getElementById("link_historial").style.display = "none";

//ocultar boton para iniciar solicitud de dias administrativos
document.getElementById("iniciarSolicitud").style.display = "none";

//si se elige un valor, llama a la funcion
document.getElementById(idCampoRutUser).onchange = function(){

	//se rellenan los campos con el valor elegido
	var valorSelected =  document.getElementById(idCampoRutUser).value.split("-");
	document.getElementById(idCampoRut).value =  valorSelected[1].concat("-".concat(valorSelected[2]));
	document.getElementById(idCampoName).value =  valorSelected[0].split("/")[1] +  " " + valorSelected[0].split("/")[0];
	document.getElementById(idCampoLocation).value =  valorSelected[3].toUpperCase();
	document.getElementById(idCampoDiasAsig).value =  valorSelected[4];
	document.getElementById(idCampoMedJor).value =  valorSelected[5];
	document.getElementById(idCampoDiasTom).value =  valorSelected[6];
	document.getElementById(idCampoDiasDis).value =  valorSelected[7];
	document.getElementById(idCampoMedJorDis).value =  valorSelected[8];
	
	//si no quedan dias disponibles, no se puede iniciar solicitud
	if (valorSelected[7] == 0){ 
		document.getElementById("iniciarSolicitud").style.display = "none";
		 document.getElementById("msg").innerHTML = "<h4>Al trabajador no le quedan dias disponibles</h4>";
	}else
		document.getElementById("iniciarSolicitud").style.display = "inline";
	//obtener historial
	var json_text =  valorSelected[9];
	var json = JSON.parse(json_text);

	//mostrar historial si existe más de un valor, sino se oculta
	if (json.length > 0){
		 document.getElementById("link_historial").style.display = "inline";
		 document.getElementById("rows").innerHTML = '<tr><th>Fecha</th><th>Tipo solicitud</th></tr>';
	}else{
		 document.getElementById("link_historial").style.display = "none";
		 document.getElementById("rows").innerHTML = '';
	}
	for (var i=0; i < json.length; i ++){
		//se rellena la tabla del historial
		var date = new Date(json[i].date);

		var day = date.getDate();
		if (day < 10) day = "0" + day;

		var month = date.getMonth() + 1;
		if (month < 10) month = "0" + (month);
		var type = "Jornada completa";
		
		if (json[i].type == 2)
			 type = "Media jornada AM";
		else 
			if (json[i].type == 3)
				 type = "Media jornada PM";
		document.getElementById("rows").innerHTML += "<tr><td>"+ day + "-"+ month + "-" + date.getFullYear()+"</td><td>"+type+"</td></tr>";
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

</script>
