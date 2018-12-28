<?php

require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');
require_once(routecontrollers.'authorization.php');


class AccionGuardarRutAnnos extends Accion {

    public function displayForm() {

    	$display= '<label>Rut</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';
        $display.= '<label>Años</label>';
        $display.='<input type="text" name="extra[years]" value="' . (isset($this->extra->years) ? $this->extra->years : '') . '" />';

        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[rut]', 'Rut', 'required');
        $CI->form_validation->set_rules('extra[years]', 'Años', 'required');
    	
    }

    public function ejecutar(Etapa $etapa) {
        $regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->years);
        $years=$regla->getExpresionParaOutput($etapa->id);

        //Correct
        if ($years>=1 and $years <=10) {
            $years = $years;
        }else { //Incorrect
            $years=0;
        }        

        $oa = new Authorization();
        $token = $oa->getToken();
      
        $url = urlapi."users/".$rut."/validatedYears/".$years;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json", "Authorization: Bearer ".$token));
        $result = curl_exec($ch);
        $httpCodeResponse = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);
        $body = substr($body,1,-1);


        // set @@body response
        $dato = Doctrine::getTable("DatoSeguimiento")->findOneByNombreAndEtapaId("body_response", $etapa->id);
        if($dato){
            if ($httpCodeResponse == 200) {
                $dato->valor = $body;
            }            
            $dato->save();
        }
        
        //set @@http_code
        $dato2 = Doctrine::getTable("DatoSeguimiento")->findOneByNombreAndEtapaId("http_code", $etapa->id);
        if($dato2){
            $dato2->valor = ($httpCodeResponse);
            $dato2->save();
        }

    } 

}

?>

