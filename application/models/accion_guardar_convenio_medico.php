<?php
require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');
require_once(routecontrollers.'authorization.php');

class AccionGuardarConvenioMedico extends Accion {


    public function displayForm() {

        $display= '<label>Rut Trabajador</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';
        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[rut]', 'rut', 'required');
    }

    public function ejecutar(Etapa $etapa) {

        $regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);

        $json = ""; //new stdClass();
//	$url = "http://private-120a8-apisimpleist.apiary-mock.com/user/".$rut."/medicalbenefit";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ));
        curl_exec($ch);
        curl_close($ch);

     }
}
