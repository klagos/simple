<h2 style="line-height: 28px;">
    Colaborador.
<br></br>
</h2>
<?php if ($json_usuario!=""): ?>
<body onload="cargarDatos()">
<form  method="GET" action="<?= site_url('trabajadores/update')?>">
<table>
<tr style="text-align: right;">
        <td><h4>Datos personales</h4></td>
</tr>
<tr style="text-align: right;">
	<td>Rut &nbsp;&nbsp;&nbsp;&nbsp;</td>
       	<td><input name="rut" id ="rut" class="input-semi-large" type="text" value="<?php echo $json_usuario->rut ?>" readonly></td>
</tr>

<tr style="text-align: right;">
        <td>Nombre &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <input maxlength="22" id="nombre" type="text" class="input-semi-large" name="nombre" value="<?php echo $json_usuario->name  ?>" placeholder="número máximo 18 caracteres" ></td>
</tr>

<tr style="text-align: right;">
        <td>Apellido &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <input id="apellido" type="text" class="input-semi-large" name="apellido" value="<?php echo $json_usuario->lastName; ?>" ></td>
</tr>

<tr style="text-align: right;">
        <td>Sexo &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>
                <select class="select-semi-large" name="gender" id="gender" required>
                <option value="<?php echo ($json_usuario->gender!=null)?$json_usuario->gender:'' ?>"><?php echo ($json_usuario->gender!=null)?$json_usuario->gender:'' ?> </option>
                <option value="Femenino">Femenino</option>
                <option value="Masculino">Masculino</option>
         </td>
</tr>

<tr style="text-align: right;">
        <td>Fecha de nacimiento  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td> <div class="pull-left"> <input id="birth_day" type="text" class="datepicker" name="birth_day" value="<?php echo date('d-m-Y',$json_usuario->birthday/1000) ?>" required> </div> </td>
</tr>

<tr style="text-align: right;">
	<td><h4>Datos laborales</h4></td>
</tr>

<tr style="text-align: right;">
        <td>Cargo  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="cargo" type="text" class="input-semi-large" name="cargo" value="<?php echo $json_usuario->position ?>" ></td>
</tr>
<tr style="text-align: right;">
        <td>Correo &nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td><input id="email" type="text" class="input-semi-large" name="email" value="<?php echo $json_usuario->email ?>" ></td>
</tr>
<tr style="text-align: right;">
        <td>Tipo de contrato &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>
                <select class="select-semi-large" name="contractType" id="contractType" required>
                <option value="<?php echo ($json_usuario->contractType!=null)?$json_usuario->contractType:'' ?>"><?php echo ($json_usuario->contractType!=null)?(($json_usuario->contractType==1)?'Indefinido':(  ($json_usuario->contractType==2)?'Plazo fijo':'Reemplazo'   )):''  ?> </option>
                <option value=1>Indefinido</option>
                <option value=2>Plazo fijo</option>
		<option value=2>Reemplazo</option>
         </td>
</tr>


<tr>
        <td style="text-align: right;">Gerencia  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td>
                <select  size="36" style="width:382px" data-placeholder="Seleccione la gerencia" class="chosen"  id="gerencia" name = "gerencia"  required >
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
<tr>
        <td style="text-align: right;">Localidad  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td>
                <select  size="36" style="width:382px" data-placeholder="Seleccione la localidad" class="chosen"  id="localidad" name = "localidad"   required>
                <option value="null"></option>
                <?php
                        foreach ($json_localidad as $localidad){
                ?>
                                <option  class="text-left"  value = '<?php echo $localidad->name.' - '.$localidad->code ?>'><?php echo $localidad->name.' - '.$localidad->code ?></option>
                <?php
                        }
                ?>
                </select>
        </td>
