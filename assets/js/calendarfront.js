window.feriados = new Array();
window.mesvisto = '';
window.calendar;
window.hiddenbutton = false;

$(function() {
  $("#tabs").tabs();
  var idagenda = $('#txtidagenda').val();
  ignorar_festivo(idagenda);
  var url = $('#urlbase').val() + 'agenda/disponibilidad/' + idagenda;
  feriados = cargarDiasFeriados();
  var options = {
    events_source: url,
    view: 'month',
    tmpl_path: $('#urlbase').val() + '/assets/calendar/tmpls_funcionario/',
    tmpl_cache: false,
    language: 'es-CO',
    width: 436,
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
    merge_holidays: true,
    holidays: feriados,
    onAfterEventsLoad: function(events) {

      $('.navtime').each(function () {
        var timeserver = $(this).attr('data-time');
        $(this).text(getTimeBrowser(timeserver));
      });

      if (!events) {
        return;
      }
      var list = $('#eventlist');
      list.html('');
      $.each(events, function(key, val) {
        $(document.createElement('li')).html('<a href="' + val.url + '">' + val.title + '</a>').appendTo(list);
      });
    },
    onAfterViewLoad: function(view) {
      $('.page-header h3').text(this.getTitle());
      $('.btn-group button').removeClass('active');
      $('button[data-calendar-view="' + view + '"]').addClass('active');
      $('.cal-event-list').find('a').css({'text-decoration' : 'none'});

      $('.cal-cell').dblclick(function() {
        eventDaysCalendar();
      });

      $('.cal-cell').click(function() {
        var ignore = 0;
        if (typeof($('#validarferiado')) !== "undefined") {
          ignore = $('#validarferiado').val();
        }
        if ($(this).children().hasClass('cal-month-day') && (!$(this).children().hasClass('dia-festivo') || ignore == 1)) {
          eventDaysCalendar();
        }
      });

      $('.cal-month-box .event-warning').parent().parent().find('span').addClass('styhaycita');
      $('.cal-month-box .event-warning').parent().parent().addClass('sweventhaycita');

      var ignore = 0;

      if (typeof($('#validarferiado')) !== "undefined") {
        ignore = $('#validarferiado').val();
        if (ignore == 1) {
          $('.event-warning').parent().parent().addClass('festivodisp');
          $('.event-warning').parent().parent().find('span').addClass('styhaycitaspan');
        }
      }

      var tmp = new Date();
      var $datehoy = new Date(tmp.getFullYear(), tmp.getMonth(), tmp.getDate(), 0, 0, 0, 0);

      $.each($('span[data-cal-date]'), function (index, element) {
        var adateinview = $(this).attr('data-cal-date').split('-');
        var $datecal = new Date(adateinview[0], adateinview[1] - 1, adateinview[2], 0, 0, 0, 0);
        if ($datecal.getTime() < $datehoy.getTime()) {
          $(this).addClass('diapasado');
        }
      });
      $('.eventradocup').parent().parent().find('span').addClass('diaocupado');
    }
  };

  calendar = $('#calendar').calendar(options);

  $(document).on('click', '.pull-right', function() {
    eventDaysCalendar();
  });

  var fecha = new Date();
  var mesactual = fecha.getMonth() + 1;
  mesvisto = calendar.getMonth();

  $('.btn-group button[data-calendar-nav]').each(function() {
    var $this = $(this);
    $this.click(function() {
      hiddenbutton = false;
      calendar.navigate($this.data('calendar-nav'));
      var ignore = 0;
      if (typeof($('#validarferiado')) !== "undefined") {
          ignore = $('#validarferiado').val();
      }
      var index = 0;
      var sw = false;
      var tmp = calendar.getDateSelect().split("/");
      var hoy = new Date(tmp[2], tmp[1] - 1, tmp[0], 0, 0, 0, 0);
      for (var k in feriados) {
        if (feriados.hasOwnProperty(k)) {
          var m = k.split('-');
          var hol = new Date(m[2], m[1] - 1, m[0], 0, 0, 0, 0);
          if (hoy.getTime() == hol.getTime()) {
            if (ignore == 0) {
              sw = true;
            }
          }
        }
      }
      if (sw) {
        // $("#cont_cal").css({'display':'none'});
        $('#cont_cal').html('<div class="clearfix row-events-cal">No existe disponibilidad de citas</div>');
        hiddenbutton = true;
      }
    });
  });

  $('.btn-group button[data-calendar-view]').each(function() {
    var $this = $(this);
    $this.click(function() {
      calendar.view($this.data('calendar-view'));
      eventDaysCalendar();
    });
  });

  $('#first_day').change(function() {
    var value = $(this).val();
    value = value.length ? parseInt(value) : null;
    calendar.setOptions({first_day : value});
    calendar.view();
  });

  $('#events-in-modal').change(function() {
    var val = $(this).is(':checked') ? $(this).val() : null;
    calendar.setOptions({modal : val});
  });

  $('#show_wbn').change(function() {
    var val = $(this).is(':checked') ? true : false;
    calendar.setOptions({display_week_numbers : val});
    calendar.view();
  });

  $('#show_wb').change(function() {
    var val = $(this).is(':checked') ? true : false;
    calendar.setOptions({weekbox : val});
    calendar.view();
  });

  $('#events-modal .modal-header, #events-modal .modal-footer').click(function(e) {
    // e.preventDefault();
    // e.stopPropagation();
  });

  $(document).on('click', '.cal-cell', function() {
    var fe = $(this).find('span').attr('data-cal-date').split('-');
    fecha = fe[2] + '-' + fe[1] + '-' + fe[0];
    $("#agregardia").load(site_url + "backend/agendas/ajax_dia_conf_global/" + fecha);
    $("#agregardia").modal();
  });

  $('#tabs').css({'display' : 'block'});
  $('#ajaxLoaderfuncini').remove();
});

