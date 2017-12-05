<div class="row-fluid">  
    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?=site_url('backend/configuracion')?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Configuraci&oacute;n Services</li>
        </ul>       
        <?php $this->load->view('messages') ?>
        <div class="validacion"></div>
        <form method="POST" action="<?= base_url('backend/configuracion/datos_services') ?>" id="formservices" style="position:relative;" class="">
            <div class="header-alfresco">
                <h3>Informaci&oacute;n de Conexi&oacute;n</h3>
            </div>
            <div>
                <label>AppKey</label>
                <input type="text" id="appkey" name="appkey" value="<?= $appkey ?>" placeholder="Digite AppKey" />
            </div>
            <div>
                <label>Domain</label>
                <input type="text" id="domain" name="domain" value="<?= $domain ?>" placeholder="Digite Domain" />
            </div>
            <input type="button" class="btn btn-primary" value="Guardar" onclick="guardar_datos();" />          
        </form>
    </div>
</div>
<div id="modalinfo" class="modal hide fade"></div>
<script>
    $(function(){

    });

    function guardar_datos(){
        $('.validacion').html('');
        var form=$('#formservices');
        form.submitting=false;
        var url=form.attr('action');
        $(form).append("<div class='ajaxLoaderfunc'>Cargando</div>");
        var ajaxLoader=$(form).find(".ajaxLoaderfunc");
        $(ajaxLoader).css({
            left: ($(form).width()/2 - $(ajaxLoader).width()/2)+"px", 
            top: ($(form).height()/2 - $(ajaxLoader).height()/2)+"px"
        });
        $.ajax({
            url: url,
            data:form.serialize(),
            type:form.attr('method'),
            dataType: "json",
            success: function( data ) {
                if(data.code==200){
                    $("#modalinfo").load(site_url + "backend/configuracion/ajax_modal_info");
                    $("#modalinfo").modal();
                }else{
                    $('.validacion').html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>'+data.mensaje+'.</div>');
                }
                $('.ajaxLoaderfunc').remove();
            }
        });
        return false;
    }
</script>