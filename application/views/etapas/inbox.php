<script>

function descargarDocumentos(tramiteId) {
    $("#modal").load(site_url + "etapas/descargar/" +tramiteId);
    $("#modal").modal();
    return false;
}

$(document).ready(function() {
  $('#select_all').click(function(event) {
      var checked = [];
      $('#tramites').val();
      if(this.checked) {
          $('.checkbox1').each(function() {
              this.checked = true;
          });
      }else{
          $('.checkbox1').each(function() {
              this.checked = false;
          });
      }
      $('#tramites').val(checked);
  });

});

function descargarSeleccionados() {
    var numberOfChecked = $('.checkbox1:checked').length;
    if(numberOfChecked == 0){
      alert('Debe seleccionar al menos un trámite');
      return false;
    }else{
      var checked = [];
      $('.checkbox1').each(function() {
          if($(this).is(':checked')){
            checked.push(parseInt($(this).val()));  
          }
      });
      $('#tramites').val(checked);
      var tramites = $('#tramites').val();
      $("#modal").load(site_url + "etapas/descargar/" +tramites);
      $("#modal").modal();
      return false;  
    }
}

</script>

<h2 style="line-height: 28px;">
    Bandeja de Entrada
    <!--buscador-->   
  <!--  <div class='pull-right'>
        <form class="form-search" method="GET" action="<?= current_url() ?>">
            <div class="input-append">
                <input name="buscar" value="<?= $buscar?>" type="text" class="search-query" />
                <button type="submit" class="btn">Buscar</button>
            </div>
        </form>
    </div>-->
</h2>
<?php if (count($etapas) > 0): ?>
<table id="mainTable" class="table">
    <thead>
        <tr>
            <th style="width:1%"></th>
            <th style="width: 40px;"><a href="<?=current_url().'?orderby=id&direction='.($direction=='asc'?'desc':'asc')?>" style="width:9px">Nro</a></th>
            <th>Ref.</th>
            <th><a href="<?=current_url().'?orderby=proceso_nombre&direction='.($direction=='asc'?'desc':'asc')?>">Nombre</a></th>
            <th><a href="<?=current_url().'?orderby=tarea_nombre&direction='.($direction=='asc'?'desc':'asc')?>">Etapa</a></th>
            <th><a href="<?=current_url().'?orderby=updated_at&direction='.($direction=='asc'?'desc':'asc')?>">Modificación</a></th>
<!--        <th><a href="//<//?=current_url().'?orderby=vencimiento_at&direction='.($direction=='asc'?'desc':'asc')?> -->
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php $registros=false; ?>
        <?php foreach ($etapas as $e): ?>
		
            <?php
          	  $prev = $e->tareaPreVis;
                  
		  $file = $e->file;
		  if ($file)
                      $registros=true;
                  
            ?>
	    
           <tr>  <?php $prev?'data-toggle="popover" data-html="true" data-title="<h4>Previsualización</h4>" data-content="'.htmlspecialchars($prev).'"data-trigger="hover" data-placement="bottom"':''?>
		<?php if($descarga_masiva): ?>
                  <?php if($file): ?>
                  <td><div class="checkbox"><label><input type="checkbox" class="checkbox1" name="select[]" value="<?=$e->tramite_id?>"></label></div></td>
                  <?php else: ?>
                  <td></td>
                  <?php endif; ?>
                  <?php else: ?>
                  <td></td>
                <?php endif; ?>
                <td><?=$e->tramite_id?></td>
                <td class="name">
                    <?php
                        $tramite_nro ='';
                        foreach ($e->trDatoSeg as $tra_nro){
                           if($tra_nro->nombre == 'tramite_ref'){
                                $tramite_nro = $tra_nro->valor;
                            }                              
                        }                         
                        //echo $tramite_nro != '' ? $tramite_nro : $e->Tramite->Proceso->nombre;
			echo $prev !=''?$prev:$e->proceso_nombre;
                    ?>
                </td><!--Nro. tramites-->                
                <!--<td class="name"><a class="preventDoubleRequest" href="<?//=site_url('etapas/ejecutar/'.$e->id)?>"><?//= $e->Tramite->Proceso->nombre ?></a></td> Nombre-->
                <td class="name"><a class="preventDoubleRequest" href="<?=site_url('etapas/ejecutar/'.$e->id)?>">
                     <?php 
                          $tramite_descripcion ='';
                          foreach ($e->trDatoSeg as $tra){
                             if($tra->nombre == 'tramite_descripcion'){
                                  $tramite_descripcion = $tra->valor;
                             }  
                          }
                         echo $tramite_descripcion != '' ? $tramite_descripcion : $e->proceso_nombre;
                    ?>                    
                </a></td><!--Tramites-->                
                <td><?=$e->tarea_nombre?></td>
                <td class="time"><?= strftime('%d.%b.%Y',mysql_to_unix($e->updated_at))?><br /><?= strftime('%H:%M:%S',mysql_to_unix($e->updated_at))?></td>
           <!--     <td>//<//?=$e->vencimiento_at?strftime('%c',strtotime($e->vencimiento_at)):'N/A'?></td>-->
                <td class="actions">
                    <a href="<?=site_url('etapas/ejecutar/'.$e->id)?>" class="btn btn-primary preventDoubleRequest"><i class="icon-edit icon-white"></i> Realizar</a>
                    <?php if($descarga_masiva): ?>
                      <?php if($file): ?>
                      <a href="#" onclick="return descargarDocumentos(<?=$e->tramite_id?>);" class="btn btn-success"><i class="icon-download icon-white"></i> Descargar</a>
                      <?php endif; ?>
                    <?php endif; ?>
                    <!--<?php if($e->netapas==1):?><a href="<?=site_url('tramites/eliminar/'.$e->tramite_id)?>" class="btn" onclick="return confirm('¿Esta seguro que desea eliminar este tramite?')"><i class="icon-trash"></i></a><?php endif ?>-->
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if($descarga_masiva): ?>
  <?php if($registros): ?>
  <div class="pull-right">
  <div class="checkbox">
  <input type="hidden" id="tramites" name="tramites" />
  <label>
   <input type="checkbox" id="select_all" name="select_all" /> Seleccionar todos
   <a href="#" onclick="return descargarSeleccionados();" class="btn btn-success preventDoubleRequest"><i class="icon-download icon-white"></i> Descargar seleccionados</a>
  </label>

  </div>
  </div>
  <div class="modal hide fade" id="modal">

  </div>
  <?php endif; ?>
<?php endif; ?>
 <p><?= $links ?></p>
<?php else: ?>
<p>No hay procesos pendientes en su bandeja de entrada.</p>
<?php endif; ?>