function reload_dia() {
  calendar.reload_day();
  eventDaysCalendar();
}

function cargarDiasFeriados(calendar) {
  var url = $('#urlbase').val() + 'agenda/diasFeriados';
  var arrdata = new Array();
  $.ajax({
    url: url,
    dataType: "json",
    async: false,
    success: function(data) {
      if (data.code == 200) {
        var items = data.daysoff;
        var i = 0;
        $.each(items, function(index, element) {
          arrdata[element.date_dayoff] = element.name;
        });
      }
    }
  });
  return arrdata;
}

function cargarPanelLateral(mesactual) {
  moment.lang('es');
  $('#desccalendar').html('');
  var index = 0;
  for (var k in feriados) {
    if (feriados.hasOwnProperty(k)) {
      var fecha = new Date();
      if (mesactual <= 0) {
        mesactual = '0' + mesactual;
      }
      var m = k.split('-');
      if (mesactual == m[1]) {
        var ano = fecha.getFullYear();
        var val = moment(m[1] + '/' + m[0] + '/' + ano).format("LL");
        var dia = m[0];
        var desc = feriados[k];
        var impar = '';
        if ((index + 1) % 2 == 1) {
          impar = 'impar';
        }
        var row = '<div id="' + k + '-' + ano + '" class="rowcalendar js_row_calendar ' + impar + ' clearfix"><div class="labelc">' 
          + val + '</div><div class="detallec">' + desc + '</div></div>';

        $('#desccalendar').append(row);
        index++;
      }
    }
  }

  $('.js_row_calendar').off('click');

  $('.js_row_calendar').on('click',function() {
    $('.rowactive').removeClass('rowactive');
    $(this).addClass('rowactive');
    var id = $(this).attr('id');
    $('#fechaaelim').val(id);
  });
}

