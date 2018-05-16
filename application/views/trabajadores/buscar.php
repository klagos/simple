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
				<option value ='<?php echo $json->lastName."/".$json->name.'*'.$json->rut.'*'.(isset($json->phone)?'*'.$json->phone:'').(isset($json->annexPhone)?'*'.$json->annexPhone:'').(isset($json->areaCode)?'*'.$json->areaCode:'').(isset($json->management)?'*'.$json->management:'') ?>'><?php echo str_replace($tildes,$sintildes, explode(" ",$json->name)[0] ).' '. str_replace($tildes,$sintildes,$json->lastName) .' - '.$json->rut ?> </option>

		<?php 	
			} 
		 ?>
        	</select>
<br>
<form  method="GET" action="<?= site_url('trabajadores/buscar_user')?>">
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

<tr style="text-align: right;">
        <td>Rut &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <input id="rut_trabajador" type="text" class="input-semi-large" name="rut_trabajador"  readonly></td>
</tr>

</table>
<div class="form-actions">
                <button class="btn btn-primary" type="submit">Editar</button>
        </div>

<script>
//parametros
var idCampoRutUser = "consulta_admin_days";
var idCampoRut = "rut_trabajador";
var idCampoName = "nombre_trabajador";
var idCampoApellido = "apellido_trabajador";


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
	document.getElementById(idCampoRut).value     	= valorSelected[1];	

}

function uper(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

//mostrar/ocultar historial
//cambia texto del slide

</script>
