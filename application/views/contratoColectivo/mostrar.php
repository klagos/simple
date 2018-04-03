<h2 style="line-height: 28px;"> 

Convenios colectivos
<br><br>
</h2>
<body>
<table>
<tr>
        <td><h4> Convenios Vigentes </h4></td>
</tr>
<tr>
        <td>Sindicato Nª 1  &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <p><a  style="text-align: center;"     class="btn btn-success" target="_blank" href="<?=site_url('contratoColectivo/vigentes/'. $archivo1)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
</tr>

<tr>
        <td>Sindicato Nacional  &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <p><a  style="text-align: center;"     class="btn btn-success" target="_blank" href="<?=site_url('contratoColectivo/vigentes/'. $archivo2)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
</tr>

<tr>
        <td>Sindicato de Profesionales  &nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td> <p><a  style="text-align: center;"     class="btn btn-success" target="_blank" href="<?=site_url('contratoColectivo/vigentes/'. $archivo3)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
</tr>
<tr>
        <td><h4> Convenios Historicos </h4></td>		
</tr>

</table>

<h5><a href="#" id="id_sind1" onclick="return mostrarSind1();">Mostrar Sindicato Nº 1 +</a></h5>
       <?php if (sizeof($list_sind1) > 0) { ?>
                <div class="procesos_eliminados">
                <table>
		<tr><td><b>Período</b></td><td></td></tr>
                                 <?php foreach($list_sind1 as $d): ?>
                                        <tr>
                                        <td><?= explode(".",$d)[0] ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td> <p><a  style="text-align: center;" class="btn btn-success" target="_blank" href="<?=site_url('contratoColectivo/historicos/sind1/'.$d)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
                                        </tr>
         
                                 <?php endforeach; ?>
               	</table>
                </div>
        <?php } ?>


<h5><a href="#" id="id_sind2" onclick="return mostrarSind2();">Mostrar Sindicato Nacional +</a></h5>
       <?php if (sizeof($list_sind2) > 0) { ?>
                <div class="procesos_eliminados_1">
                <table>
		<tr><td><b>Período</b></td><td></td></tr>
                                 <?php foreach($list_sind2 as $d): ?>
                                        <tr>
                                        <td><?= explode(".",$d)[0] ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td> <p><a  style="text-align: center;" class="btn btn-success" target="_blank" href="<?=site_url('contratoColectivo/historicos/sind2/'.$d)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
                                        </tr>
         
                                 <?php endforeach; ?>
                </table>
                </div>
        <?php } ?>

<h5><a href="#" id="id_sind3" onclick="return mostrarSind3();">Mostrar Sindicato Profesionales +</a></h5>
       <?php if (sizeof($list_sind3) > 0) { ?>
                <div class="procesos_eliminados_2">
                <table>
		<tr><td><b>Período</b></td><td></td></tr>
                                 <?php foreach($list_sind3 as $d): ?>
                                        <tr>
                                        <td><?= explode(".",$d)[0] ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td> <p><a  style="text-align: center;" class="btn btn-success" target="_blank" href="<?=site_url('contratoColectivo/historicos/sind3/'.$d)?>" ><i class="icon-download-alt icon-white"></i> Descagar</a></p></td>
                                        </tr>

                                 <?php endforeach; ?>
                </table>
                </div>
        <?php } ?>



<script>
    function mostrarSind1(){
	$(".procesos_eliminados").slideToggle('slow', callbackSind1);
        return false;
    }

    function callbackSind1() {
        var $link = $("#id_sind1");
        $(this).is(":visible") ? $link.text("Ocultar convenios Sindicato Nº 1-") : $link.text("Mostrar Sindicato Nº 1 +");
    }

    function mostrarSind2(){
        $(".procesos_eliminados_1").slideToggle('slow', callbackSind2);
        return false;
    }

    function callbackSind2() {
        var $link = $("#id_sind2");
        $(this).is(":visible") ? $link.text("Ocultar convenios Sindicato Nacional -") : $link.text("Mostrar Sindicato Nacional  +");
    }
   
    function mostrarSind3(){
        $(".procesos_eliminados_2").slideToggle('slow', callbackSind3);
        return false;
    }

    function callbackSind3() {
        var $link = $("#id_sind3");
        $(this).is(":visible") ? $link.text("Ocultar convenios Sindicato de Profesionales -") : $link.text("Mostrar Sindicato de Profesionales  +");
    }


</script>
