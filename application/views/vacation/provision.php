<h2 style="line-height: 28px;">
    Reporte de pago de Licencias
    <!--buscador--> 
<br></br>  
<form  method="GET" action="<?= site_url('vacation/provision_diasSolicitados')?>">
<fieldset>
        <label>Seleccione el mes</label>
                <select class="select" name="mes" required>
			<option value="">Seleccionar</option>
			<option value=0>Enero</option>
			<option value=1>Febrero</option>
			<option value=2>Marzo</option>
			<option value=3>Abril</option>
			<option value=4>Mayo</option>
			<option value=5>Junio</option>
			<option value=6>Julio</option>
			<option value=7>Agosto</option>
			<option value=8>Septiembre</option>
			<option value=9>Octubre</option>
			<option value=10>Noviembre</option>
			<option value=11>Diciembre</option>
		</select>
	<div class="form-actions">
		<button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form>
 
