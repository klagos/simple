window.feriados=new Array();
window.mesvisto='';
window.idfierados=new Array();
$(function(){
    var url=$('#urlbase').val()+'/backend/agendas/EmptyCalendar';
    feriados=cargarDiasFeriados();
    var options = {
        events_source:url,
        view: 'month',
        tmpl_path: $('#urlbase').val()+'/assets/calendar/tmpls/',
        tmpl_cache: true,
        language:'es-CO',
        width:330,
        views: {
            year: {
                slide_events: 1,
                enable: 1
            },
            month: {
                slide_events: 1,
                enable: 1
            },
            week: {
                enable: 0
            },
            day: {
                enable: 0
            }
        },
        classes: {
            months: {
                inmonth: 'cal-day-inmonth',
                outmonth: 'cal-day-outmonth',
                saturday: 'cal-day-weekend',
                sunday: 'cal-day-weekend',
                holidays: 'dia-festivo',
                today: 'cal-hoy'
            },
            week: {
                workday: 'cal-day-workday',
                saturday: 'cal-day-weekend',
                sunday: 'cal-day-weekend',
                holidays: 'dia-festivo',
                today: 'cal-hoy'
            }
        },
        merge_holidays:true,
        holidays:feriados,
        onAfterEventsLoad: function(events) {
            if(!events) {
                return;
            }
            var list = $('#eventlist');
            list.html('');

            $.each(events, function(key, val) {
                $(document.createElement('li'))
                    .html('<a href="' + val.url + '">' + val.title + '</a>')
                    .appendTo(list);
            });
        },
        onAfterViewLoad: function(view) {
            $('.page-header h3').text(this.getTitle());
            $('.btn-group button').removeClass('active');
            $('button[data-calendar-view="' + view + '"]').addClass('active');
            $(".detallecal").css({'display':'block'});
        }
    };

    var calendar = $('#calendar').calendar(options);
    var fecha = new Date();
    //var mesactual=fecha.getMonth() +1;
    cargarPanelLateral();
    mesvisto=calendar.getMonth();

    $('.btn-group button[data-calendar-nav]').each(function() {
        var $this = $(this);
        $this.click(function() {
            calendar.navigate($this.data('calendar-nav'));
            cargarPanelLateral();
        });
    });

    $('.btn-group button[data-calendar-view]').each(function() {
        var $this = $(this);
        $this.click(function() {
            calendar.view($this.data('calendar-view'));
        });
    });

    $('#first_day').change(function(){
        var value = $(this).val();
        value = value.length ? parseInt(value) : null;
        calendar.setOptions({first_day: value});
        calendar.view();
    });
    $('#events-in-modal').change(function(){
        var val = $(this).is(':checked') ? $(this).val() : null;
        calendar.setOptions({modal: val});
    });
    $('#show_wbn').change(function(){
        var val = $(this).is(':checked') ? true : false;
        calendar.setOptions({display_week_numbers: val});
        calendar.view();
    });
    $('#show_wb').change(function(){
        var val = $(this).is(':checked') ? true : false;
        calendar.setOptions({weekbox: val});
        calendar.view();
    });
    $('#events-modal .modal-header, #events-modal .modal-footer').click(function(e){
        //e.preventDefault();
        //e.stopPropagation();
    });
    $(document).on('click','.cal-cell',function(){
        var fe=$(this).find('span').attr('data-cal-date').split('-');
        fecha=fe[2]+'-'+fe[1]+'-'+fe[0];
        $("#agregardia").load(site_url + "backend/agendas/ajax_dia_conf_global/"+fecha);
        $("#agregardia").modal();
    })
});
function cargarDiasFeriados(calendar){
    var url=$('#urlbase').val()+'/backend/agendas/diasFeriados';
    var arrdata=new Array();
    $.ajax({
        url: url,
        dataType: "json",
        async:false,
        success: function( data ) {
            if(data.code==200){
                var items=data.daysoff;
                var i=0;
                $.each(items, function(index, element) {
                    arrdata[element.date_dayoff]=element.name;
                    idfierados[i]=element.id;
                    i++;
                });
            }
        }
    });
    return arrdata;
}
function cargarPanelLateral(){
    moment.lang('es');
    $('#desccalendar').html('');
    var index=0;
    for (var k in feriados){
        if (feriados.hasOwnProperty(k)) {
            var fecha = new Date();
            var m=k.split('-');
            var ano = fecha.getFullYear();
            //var val=moment(m[1]+'/'+m[0]+'/'+ano).format("LL");
            var val=m[0]+'-'+m[1];
            //var dia=m[0];
            var desc=feriados[k];
            var impar='';
            if((index+1)%2==1){
                impar='impar';
            }
            var row='<div id="'+k+'-'+ano+'" data-id="'+idfierados[index]+'" data-fecha="'+val+'" class="rowcalendar js_row_calendar '+impar+' clearfix"><div class="labelc">'+val+'</div><div class="detallec">'+desc+'</div></div>';
            $('#desccalendar').append(row);
            index++;
        }
    }
    if(index==0){
        var row='<div class="rowcalendar js_row_calendar impar clearfix"><div class="labelc"></div><div class="detallec">No se encontraron registros</div></div>';
        $('#desccalendar').append(row);
    }
    $('.js_row_calendar').off('click');
    $('.js_row_calendar').on('click',function(){
        $('.rowactive').removeClass('rowactive');
        $(this).addClass('rowactive');
        var id=$(this).attr('id');
        $('#fechaaelim').val(id);
        $('#idelim').val($(this).attr('data-id'));
    });
}