<?php
require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');

class AccionEnviarVacation extends Accion {

    public function displayForm() {

		$display = '<label>Fecha Inicial</label>';
		$display.='<input type="text" name="extra[date_init]" value="' . (isset($this->extra->date_init) ? $this->extra->date_init : '') . '" />';
		$display.= '<label>Tipo Fecha Final</label>';
		$display.='<input type="text" name="extra[date_end]" value="' . (isset($this->extra->date_end) ? $this->extra->date_end : '') . '" />';
		$display.= '<label>Cantidad dias solicitados</label>';
		$display.='<input type="text" name="extra[cantidad]" value="' . (isset($this->extra->cantidad) ? $this->extra->cantidad : '') . '" />';
		$display.= '<label>Rut Trabajador</label>';
		$display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';	
        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
    }

    public function ejecutar(Etapa $etapa) {
		$regla=new Regla($this->extra->date_init);
		$date_init=$regla->getExpresionParaOutput($etapa->id);

		$regla=new Regla($this->extra->date_end);
		$date_end=$regla->getExpresionParaOutput($etapa->id);

		$regla=new Regla($this->extra->rut);
		$rut=$regla->getExpresionParaOutput($etapa->id);

		$regla=new Regla($this->extra->cantidad);
		$cantidad=$regla->getExpresionParaOutput($etapa->id);
		$cantidad= isset($cantidad)?$cantidad:'';
		$cantidad= ($cantidad!='')?$cantidad:1;        

		$tramite_id = $etapa->tramite_id;

		$json   = new stdClass();
		$json->fecha_inicial = $date_init;
		$json->fecha_final   = $date_end;
		$json->idTramite     = $tramite_id;
		$json->requestDays   = $cantidad;

		$json = json_encode($json);
		
		$url = urlapi."/users/".$rut."/vacationrequest";

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json" ));
        $result = curl_exec($ch);
        $httpCodeResponse = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
		
		//Save http code
        $http_code = Doctrine::getTable("DatoSeguimiento")->findOneByNombreAndEtapaId("http_code", $etapa->id);
        if($http_code){
            $http_code->valor = $httpCodeResponse;
            $http_code->save();
        }

		$result = json_decode($result); 
		if($result!=null && $result!=null){
		
			//PERIODS BEFORE
			$tr_before='';
			$size 	=count($result->vacationPeriodResponsesAvailableBefore);
			if($size>0){
				foreach($result->vacationPeriodResponsesAvailableBefore as $json){
	                $tr_before = $tr_before.'<tr><td>'.$json->dates.'</td><td>'.$json->basic.'</td><td>'.$json->progressive.'</td><td>'.$json->total.'</td></tr>';
				}
				$tr_before = $tr_before.'<tr ><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td></tr>';
				$tr_before = $tr_before.'<tr><td>TOTALES</td><td>'.$result->totalAvailableBefore[0].'</td><td>'.$result->totalAvailableBefore[1].'</td><td>'.$result->totalAvailableBefore[2].'</td></tr>';
			}
			else{	
				$tr_before = '<tr><td>-</td><td>-</td><td>-</td><td>-</td></tr>';
				$tr_before = $tr_before.'<tr><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td></tr>';
				$tr_before = $tr_before.'<tr><td>TOTALES</td><td></td><td></td><td></td></tr>';
			}
		
			$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("tabla_before", $etapa->id);	
			if($dato){
				$dato->valor = str_replace("trs_before",$tr_before, $dato->valor);
				$dato->save();
			}

			//PERIODS REQUEST
			$tr_used='';
	        $size=count($result->vacationPeriodResponsesUsed);
            if($size>0){
				foreach($result->vacationPeriodResponsesUsed as $json){
	                $tr_used = $tr_used.'<tr><td>'.$json->dates.'</td><td>'.$json->basic.'</td><td>'.$json->progressive.'</td><td>'.$json->date_init.'</td><td>'.$json->date_final.'</td><td>'.$json->total.'</td></tr>';
	            }
				$tr_used = $tr_used.'<tr><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td></tr>';
				$tr_used = $tr_used.'<tr><td>TOTALES</td><td>'.$result->totalAvailableUsed[0].'</td><td>'.$result->totalAvailableUsed[1].'</td><td></td><td></td><td>'.$result->totalAvailableUsed[2].'</td></tr>';
			}
			else{
                $tr_used = '<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>';
                $tr_used = $tr_used.'<tr><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td></tr>';
                $tr_used = $tr_used.'<tr><td>TOTALES</td><td></td><td></td><td></td><td></td><td></td></tr>';
        	}		
			
	 		$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("tabla_used", $etapa->id);
			if($dato){
				$dato->valor = str_replace("trs_used",$tr_used, $dato->valor);
				$dato->save();
			}

			//PERIODS AFTER
            $tr_after = '';
            $size = count($result->vacationPeriodResponsesAvailableAfter);
			if($size > 0){
				foreach($result->vacationPeriodResponsesAvailableAfter as $json){
                	$tr_after = $tr_after.'<tr><td>'.$json->dates.'</td><td>'.$json->basic.'</td><td>'.$json->progressive.'</td><td>'.$json->total.'</td></tr>';
            	}
	            $tr_after = $tr_after.'<tr ><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td></tr>';
	            $tr_after = $tr_after.'<tr><td>TOTALES</td><td>'.$result->totalAvailableAfter[0].'</td><td>'.$result->totalAvailableAfter[1].'</td><td>'.$result->totalAvailableAfter[2].'</td></tr>';
			}
			else{
				$tr_after = '<tr><td>-</td><td>-</td><td>-</td><td>-</td></tr>';
				$tr_after = $tr_after.'<tr><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td><td style="border-bottom: 1px solid #1a1a1a;"></td></tr>';
				$tr_after = $tr_after.'<tr><td>TOTALES</td><td></td><td></td><td></td></tr>';	
			}

			$dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("tabla_after", $etapa->id);
            if($dato){
                $dato->valor = str_replace("trs_after",$tr_after, $dato->valor);
                $dato->save();
            }
		} //End if rsult !=0
    }
}
