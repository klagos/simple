<ul class="breadcrumb">
	<li><a href="<?= site_url('backend/auditoria')?>">Auditoría </a></li>
	<span class="divider">/</span>

	<li class="active">
    	<?=$title?>
    </li>
</ul>

<h2 style = "text-align:center;"><?=$title?></h2>

<div class="well">
<dl class = "dl-horizontal">
	<dt>  Operacion </dt>
	<dd> <?= $registro->operacion?> </dd>
	<dt>  Usuario  </dt>
	<dd> <?= htmlspecialchars($registro->usuario)?></dd>
	<dt>  Fecha </dt>
	<dd> <?= $registro->fecha?></dd>
	<dt>  Motivo </dt>
	<dd> <?= $registro->motivo != '' ? $registro->motivo : ' '?> </dd>
	
</dl>
</div>
<?php
$column = 0;
foreach($registro->detalles as $elemento=>$detalle):
?>
<?php if ($column == 0):?>
<div class = "row-fluid">
	<div class = "span12">
	
<?php endif; ?>

<div class = "span6">
<h4><?=str_replace('_', ' ', ucfirst($elemento))?></h4>
<div class="well">
<dl class = "dl-horizontal">
	<?php
	if (count($detalle)>0):
	foreach($detalle as $key=>$value):
	?>
	<dt><?=str_replace('_', ' ', ucfirst($key))?></dt>
		<?php if(is_array($value)):?>
		<dl class="dl-horizontal">
			<?php foreach($value as $key=>$value):?>
			<dt><?=str_replace('_', ' ', ucfirst($key))?></dt>
			<dd><?=is_array($value)?json_encode($value) : $value?></dd>
				
			<?php endforeach;?>
		</dl>
		<?php else:?>
		
		<dd><?=$value?></dd>
		<?php endif;?>
	<?php 
	endforeach;
	else:
	?>
	<p>Sin datos</p>
	<?php endif;?>
</dl>
</div>
</div>
<?php 
$column++;
if ($column>=2):
?>

</div>
</div>
<?php
$column = 0;
endif;?>

<?php endforeach;?>