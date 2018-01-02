<h2 style="line-height: 28px;">
    Buscador de Licencias
    <!--buscador--> 
<br></br>  
<form  method="GET" action="<?= site_url('licencias/buscar')?>">
<fieldset>
        <label>Número de licencia</label>
                <input type="number" name="licencia_numero" value=""/>
        <label>Rut del trabajador</label>
		<input type="text" maxlenght="8" name="trabajador_rut" value=""/>
        <div class="form-actions">
		<button class="btn btn-primary" type="submit">Buscar</button>
        </div>
</fieldset>
</form>
 
