function doSearchEnter(e) {
    var key = e.keyCode || e.which;
    if (key === 13) {
        return true;
    } else {
        return false;
    }
}

function vacio(q) {
    for (i = 0; i < q.length; i++) {
        if (q.charAt(i) !== " ") {
            return true;
        }
    }
    return false;
}

function validarDatos(event, dat_origen, dat_destino) {
    if (doSearchEnter(event) === true) {
        if (!vacio($(dat_origen).val())) {
            $(dat_origen).focus();
        } else {
            $(dat_destino).focus();
        }

    }
}

$('.form_date').datetimepicker({
    language: 'es',
    autoclose: 1,    
    startView: 'decade',
    orientation: "left",
    minViewMode: 2,
    minView: 4    
});

$(document).ready(function () {       
    $('#nrotramite').numeric();    

    $('#nrotramite').on("keypress", function (e) {
        if (e.keyCode == 13) {
            if (!vacio($('#nrotramite').val())) {
                $('#nrotramite').focus();
            } else {
                var inputs = $(this).parents("form").eq(0).find(":input");
                var idx = inputs.index(this);
                if (idx == inputs.length - 1) {
                    inputs[0].select();
                } else {
                    inputs[idx + 1].focus();
                    inputs[idx + 1].select();
                }
            }
            return false;
        }
    });
});


