<h2 style="line-height: 28px;">
    Generar pie de firma

<br><br>

</h2>

	<h4>Trabajador a consultar</h4>
  		<br> 
		<select  size="35" style="width:380px" data-placeholder="Seleccione por rut o nombre" class="chosen"  id="consulta_admin_days">
                
		<?php 
			foreach ($json_list_users as $json){ 
		?>
				<option value ='<?php echo $json->lastName."/".$json->name.'-'.$json->rut.'-'.(isset($json->position)?'-'.$json->position:'').(isset($json->phone)?'-'.$json->phone:'').(isset($json->annexPhone)?'-'.$json->annexPhone:'').(isset($json->areaCode)?'-'.$json->areaCode:'').(isset($json->management)?'-'.$json->management:'') ?>'><?php echo explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut ?> </option>

		<?php 	
			} 
		 ?>
        	</select>
<br>
<form  method="GET" action="<?= site_url('piefirma/descargar')?>">
<table>
<tr>
	<td><h4> Datos Trabajador </h4></td>
</tr>
<br></br>
<tr style="text-align: right;">
	<td>Nombre &nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td> <input id="nombre_trabajador" type="text" class="input-semi-large" name="nombre_trabajador"  ></td>	
</tr>
<tr>
	<td style="text-align: right;">Gerencia  &nbsp;&nbsp;&nbsp;&nbsp; </td> 
	<td>
	     	<select  size="36" style="width:382px" data-placeholder="Seleccione la gerencia" class="chosen"  id="gerencia_trabajador" name = "gerencia_trabajador"  >	
		<option value="null"></option>
		<?php	
                	foreach ($json_gerencias as $gerencia){
		?>
				<option  class="text-left"  value = '<?php echo $gerencia->name ?>'><?php echo $gerencia->name ?></option> 
		<?php           
                	}
         	?>
		</select>
	</td>
</tr>
<tr style="text-align: right;">
        <td>Cargo  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="cargo_trabajador" type="text" class="input-semi-large" name="cargo_trabajador" ></td>
</tr>
<tr style="text-align: right;">
        <td>Código de área &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="codigo_trabajador" type="text" class="input-semi-large" name="codigo_trabajador" ></td>
</tr>
<tr style="text-align: right;">
        <td>Teléfono  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="anexo_trabajador" type="text" class="input-semi-large" name="anexo_trabajador" ></td>
</tr>
<tr style="text-align: right;">
        <td>Celular  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="celular_trabajador" type="text" class="input-semi-large" name="celular_trabajador" ></td>
</tr>
	<!-- Rut trabajador-->
	<input  type="hidden" name="rut_trabajador" id ="rut_trabajador" />

</table>

<div class="form-actions">
                <button class="btn btn-primary" type="submit">Generar</button>
        </div>
<script>
//parametros
var idCampoRutUser = "consulta_admin_days";
var idCampoRut = "rut_trabajador";
var idCampoName = "nombre_trabajador";
var idCampoCargo= "cargo_trabajador";
var idCampoCelular="celular_trabajador";
var idCampoAnexo="anexo_trabajador";
var idCampoCodigo="codigo_trabajador";
var idCampoGerencia="gerencia_trabajador";

//permitir un match de mas de 1 palabra (por 2 apellidos por ej)
$("#"+idCampoRutUser).chosen({ search_contains: true});

//ocultar historial
//document.getElementById("link_historial").style.display = "none";

//ocultar boton para iniciar solicitud de dias administrativos
//document.getElementById("iniciarSolicitud").style.display = "none";

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
	document.getElementById(idCampoRut).value	= valorSelected[1];
	document.getElementById(idCampoName).value 	= valorSelected[0].split("/")[1] +  " " + valorSelected[0].split("/")[0];
	document.getElementById(idCampoCargo).value 	= valorSelected[4];
	document.getElementById(idCampoCelular).value   = valorSelected[5].toUpperCase();
	document.getElementById(idCampoAnexo).value   	= valorSelected[6].toUpperCase();	
	document.getElementById(idCampoCodigo).value	= valorSelected[7].toUpperCase();
	
	if(valorSelected[8]!=""){
		$("#"+idCampoGerencia).data("chosen").default_text = valorSelected[8];//.toUpperCase();
		$("#"+idCampoGerencia).val(valorSelected[8]); 
		$("#"+idCampoGerencia).trigger("liszt:updated"); //update chosen
		$("#"+idCampoGerencia).val(valorSelected[8]).trigger("chosen:updated"); //update option selected
	}
}

function uper(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

//mostrar/ocultar historial
//cambia texto del slide

</script>
