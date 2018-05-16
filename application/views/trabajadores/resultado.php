<h2 style="line-height: 28px;">
    Colaborador.
<br></br>
</h2>
<?php if ($json_usuario!=""): ?>
<form  method="GET" action="<?= site_url('piefirma/update_half')?>">
<table>
<tr style="text-align: right;">
	<td>Rut &nbsp;&nbsp;&nbsp;&nbsp;</td>
       	<td><input name="rut" id ="rut" class="input-semi-large" type="text" value="<?php echo $json_usuario->rut ?> " readonly></td>
</tr>

<tr style="text-align: right;">
        <td>Nombre &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <input maxlength="22" id="nombre" type="text" class="input-semi-large" name="nombre" value="<?php echo $json_usuario->name  ?>" placeholder="número máximo 18 caracteres" ></td>
</tr>

<tr style="text-align: right;">
        <td>Apellido &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <input id="apellido" type="text" class="input-semi-large" name="apellido" value="<?php echo $json_usuario->lastName; ?>" readonly ></td>
</tr>

<tr style="text-align: right;">
        <td>Cargo  &nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="cargo" type="text" class="input-semi-large" name="cargo" value="<?php echo $json_usuario->position ?>" readonly></td>
</tr>
<tr style="text-align: right;">
        <td>Correo &nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td><input id="email" type="text" class="input-semi-large" name="email" value="<?php echo $json_usuario->email ?>" readonly></td>
</tr>
<tr style="text-align: right;">
        <td>Sexo &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input id="gender" type="text" class="input-semi-large" name="gender" value="<?php echo $json_usuario->email ?>" readonly></td>
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
	<td><input id="celular"  type="number" class="input-semi-large" name="celular" value="<?php echo ($json_usuario->phone!=0)?$json_usuario->phone:'' ?>" pattern="\d*"  maxlength="9" placeholder="El número debe tener 9 dítigos. Ejemplo 984583008"> </td>
</tr>
<tr >
        <td></td>
        <td> <font color="#A4A4A4">Número con 9 dígitos</font></td>
</tr>
<!-- Rut trabajador-->
</table>

<div class="form-actions">
        <button class="btn btn-success" type="submit">Descargar</button>
</div>


<?php else: ?>
    <p>No hay usuario asociado al rut: <?php echo $rut ?></p>
<?php endif; ?>

<script>
var idCampoGerencia="gerencia_trabajador";
var idCampoLocalidad="localidad";
var idCampoCentro="centro";


</script>
