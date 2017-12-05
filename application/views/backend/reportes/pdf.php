<?php
$col_size = count($reporte[0]);
$row_size = count($reporte);

?>



<html>
<head>
        <link href="<?= base_url() ?>assets/css/bootstrap.css" rel="stylesheet">
        <link href="<?= base_url() ?>assets/css/responsive.css" rel="stylesheet">
                <link href="<?= base_url() ?>assets/css/common.css" rel="stylesheet">
        <link href="<?= base_url() ?>assets/css/frontend.css" rel="stylesheet">
        
        <style>
        	@page {
        	margin: 7%;
        	margin-header: 5mm;
        	margin-footer: 5mm;
        	header : myHeader;
        	footer : myFooter;
        	}
        	
        	
        	
        </style>
</head>
<body>	


<htmlpageheader name="myHeader">
<H4><?php echo $title; ?></H4>
<p>Consultado por: <?= UsuarioBackendSesion::usuario()->nombre .' '.  UsuarioBackendSesion::usuario()->apellidos ?></p>
	
</htmlpageheader>
<pagefooter content-left="{DATE d/m/Y}" content-center="" content-right="{PAGENO}/{nbpg}" name="myFooter"/>
<div class="row-fluid">
	<div class="span12">
		<dl class="dl-horizontal">
			<dt>Duración promedio</dt><dd> <?=$promedio_tramite?abs($promedio_tramite) . ' días':'No hay tramites finalizados'?></dd>
			<dt>Cantidad de trámites</dt><dd> <?=$tramites_completos + $tramites_pendientes?></dd>
			<dt>Completos</dt><dd> <?=$tramites_completos?></dd>
			<dt>En curso</dt> <dd><?=$tramites_pendientes?></dd>
			<dt>En curso vencidos</dt><dd> <?=$tramites_vencidos?></dd>                        
		</dl>
	</div>
</div>
<table class = "table">
	<thead>
	
		<tr>
		<?php for($col=0;$col<$col_size;$col++):?>
		<th><?php echo $reporte[0][$col];?></th>
		<?php endfor;?>
		</tr>	
	
	</thead>

	<?php for($row=1;$row<$row_size;$row++):?>
		<tr>
		<?php for($col = 0;$col<$col_size;$col++):?>
			<td><?php echo $reporte[$row][$col];?></td>
		<?php endfor;?>
		</tr>
	<?php endfor;?>
</table>
</body>
</html>