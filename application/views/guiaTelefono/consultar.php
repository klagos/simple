<h2 style="line-height: 28px;">
     Información colaboradores 
<br>

</h2>

	<h4>Colaborador a consultar</h4>
  		<br> 
		<select  size="35" style="width:380px" data-placeholder="Seleccione por rut o nombre" class="chosen"  id="consulta_admin_days">
                
		<?php
			 $tildes = array("á","é","í","ó","ú");			
			$sintildes =array("a","e","i","o","u");
			 
			foreach ($json_list_users as $json){ 
		?>
				<option value ='<?php echo $json->lastName."/".$json->name.'*'.$json->rut.'*'.(isset($json->position)?'*'.$json->position:'').(isset($json->phone)?'*'.$json->phone:'').(isset($json->annexPhone)?'*'.$json->annexPhone:'').(isset($json->areaCode)?'*'.$json->areaCode:'').(isset($json->management)?'*'.$json->management:'').(isset($json->email)?'*'.$json->email:'') ?>'><?php echo str_replace($tildes,$sintildes, explode(" ",$json->name)[0] ).' '. str_replace($tildes,$sintildes,$json->lastName) .' - '.$json->rut ?> </option>

		<?php 	
			} 
		 ?>
        	</select>
<br>
<table>
<tr>
	<td><h4> Datos  </h4></td>
</tr>
<br></br>
<tr style="text-align: right;">
	<td>Nombres &nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td> <input id="nombre_trabajador" type="text" class="input-semi-large" name="nombre_trabajador" maxlength="18" readonly></td>	
</tr>
<tr style="text-align: right;">
        <td>Apellidos &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <input id="apellido_trabajador" type="text" class="input-semi-large" name="apellido_trabajador"  readonly></td>
</tr>
<tr>
	<td style="text-align: right;">Gerencia  &nbsp;&nbsp;&nbsp;&nbsp; </td> 
	<td> <input type="text" class="input-semi-large" id="gerencia_trabajador" name = "gerencia_trabajador" readonly></td>
</tr>
<tr style="text-align: right;">
        <td>Cargo  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="cargo_trabajador" type="text" class="input-semi-large" name="cargo_trabajador" readonly></td>
</tr>
<tr style="text-align: right;">
        <td>Código de área &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="codigo_trabajador" type="number" class="input-semi-large" name="codigo_trabajador" placeholder="El trabajador no tiene un código asociado" readonly></td>

</tr>
<tr style="text-align: right;">
        <td>Teléfono  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="anexo_trabajador" type="number" class="input-semi-large" name="anexo_trabajador" placeholder="El trabajador no tiene un de anexo asociado" readonly></td>
</tr>
<tr style="text-align: right;">
        <td>Celular  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="celular_trabajador" type="number" class="input-semi-large" name="celular_trabajador" maxlength="9" placeholder="El trabajador no tiene un celular asociado" readonly></td>
</tr>
<tr style="text-align: right;">
        <td>Email  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="email_trabajador" type="text" class="input-semi-large" name="email_trabajador" maxlength="9" placeholder="El trabajador no tiene un correo asociado" readonly></td>
</tr>

</table>

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
var idCampoApellido="apellido_trabajador";
var idCampoEmail="email_trabajador";

//permitir un match de mas de 1 palabra (por 2 apellidos por ej)
$("#"+idCampoRutUser).chosen({ search_contains: true});


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

	console.log(valorSelected);
	document.getElementById(idCampoName).value 	= valorSelected[0].split("/")[1];  
	document.getElementById(idCampoApellido).value 	= valorSelected[0].split("/")[0];
	document.getElementById(idCampoCargo).value 	= valorSelected[3];
	document.getElementById(idCampoCelular).value   = (valorSelected[4]!=0)?valorSelected[4]:'';
	document.getElementById(idCampoAnexo).value   	= (valorSelected[5]!=0)?valorSelected[5]:'';
	document.getElementById(idCampoCodigo).value	= (valorSelected[6]!=0)?valorSelected[6]:'';
	document.getElementById(idCampoGerencia).value    = valorSelected[7];
	document.getElementById(idCampoEmail).value    = (valorSelected[8])?valorSelected[8]:'';

}

function uper(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

//mostrar/ocultar historial
//cambia texto del slide

</script>
