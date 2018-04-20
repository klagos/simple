<h2 style="line-height: 28px;"> 

Procedimiento de Inducción en Competencias mínimas del seguro de Ley 16.744 
<br><br>
</h2>
<body>
<table>
<tr>
        <td><h4> Documentación Obligatoria </h4></td>
</tr>
<tr>
        <td>Ley 16395 Competencias Suseso  &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <p><a  style="text-align: center;"     class="btn btn-success" target="_blank" href="<?=site_url('procesoInduccion/obligatorio/'. $archivo1)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
</tr>

<tr>
        <td>Libro I. Descripción General del Seguro  &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <p><a  style="text-align: center;"     class="btn btn-success" target="_blank" href="<?=site_url('procesoInduccion/obligatorio/'. $archivo2)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
</tr>

<tr>
        <td>Libro III. Denuncia, Calificación y Evaluación de Incapacidades Permanentes  &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <p><a  style="text-align: center;"     class="btn btn-success" target="_blank" href="<?=site_url('procesoInduccion/obligatorio/'. $archivo3)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
</tr>

<tr>
        <td>Libro V. Prestaciones Médicas   &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <p><a  style="text-align: center;"     class="btn btn-success" target="_blank" href="<?=site_url('procesoInduccion/obligatorio/'. $archivo4)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
</tr>

<tr>
        <td>Circular 3332   &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <p><a  style="text-align: center;"     class="btn btn-success" target="_blank" href="<?=site_url('procesoInduccion/obligatorio/'. $archivo5)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
</tr>



<tr>
        <td><h4> Registro de Cumplimiento de Inducción </h4></td>
</tr>
<tr>
        <td>Registro de cumplimiento  &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <p><a  style="text-align: center;"     class="btn btn-success" target="_blank" href="<?=site_url('procesoInduccion/obligatorio/'. $archivo6)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
</tr>


<tr>
        <td><h4> Documentación Complementaria </h4></td>		
</tr>

</table>

<h5><a href="#" id="id_doc" onclick="return mostrarDoc();">Mostrar documentos complentarios +</a></h5>
       <?php if (sizeof($list_comp) > 0) { ?>
                <div class="procesos_eliminados">
                <table>
		<tr><td><b>Nombre</b></td><td></td></tr>
                                 <?php foreach($list_comp as $d): ?>
                                        <tr>
                                        <td><?= explode(".",$d)[0] ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td> <p><a  style="text-align: center;" class="btn btn-success" target="_blank" href="<?=site_url('procesoInduccion/complement/'.$d)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
                                        </tr>
         
                                 <?php endforeach; ?>
               	</table>
                </div>
        <?php } ?>

<script>

    function mostrarDoc(){
	$(".procesos_eliminados").slideToggle('slow', callbackDoc);
        return false;
    }

    function callbackDoc() {
        var $link = $("#id_doc");
        $(this).is(":visible") ? $link.text("Ocultar documentos complentarios -") : $link.text("Mostrar documentos complentarios +");
    }

</script>
