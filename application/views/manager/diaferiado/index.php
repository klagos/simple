<script src="<?= base_url() ?>assets/js/jquery.chosen/chosen.jquery.min.js"></script> <?php //Soporte para selects con multiple choices     ?>
<script src="<?= base_url() ?>assets/js/jquery.select2/dist/js/select2.min.js"></script> <?php //Soporte para selects con multiple choices     ?>
<script src="<?= base_url() ?>assets/js/jquery.select2/dist/js/i18n/es.js"></script> <?php //Soporte para selects con multiple choices     ?>
<link rel="stylesheet" href= "<?= base_url('assets/calendar/css/calendar.css') ?>" >
<script src= "<?= base_url('/assets/js/jquery-ui/js/jquery-ui.js') ?>"></script>
<script src= "<?= base_url('/assets/calendar/js/moment-2.2.1.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/calendar/components/underscore/underscore-min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/calendar/components/jstimezonedetect/jstz.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/calendar/js/language/es-CO.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/calendar/js/calendar.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/calendarmanager.js?v=0.5') ?>"></script>
<script src="<?= base_url() ?>assets/js/collapse.js"></script>
<script src="<?= base_url() ?>assets/js/transition.js"></script>
<script src="<?= base_url() ?>assets/js/bootstrap-datetimepicker.min.js"></script>
<ul class="breadcrumb">
    <li class="active"><?=$title?></li>
</ul>
<div class="containter-calendar container-feriados">
    <input type="hidden" id="urlbase" value="<?= base_url() ?>" />
    <div class="page-header">
        <h3></h3>
        <div class="form-inline">
            <div class="btn-group">
                <button class="btn" data-calendar-nav="prev">&lt;&lt;</button>
                <button class="btn btn-primary" data-calendar-nav="today">Mes Actual</button>
                <button class="btn" data-calendar-nav="next">&gt;&gt;</button>
            </div>
            <div class="btn-group">
                <button class="btn anoradios" data-calendar-view="year">A&ntilde;o</button>
                <button class="btn active mesradios" data-calendar-view="month">Mes</button>
            </div>
        </div>
    </div>
    <div id="calendar" class="calendar float-left"></div>
    <div class="detallecal" style="display:none;">
        <div class="labeldetconfglob"><label>D&iacute;as Feriados Registrados</label></div>
        <div id="desccalendar" class="det col-md-5 col-sm-12 col-xs-12"></div>
        <div class="container-bot"><a class="btn btn-danger" href="#" onclick="eliminarDia();"><i class="icon-white icon-remove"></i> Eliminar</a></div>
    </div>
</div>
<div id="agregardia" class="modal hide fade modalconfg"></div>
<input type="hidden" id="fechaaelim" value="" />
<input type="hidden" id="idelim" value="" />
<script>
    window.listado=null;
    var calendars = {};
    $(function(){
        carcarObjectCalendar();
        
    });    
    function carcarObjectCalendar(){
        moment.lang('es');
        var thisMonth = moment().format('YYYY-MM');
    }
    function getListado(){
        return listado;
    }
    function eliminarDia(){
        var swselecciono=0;
        var fecha='0';
        if(jQuery.trim($('#fechaaelim').val())!=''){
            swselecciono=1;
            fecha=$('#fechaaelim').val();
            var idelim=$('#idelim').val();
            $("#agregardia").load(site_url + "manager/diaferiado/ajax_confirmar_eliminar_dia?select="+swselecciono+"&fecha="+fecha+"&id="+idelim);
            $("#agregardia").modal();
        }
    }
</script>