</tr>
<tr>
        <td style="text-align: right;">Centro de costos  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td>
                <select  size="36" style="width:382px" data-placeholder="Seleccione el centro de costo" class="chosen"  id="centro" name = "centro"   required>
                <option value="null"></option>
                <?php
                        foreach ($json_centro as $centro){
                ?>
                                <option  class="text-left"  value = '<?php echo $centro->name.' - '.$centro->code ?>'><?php echo $centro->name.' - '.$centro->code ?></option>
                <?php
                        }
                ?>
                </select>
        </td>
</tr>



<tr style="text-align: right;">
        <td>Código de área &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="codigo"  maxlength="2" type="number" class="input-semi-large" name="codigo" value="<?php echo ($json_usuario->areaCode!=0)?$json_usuario->areaCode:'' ?>" placeholder="El código de ciudad debe tener 1 o 2 dígitos según la región"></td>
</tr>
<tr style="text-align: right;">
        <td>Teléfono  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="anexo" type="number" class="input-semi-large" name="anexo" value="<?php echo ($json_usuario->annexPhone!=0)?$json_usuario->annexPhone:'' ?>" placeholder="Formato 2652069" ></td>
</tr>
<tr >
        <td></td>
        <td> <font color="#A4A4A4">Formato 2652069</font></td>
</tr>
<tr style="text-align: right;">
        <td>Celular  &nbsp;&nbsp;&nbsp;&nbsp; </td>
	<td><input id="celular"  type="number" class="input-semi-large" name="celular" value="<?php echo ($json_usuario->phone!=0)?$json_usuario->phone:'' ?>" pattern="\d*"  maxlength="9" placeholder="El número debe tener 9 dítigos. Ejemplo 98458300"   > </td>
</tr>
<tr >
        <td></td>
        <td> <font color="#A4A4A4">Número con 9 dígitos</font></td>
</tr>

<!-- Valores ocultos  type='hidden'  -->
<input id="gerencia_def"  type='hidden' value ='<?php echo $json_usuario->management ?>' >
<input id="localidad_def" type='hidden' value ='<?php echo $json_usuario->location ?>' >
<input id="centro_def" 	  type='hidden' value ='<?php echo $json_usuario->costCenter ?>' >
</table>

<div class="form-actions">
        <button class="btn btn-primary" type="submit">Guardar</button>
</div>


<?php else: ?>
    <p>No hay usuario asociado al rut: <?php echo $rut ?></p>
<?php endif; ?>

<script>

var idCampoGerenciaDef="gerencia_def";
var idCampoGerencia= "gerencia";
var idCampoLocalidadDef="localidad_def";
var idCampoLocalidad="localidad";
var idCampoCentro="centro";
var idCampoCentroDef="centro_def";

function cargarDatos(){
	//Gerencia
	var gerencia_default = document.getElementById(idCampoGerenciaDef).value;
	if(gerencia_default!=""){
		$("#"+idCampoGerencia).data("chosen").default_text = gerencia_default;//.toUpperCase();
                $("#"+idCampoGerencia).val(gerencia_default); 
                $("#"+idCampoGerencia).trigger("liszt:updated"); //update chosen
                $("#"+idCampoGerencia).val(gerencia_default).trigger("chosen:updated"); 
	}
	//Loacalidad
	var localidad_default = document.getElementById(idCampoLocalidadDef).value;
        if(localidad_default!=""){
                $("#"+idCampoLocalidad).data("chosen").default_text = localidad_default;//.toUpperCase();
                $("#"+idCampoLocalidad).val(localidad_default); 
                $("#"+idCampoLocalidad).trigger("liszt:updated"); //update chosen
                $("#"+idCampoLocalidad).val(localidad_default).trigger("chosen:updated"); 
        }
	//Centro de costos
	 var centro_default = document.getElementById(idCampoCentroDef).value;
        if(centro_default!=""){
                $("#"+idCampoCentro).data("chosen").default_text = centro_default;//.toUpperCase();
                $("#"+idCampoCentro).val(centro_default); 
                $("#"+idCampoCentro).trigger("liszt:updated"); //update chosen
                $("#"+idCampoCentro).val(centro_default).trigger("chosen:updated"); 
        }	
	
}

</script>