function eventDaysCalendar() {
  var tmp = calendar.getDateSelect().split('/');
  var d = new Date(tmp[2],tmp[1] - 1, tmp[0], 1, 0, 0, 0);
  var select = d.getDate() + '/' + d.getMonth() + '/' + d.getFullYear();
  var $html = '';
  var i = 0;
  var concurrencia = 1;
  var swhaycita = false;
  var iconrow = '';
  var swpuedeblock = true;
  var toltips = '';
  var desctoltips = '';
  var timeacutal = new Date();
  $("#frmdataranghorbloq").html('');
  var arrbloq = new Array();
  var arrrese = new Array();

  $.each(calendar.getEventos(), function(index, element) {
    var cita = new Date(element.start);
    var fincita = new Date(element.end);
    var diacita = cita.getDate() + '/' + cita.getMonth() + '/' + cita.getFullYear();

    var dataEvent = fecha_hora(cita);
    var dataEventFin = fecha_hora(fincita);
    var min = cita.getMinutes();
    if (min <= 9) {
      min = '0' + min;
    }
    var hora = cita.getHours() + ':' + min;

    if (diacita == select) {
      swhaycita = true;
      if (i == 0) {
        if (cita > timeacutal) {
          iconrow = '<div><span onclick="block(' + element.start + ',' + element.end + ');" class="glyphicon glyphicon glyphicon-ban-circle cursor" aria-hidden="true"></span></div>';
          $html = '<div><div class="clearfix js-row-day">';
          if (element.estado == 'D') {
            if (typeof($('#ref' + element.start).val()) === "undefined") {
              var objrean = '<input type="hidden" name="horainicio[]" id="ref' + element.start + '" value="' + element.start 
                + '" ><input type="hidden" name="horafinal[]" id="reff' + element.start + '" value="' + element.end + '" >';
              $("#frmdataranghorbloq").append(objrean);
            }
          }
        }
      } else {
        if (i == element.concurrencia) {
          concurrencia = element.concurrencia;
          if (cita > timeacutal) {
            $html = $html + iconrow + '</div><hr class="sep-row" /><div class="clearfix js-row-day">';
            iconrow = '<div><span onclick="block(' + element.start + ',' + element.end 
              + ');" class="glyphicon glyphicon glyphicon-ban-circle cursor" aria-hidden="true"></span></div>';
            if (element.estado == 'D') {
              if (typeof($('#ref' + element.start).val()) === "undefined") {
                var objrean = '<input type="hidden" name="horainicio[]" id="ref' + element.start + '" value="' + element.start 
                  + '" ><input type="hidden" name="horafinal[]" id="reff' + element.start + '" value="' + element.end + '" >';
                $("#frmdataranghorbloq").append(objrean);
              }
            }
          }
          i = 0;
        }
      }
      $desc = '';
      toltips = '';
      desctoltips = '';
      var cssdesc = '';
      if (element.estado == 'D') {
        $desc = 'Disponible';
        cssdesc = 'evdisp';
      } else {
        if (element.estado == 'R') {
          cssdesc = 'evreserv evbloq';
          $desc = 'Reservado';
          toltips = 'data-tooltips="tooltip"';
          desctoltips = '' + element.id + ' ' + element.correo;
          swpuedeblock = true;
          iconrow = '';
          arrrese.push(element.start);
        } else {
          if (element.estado == 'B') {
            cssdesc = 'evbloq';
            $desc = 'Bloqueado';
            swpuedeblock = true;
            if (cita > timeacutal) {
              iconrow = '<div><span onclick="unblock(' + element.block_id + ');" class="glyphicon glyphicon glyphicon-remove-circle cursor" aria-hidden="true"></span></div>';
            }
            arrbloq.push(element.start);
          }
        }
      }
      var zonahora = '';
      if (i == 0) {
        zonahora = '<div class="hora">' + hora + '</div>';
      }
      var $div = '<div class="clearfix row-events-cal">' + zonahora + '<div><div data-cita="' + element.cita + '" data-fecha="' + element.fecha 
        + '" data-hora="' + element.hora + '" data-tramite="' + element.tramite + '" data-solicitante="' + element.id + '" data-correo="' 
        + element.correo + '" class="descevent ' + cssdesc + '" data-event="' + dataEvent + '" ' + toltips + ' title="' + desctoltips 
        + '" data-event-fin="' + dataEventFin + '"  >' + $desc + '</div></div></div>';

      if (cita > timeacutal) {
        $html = $html + $div;
      }
    } else {
      i = -1;
    }
    i++;
  });

  $.each(arrrese, function(index, value) {
    if (typeof($('#ref' + value).val()) !== "undefined") {
      $('#ref' + value).remove();
      $('#reff' + value).remove();
    }
  });

  $.each(arrbloq, function(index, value) {
    if (typeof($('#ref'+value).val()) !== "undefined") {
      $('#ref' + value).remove();
      $('#reff' + value).remove();
    }
  });

  $html = $html + iconrow + '<hr class="sep-row"/></div></div>';

  if (swhaycita) {
    $('#cont_cal').html($html);
    if (hiddenbutton) {
      // $("#btnbloqueargeneral").css({'display':'none'});
      $('#cont_cal').html('<div class="clearfix row-events-cal">No existe disponibilidad de citas</div>');    
    } else {
      $("#btnbloqueargeneral").css({'display' : 'block'});
    }
  } else {
    $("#btnbloqueargeneral").css({'display' : 'none'});
    $('#cont_cal').html('<div class="clearfix row-events-cal">No existe disponibilidad de citas</div>');
  }

  $('[data-tooltips="tooltip"]').tooltip();

  var wcol = concurrencia * 200;
  var wsep = concurrencia * 153;

  $('.js-row-day').css({'width' : wcol + 'px'});
  $('.sep-row').css({'width' : wsep + 'px'});

  $('.evdisp').click(function() {
    var object = '<?= $idobject ?>';
    var tmp = $(this).attr('data-event').split(' ');
    var tmpf = tmp[0].split('/');
    var fecha = tmpf[2] + '-' + tmpf[1] + '-' + tmpf[0];

    var tmp2 = $(this).attr('data-event-fin').split(' ');
    var tmpf2 = tmp2[0].split('/');
    var fecha2 = tmpf2[2] + '-' + tmpf2[1] + '-' + tmpf2[0];

    $("#modalconfirmar").load(site_url + "etapas/ajax_confirmar_agregar_dia?idagenda=<?= $idagenda ?>&fecha=" 
      + fecha + "&hora=" + tmp[1] + "&obj=" + object + "&fechaf=" + fecha2 + "&horaf=" + tmp2[1] + "&idcita=<?= $idcita ?>");
    $("#modalconfirmar").modal();
  });

  $('.evreserv').off('click');

  $('.evreserv').on('click',function() {
    var fecha = $(this).attr('data-fecha');
    var hora = $(this).attr('data-hora');
    var tramite = $(this).attr('data-tramite');
    var solicitante = $(this).attr('data-solicitante');
    var correo = $(this).attr('data-correo');
    var cita = $(this).attr('data-cita');

    $('#ver_solicitante').val(solicitante);
    $('#ver_dia').val(fecha);
    $('#ver_hora').val(hora);
    $('#ver_tramite').val(tramite);
    $('#ver_email').val(correo);
    var param = $('#formvercita').serialize();
    $("#modalcancelar").load(site_url + "backend/agendasusuario/ajax_modal_ver_cita_funcionario/" + cita + '?' + param);
    $("#modalcancelar").modal();
  });

  $('.btnbloqueargeneral').off('click');

  $('.btnbloqueargeneral').on('click',function() {
    var urlbase = $('#urlbase').val();
    var dia = $("#dvbloquear").val();
    $("#fechainicio").val(dia + ' 00:00');
    $("#fechafinal").val(dia + ' 23:59');
    $('#agendabloqgen').val($('#cmbagenda').val());
    var param = $("#frmbloqgener").serialize();
    $("#modalcancelar").load(urlbase + 'agenda/ajax_confirmar_agregar_bloqueo_dia_completo?' + param);
    $("#modalcancelar").modal();
  });
}

