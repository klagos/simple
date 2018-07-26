<h2 style="line-height: 28px;">
    Buscador de Licencias </h2>
    <!--buscador--> 
<br></br> 
<form  method="GET" action="<?= site_url('licencias/buscar_new')?>">
<fieldset>
<table>
<tr>
	<td><h4> Datos Licencia </h4></td>
</tr>
<tr style="text-align: right;">
        <td>Numero &nbsp;&nbsp;</td>
        <td><input  id="licencia_numero" type="text" class="input-semi-small" name="licencia_numero"></td>
</tr>
<tr style="text-align: right;">
        <td>Estado &nbsp;&nbsp;</td>
	<td><select class="select-semi-small" name="licencia_estado" id="licencia_estado">
		<option value=0> </option>
        	<option value=1>Ingresada</option>
        	<option value=2>Pagada</option>
		<option value=3>Retornada</option>
		<option value=4>Finalizada</option>
		</select>	
	</td>
</tr>
<tr style="text-align: right;">
        <td>Tipo &nbsp;&nbsp;</td>
        <td><select class="select-semi-small" name="licencia_tipo" id="licencia_tipo">
       		<option value=""></option>
               	<option value="1">Enfermedad o accidente común</option>
		<option value="2">Medicina preventiva</option>
		<option value="3">Pre y postnatal</option>
		<option value="4">Enfermedad grave del niño</option>
		<option value="5">Accidente del trabajo</option>
		<option value="6">Enfermedad profesional</option>
		<option value="7">Patologías del embarazo</option>
		<option value="8">Permiso post natal parental</option>
        	</select> 
        </td>         
</tr>
<tr style="text-align: right;">
        <td>Fecha inicio &nbsp;&nbsp;</td>
	<td><input type="text" class="datepicker" name="fecha_inicial" value="" autocomplete="off" placeholder="dd-mm-aaaa" ></td>
</tr>
<tr style="text-align: right;">
        <td>Fecha termino &nbsp;&nbsp;</td>
        <td><input type="text" class="datepicker" name="fecha_termino" value="" autocomplete="off" placeholder="dd-mm-aaaa" ></td>
</tr>

<tr>
        <td><h4> Datos Trabajador </h4></td>
</tr>
<tr>
        <td style="text-align: right;">Nombre - Rut &nbsp;&nbsp;</td>
	<td style="size: 28;"><select  data-placeholder="Seleccione por nombre" class="chosen"  id="nombre_rut">
		<option value="null"> </option>
		<?php
                foreach ($json_list_users  as $json){
		?>
			<option value = '<?php echo $json->lastName."/".$json->name.'*'.$json->rut  ?>'> <?php echo explode(" ",$json->name)[0].' '.$json->lastName.' - '.$json->rut ?> </option>
		<?php					
		}
		?>
		</select>
	</td>
</tr>
<tr id="tr_nombre" style="text-align: right; display:none;">
        <td>Nombre &nbsp;&nbsp;</td>
        <td> <input  id="nombre" type="text" class="input-semi-small" name="nombre" readonly></td>
</tr>
<tr id="tr_rut" style="text-align: right; display:none;">
        <td>Rut &nbsp;&nbsp;</td>
        <td> <input  id="trabajador_rut" type="text" class="input-semi-small" name="trabajador_rut" readonly></td>
</tr>
</table>
<br>

<div class="form-actions">
                <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
</fieldset>

<script>
var idCampoRutUser = "nombre_rut";
var idCampoRut 	= "trabajador_rut";
var idCampoName = "nombre";

//permitir un match de mas de 1 palabra (por 2 apellidos por ej)
$("#"+idCampoRutUser).chosen({ search_contains: true});

document.getElementById(idCampoRutUser).onchange = function(){
	
	document.getElementById("tr_nombre").style.display="";
	document.getElementById("tr_rut").style.display="";
	
	var valorSelected =  document.getElementById(idCampoRutUser).value.split("*");
        document.getElementById(idCampoRut).value =  valorSelected[1];
        document.getElementById(idCampoName).value =  valorSelected[0].split("/")[1] +  " " + valorSelected[0].split("/")[0];
}

</script>
 
