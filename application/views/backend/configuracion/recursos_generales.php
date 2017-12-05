<div class="row-fluid">
    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?=site_url('backend/configuracion')?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Recursos Generales</li>
        </ul>
        <?php $this->load->view('messages') ?>
        <div>
            <button class="btn btn-warning js-nuevo-recurso">Cargar Nuevo Recurso</button>
        </div>
        <table id="tblresources" class="table">
            <thead>
                <tr>
                    <th style="width: 300px;">ID</th>
                    <th>Descripci&oacute;n</th>
                    <th>Tipo</th>
                    <th style="width: 180px;" >Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(isset($checked) && $checked==1){
                    if(isset($data) && is_array($data)){
                        if(count($data)>1){
                            foreach($data as $item){
                                $id=str_replace('://','/', $item->noderef);
                                $acciones='<a class="btn btn-primary js-editar-recurso" data-id="'.$id.'" data-noderef="'.$item->noderef.'" data-nombre="'.$item->nombre.'" data-desc="'.$item->descripcion.'" href="#"><i class="icon-white icon-edit"></i> Editar</a> <a class="btn btn-danger btncanappofun" href="#" ><i class="icon-white icon-remove"></i> Eliminar</a>';
                                //echo '<tr><td>'.$item->nombre.'</td><td>'.$item->descripcion.'</td><td>'.$item->mimetype.'</td><td>'.$acciones.'</td></tr>';
                            }
                        }else{
                            echo '<tr><td colspan="4">No existen recursos</td></tr>';
                        }
                    }else{
                        echo '<tr><td colspan="4">No existen recursos</td></tr>';
                    }
                }else{
                    echo '<tr><td colspan="4">No esa habilitada la configuraci&oacute;n del CMS</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<form id="frmdata">
    <input type="hidden" name="nombre" id="nombre">
    <input type="hidden" name="id" id="id">
    <input type="hidden" name="desc" id="desc">
</form>
<div id="modaleditresources" class="modal hide fade"></div>
<div id="modalnuevorecurso" class="modal hide fade">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3 id="myModalLabel">Nuevo Recurso</h3>
    </div>
        <div class="modal-body">
            <form id="frmnresource">
                <input id="tipoaccion" name="tipoaccion" value="1" />
                <div class=".validacion"></div>
                <input type="hidden" id="namefile" name="namefile" />
                <div class="validacion"></div>
                <div class="file-uploader" data-action="<?=site_url('uploader/uploadResource')?>"></div>
                <p class="link"></p>
                <label>Descripci&oacute;n</label>
                <textarea id="txtdescripcion" name="descripcion" style="width: 300px; resize: none;"></textarea>
            </form>
        </div>
    <div class="modal-footer">
        <button class="btn js_cerrar_vcancelar" data-dismiss="modal">Cerrar</button>
        <button id="btngrabresource" class="btn btn-primary" >Grabar</button>
    </div>
</div>
<script>
    var urlbase='<?=site_url()?>';
    $(function(){
        ajax_cargar_resources();
        $('#btngrabresource').click(function(){
            if(jQuery.trim($("#txtdescripcion").val())!=""){
                var form=$('.modal-body');
                $(form).append("<div class='ajaxLoader ajaxLoaderfunc'>Cargando</div>");
                var ajaxLoader=$(form).find(".ajaxLoader");
                $(ajaxLoader).css({
                    left: ($(form).width()/2 - $(ajaxLoader).width()/2)+"px", 
                    top: ($(form).height()/2 - $(ajaxLoader).height()/2)+"px"
                });
                var url=urlbase+"uploader/grabar_nuevo_cms";
                var $obja=$('.link').children('a');
                var file=$($obja[0]).html();
                $('#namefile').val(file);
                var param=$('#frmnresource').serialize();
                $.ajax({
                    url: url,
                    data:param,
                    dataType: "json",
                    success: function( data ) {
                        if(jQuery.trim(data.status)=="success"){
                            $('.ajaxLoader').remove();
                            $("#modalnuevorecurso").modal('toggle');
                        }else{
                            $('.ajaxLoader').remove();
                            $('.validacion').html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>'+data.error+'</div>');
                        }
                    },
                    complete:function(){
                        $('.ajaxLoader').remove();
                        ajax_cargar_resources();
                    },
                    error:function(xhr,status,error){
                        $('.ajaxLoader').remove();  
                    }
                });
            }else{
                $('.validacion').html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>Digite una descripción.</div>');
            }
        });
        $('.js-nuevo-recurso').click(function(){
            $("#myModalLabel").html('Nuevo Recurso');
            $("#tipoaccion").val(1);
            $("#txtdescripcion").val('');
            $('.qq-upload-list').html('');
            $('.link').html('');
            $("#modalnuevorecurso").modal();
            $('#modalnuevorecurso').on('hidden.bs.modal', function (e) {
                var $obja=$('.link').children('a');
                var file=$($obja[0]).html();
                eliminar_archivo(file);
            });
        });
    });
    function eliminar_archivo(nombre){
        var url=urlbase+"uploader/eliminar_file";
        $.ajax({
            url: url,
            data:{
                file:nombre
            },
            dataType: "json",
            success: function( data ) {
                
            }
        });
    }
    function ajax_cargar_resources(){
        var url=urlbase+"backend/configuracion/ajax_listar_recursos";
        var form=$('#main');
        $(form).append("<div class='ajaxLoader ajaxLoaderfunc'>Cargando</div>");
        var ajaxLoader=$(form).find(".ajaxLoader");
        $(ajaxLoader).css({
            left: ($(form).width()/2 - $(ajaxLoader).width()/2)+"px", 
            top: ($(form).height()/2 - $(ajaxLoader).height()/2)+"px"
        });
        $("#tblresources tbody").html('');
        $.ajax({
            url: url,
            dataType: "json",
            success: function( data ) {
                $.each(data.rows,function(index,element){
                    $("#tblresources tbody").append(element);

                    $('.js-editar-recurso').off();
                    $('.js-editar-recurso').on('click',function(){
                        $("#myModalLabel").html('Editar Recurso');
                        $("#tipoaccion").val(0);
                        var nombre=$(this).attr('data-nombre');
                        var desc=$(this).attr('data-desc');
                        var id=$(this).attr('data-id');


                        $("#txtdescripcion").val('');
                        $('.qq-upload-list').html('');
                        $('.link').html('');
                        $("#modalnuevorecurso").modal();
                        $('#modalnuevorecurso').on('hidden.bs.modal', function (e) {
                            var $obja=$('.link').children('a');
                            var file=$($obja[0]).html();
                            eliminar_archivo(file);
                        });
                    });

                });
                $('.ajaxLoader').remove();
            },
            error:function(xhr,status,error){
                $('.ajaxLoader').remove();
            }
        });
    }
    /**/
</script>