function block(start,end) {
  var id = $('#cmbagenda').val();
  $("#modalcancelar").load($('#urlbase').val() + 'agenda/bloqueo?start=' + start + '&end=' + end + '&id=' + id);
  $("#modalcancelar").modal();
}

function cancelar_cita(idcita) {
  $("#modalcancelar").load($('#urlbase').val() + 'agenda/ajax_cancelar_cita_funcionario/' + idcita);
  $("#modalcancelar").modal();
}

function confirmar_cancelar_cita(idcita) {
  $('#cancelar_cita_' + idcita).prop("disabled", true);
  $('.js_cerrar_vcancelar').prop("disabled", true);
  var motivo = jQuery.trim($('#txtmotivo').val());
  var url = $('#urlbase').val() + 'agenda/ajax_cancelarCita/' + idcita;
  if (motivo != '') {
    var form = $('#modalcancelar');
    $(form).append("<div class='ajaxLoaderfunc'>Cargando</div>");
    var ajaxLoader = $(form).find(".ajaxLoaderfunc");

    $(ajaxLoader).css({
      left: ($(form).width() / 2 - $(ajaxLoader).width() / 2) + "px", 
      top: ($(form).height() / 2 - $(ajaxLoader).height() / 2) + "px"
    });

    var param = $('#frmcanccita').serialize();

    $.ajax({
      url: url,
      dataType: "json",
      data: {
        motivo: motivo
      },
      success: function( data ) {
        if (data.code == 200) {
          $("#modalcancelar").modal('toggle');
          calendar.view();
          eventDaysCalendar();
          $(ajaxLoader).remove();
        } else {
          $(ajaxLoader).remove();
          $('.valcancelcita').html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>' + data.message + '.</div>');
          $('#cancelar_cita_' + idcita).prop("disabled", false);
          $('.js_cerrar_vcancelar').prop("disabled", false);
        }
      }
    });
  } else {
    $('.valcancelcita').html('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>Debe escribir un motivo.</div>');
    $('#cancelar_cita_' + idcita).prop("disabled", false);
    $('.js_cerrar_vcancelar').prop("disabled", false);
  }
}

