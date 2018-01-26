<h2 style="line-height: 28px;">
    Buscador de Licencias
    <!--buscador--> 
<br></br>  
<form  method="GET" action="<?= site_url('licencias/buscar')?>">
<fieldset>
        <label>Número de licencia</label>
                <input type="number" name="licencia_numero" value=""/>
        <label>Estado licencia</label>
		<select name="licencia_estado">
			<option value=""></option>
			<option value="ingresada">Ingresada</option>
			<option value="pagada">Pagada</option>
			<option value="retornada">Retornada</option>
		</select>
	<label>Tipo licencia</label>
                <select name="licencia_tipo">
                        <option value=""></option>
                        <option value=1>Enfermedad o accidente común</option>
                        <option value=2>Medicina preventiva</option>
			<option value=3>Pre y postnatal</option>
			<option value=4>Enfermedad grave del niño</option>
			<option value=5>Accidente del trabajo</option>
			<option value=6>Enfermedad profesional</option>
			<option value=7>Patologías del embarazo</option>
                </select>
	<label>Rut del trabajador</label>
                <input type="text" maxlenght="8" name="trabajador_rut" value=""/>
	
        <div class="form-actions">
		<button class="btn btn-primary" type="submit">Buscar</button>
        </div>
</fieldset>
</form>
 
