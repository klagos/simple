<h2 style="line-height: 28px;">
    Datos Actualizados
    <!--buscador-->
<br></br>
<tr>
        <td><h4>   Los datos del colaborador   <?php echo $name.' '. $apellido.' '.'han sido actualizados' ?></h4></td>
</tr>
<body onload="cargarDatos()">

<form  method="GET" action="<?= site_url('trabajadores/buscar')?>">
	<div class="form-actions" >
                <button class="btn btn-primary" type="submit">Volver</button>
        </div>


</form>