function fecha_hora(ObjectDate) {
  var min = ObjectDate.getMinutes();
  if (min <= 9) {
    min = '0' + min;
  }
  var hora = ObjectDate.getHours() + ':' + min;

  var tmp = '';
  var tmpdia = ObjectDate.getMonth() + 1;
  if (tmpdia <= 9) {
    tmp = ObjectDate.getDate() + '/0' + tmpdia + '/' + ObjectDate.getFullYear();
  } else {
    tmp = ObjectDate.getDate() + '/' + tmpdia + '/' + ObjectDate.getFullYear();
  }
  var dataEvent = tmp + ' ' + hora;
  return dataEvent;
}

function unblock(idblock) {
  $("#modalcancelar").load($('#urlbase').val() + 'agenda/desbloqueo?id=' + idblock);
  $("#modalcancelar").modal();
}

function getTimeBrowser(time) {
  var localDate = moment(time).toDate();
  var min = ('0' + localDate.getMinutes()).slice(-2);

  var localHour = localDate.getHours() + ':' + min;

  return localHour;
}

function ignorar_festivo(idagenda) {
  var url = $('#urlbase').val() + 'agenda/ajax_obtener_datos_agenda';
  $.ajax({
    url: url,
    async: false,
    dataType: "json",
    data: {
      id: idagenda
    },
    success: function(data) {
      if (data.code == 200) {
        var ignorefestivo = data.calendar.ignore_non_working_days;
        $('#validarferiado').val(ignorefestivo);
      }
    }
  });
}
