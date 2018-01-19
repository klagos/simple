<h2 style="line-height: 28px;">
    Reporte de pago de Licencias
    <!--buscador--> 
<br></br>  
<form  method="GET" action="<?= site_url('licencias/generarpago')?>">
<fieldset>
        <label>Fecha de pago</label>
                <input type="text" class="datepicker" name="fecha_pago" value="" placeholder="dd-mm-aaaa" required/>	
        <label>Tipo de pago</label>
                <select class="select" name="tipo_pago" required>
			<option value="">Seleccionar</option>
			<option value="15">Quincena</option>
			<option value="30">Fin de mes</option>
		</select>
	<div class="form-actions">
		<button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form>
 
