<h2 style="line-height: 28px;">
    Reportes de correos
    <!--buscador--> 
<br></br>
<tr>
        <td><h4> Descarga masiva directorios de correos y teléfonos</h4></td>
</tr>
<form  method="GET" action="<?= site_url('guiatelefono/email_report')?>">

<fieldset>
	<div class="form-group">
             <label><input class="myCheckBox" name="checkBox[]" type="checkbox" value="e"  > Email</label>
        </div>
        <div class="form-group">
             <label><input class="myCheckBox" name="checkBox[]" type="checkbox" value="p"> Teléfono</label>
        </div>
        <div class="form-actions">
                <button  id="generar" class="btn btn-primary" type="submit" disabled="disabled">Descargar</button>
        </div>
</fieldset>
</form>

<!--SCRIPT PARA VALIDAR LAS CASILLAS -->
<script  type="text/javascript">

        var boxes = $('.myCheckBox');
        boxes.on('change', function () {
                $('#generar').prop('disabled', !boxes.filter(':checked').length);
        }).trigger('change');
</script>
