<h2 style="line-height: 28px;">
    Generación Pie de firma. 
    <!--buscador--> 
<br></br>  
<form  method="GET" action="<?= site_url('piefirma/buscar')?>">
<fieldset>
        <label>Ingrese su Rut</label>
                <input type="text" class="text" name="rut" id = "rut" value="" placeholder="16755073-8" autocomplete="off" required/>
	<label><font color="#A4A4A4"> Rut sin punto y con guión. Ejemplo : 16755073-8 </font></label>  
        <div class="form-actions">
                <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
</fieldset>
</form>
