<?php
require_once(FCPATH."procesos.php");
require_once('accion.php');
require_once('ChromePhp.php');

class AccionGuardarTrabajador extends Accion {

    public function displayForm() {


    	$display= '<label>Rut</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';
        $display.= '<label>Nombres</label>';
        $display.='<input type="text" name="extra[nombres]" value="' . (isset($this->extra->nombres) ? $this->extra->nombres : '') . '" />';
        $display.= '<label>Apellido Paterno</label>';
        $display.='<input type="text" name="extra[apellidoPaterno]" value="' . (isset($this->extra->apellidoPaterno) ? $this->extra->apellidoPaterno : '') . '" />';
        $display.= '<label>Apellido Materno</label>';
        $display.='<input type="text" name="extra[apellidoMaterno]" value="' . (isset($this->extra->apellidoMaterno) ? $this->extra->apellidoMaterno : '') . '" />';
    	$display.= '<label>Email Personal</label>';
        $display.='<input type="text" name="extra[emailPersonal]" value="' . (isset($this->extra->emailPersonal) ? $this->extra->emailPersonal : '') . '" />';	
    	$display.= '<label>Genero</label>';
        $display.='<input type="text" name="extra[genero]" value="' . (isset($this->extra->genero) ? $this->extra->genero : '') . '" />';
    	$display.= '<label>Fecha Nacimiento</label>';
        $display.='<input type="text" name="extra[fNacimiento]" value="' . (isset($this->extra->fNacimiento) ? $this->extra->fNacimiento : '') . '" />';
    	$display.= '<label>Nacionalidad</label>';
        $display.='<input type="text" name="extra[nacionalidad]" value="' . (isset($this->extra->nacionalidad) ? $this->extra->nacionalidad : '') . '" />';
    	$display.= '<label>Estado Civil</label>';
        $display.='<input type="text" name="extra[estadoCivil]" value="' . (isset($this->extra->estadoCivil) ? $this->extra->estadoCivil : '') . '" />';
    	$display.= '<label>Celular</label>';
        $display.='<input type="text" name="extra[celular]" value="' . (isset($this->extra->celular) ? $this->extra->celular : '') . '" />';

        //Datos laborales

        $display.= '<label>Cargo</label>';
        $display.='<input type="text" name="extra[cargo]" value="' . (isset($this->extra->cargo) ? $this->extra->cargo : '') . '" />';
        $display.= '<label>Email IST</label>';
        $display.='<input type="text" name="extra[emailIST]" value="' . (isset($this->extra->emailIST) ? $this->extra->emailIST : '') . '" />';
        $display.= '<label>Fecha Contrato</label>';
        $display.='<input type="text" name="extra[fContrato]" value="' . (isset($this->extra->fContrato) ? $this->extra->fContrato : '') . '" />';
        $display.= '<label>Empresa</label>';
        $display.='<input type="text" name="extra[empresa]" value="' . (isset($this->extra->empresa) ? $this->extra->empresa : '') . '" />';
	$display.= '<label>Ubicación</label>';
        $display.='<input type="text" name="extra[ubicacion]" value="' . (isset($this->extra->ubicacion) ? $this->extra->ubicacion : '') . '" />';
        $display.= '<label>Centro de Costo</label>';
        $display.='<input type="text" name="extra[centroCosto]" value="' . (isset($this->extra->centroCosto) ? $this->extra->centroCosto : '') . '" />';
	$display.= '<label>Jornada</label>';
        $display.='<input type="text" name="extra[jornada]" value="' . (isset($this->extra->jornada) ? $this->extra->jornada : '') . '" />';
        $display.= '<label>Gerencia</label>';
        $display.='<input type="text" name="extra[gerencia]" value="' . (isset($this->extra->gerencia) ? $this->extra->gerencia : '') . '" />';
        $display.= '<label>Celular IST</label>';
        $display.='<input type="text" name="extra[celularIST]" value="' . (isset($this->extra->celularIST) ? $this->extra->celularIST : '') . '" />';
        $display.= '<label>Anexo</label>';
        $display.='<input type="text" name="extra[anexo]" value="' . (isset($this->extra->anexo) ? $this->extra->anexo : '') . '" />';
        $display.= '<label>Código de Área</label>';
        $display.='<input type="text" name="extra[codigoArea]" value="' . (isset($this->extra->codigoArea) ? $this->extra->codigoArea : '') . '" />';
        $display.= '<label>Tipo de Contrato</label>';
        $display.='<input type="text" name="extra[tipoContrato]" value="' . (isset($this->extra->tipoContrato) ? $this->extra->tipoContrato : '') . '" />';
        $display.= '<label>Fecha Término de Contrato</label>';
        $display.='<input type="text" name="extra[fTerminoContrato]" value="' . (isset($this->extra->fTerminoContrato) ? $this->extra->tipoContrato : '') . '" />';

        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[nombres]', 'Nombres', 'required');
        $CI->form_validation->set_rules('extra[apellidoPaterno]', 'Apellido Paterno', 'required');
    	$CI->form_validation->set_rules('extra[apellidoMaterno]', 'Apellido Materno', 'required');
    	$CI->form_validation->set_rules('extra[rut]', 'Rut', 'required');
    	$CI->form_validation->set_rules('extra[emailPersonal]', 'Email Personal', 'required');
    	$CI->form_validation->set_rules('extra[genero]', 'Genero', 'required');
    	$CI->form_validation->set_rules('extra[fNacimiento]', 'Fecha Nacimiento', 'required');
     	$CI->form_validation->set_rules('extra[nacionalidad]', 'Nacionalidad', 'required');
    	$CI->form_validation->set_rules('extra[estadoCivil]', 'Estado Civil', 'required');
    	$CI->form_validation->set_rules('extra[celular]', 'Celular', 'required');

        //Datos Laborales
        $CI->form_validation->set_rules('extra[cargo]', 'Cargo', 'required');
        $CI->form_validation->set_rules('extra[emailIST]', 'Email IST', 'required');
        $CI->form_validation->set_rules('extra[fContrato]','Fecha Contrato', 'required');
        $CI->form_validation->set_rules('extra[ubicacion]','Ubicación', 'required');
        $CI->form_validation->set_rules('extra[centroCosto]','Centro de Costo', 'required');
        $CI->form_validation->set_rules('extra[gerencia]','Gerencia', 'required');
        $CI->form_validation->set_rules('extra[celularIST]','Celular IST', 'required');
        $CI->form_validation->set_rules('extra[anexo]','Anexo', 'required');
        $CI->form_validation->set_rules('extra[codigoArea]','Código de Area', 'required');
        $CI->form_validation->set_rules('extra[tipoContrato]','Tipo de Contrato', 'required');
        $CI->form_validation->set_rules('extra[fTerminoContrato]','Fecha Término de Contrato', 'required');
    }

    public function ejecutar(Etapa $etapa) {
        $regla=new Regla($this->extra->nombres);
        $nombres=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->apellidoPaterno);
        $lastNameP=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->apellidoMaterno);
        $lastNameM=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->rut);
        $rut=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->emailPersonal);
        $emailPersonal=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->genero);
        $gender=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->fNacimiento);
        $birthday=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->nacionalidad);
        $nationality=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->estadoCivil);
        $civilStatus=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->celular);
        $cellPhone=$regla->getExpresionParaOutput($etapa->id);

        $aux = $lastNameP.' ';
        $aux=$aux.$lastNameM;

        //Datos Laborales
        $regla=new Regla($this->extra->cargo);
        $position=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->emailIST);
        $emailIST=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->fContrato);
        $contract_date=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->ubicacion);
        $location=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->centroCosto);
        $centerCost=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->jornada);
        $jornada=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->gerencia);
        $management=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->celularIST);
        $cellPhoneIST=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->anexo);
        $annexPhone=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->codigoArea);
        $areaCode=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->tipoContrato);
        $contract=$regla->getExpresionParaOutput($etapa->id);

        $regla=new Regla($this->extra->fTerminoContrato);
        $contract_end_date=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->empresa);
        $empresa=$regla->getExpresionParaOutput($etapa->id);
        
        // create json principal and 2 'sub-json'
        $json = new stdClass();
        $jsonPD = new stdClass();
        $jsonCD = new stdClass();

        // json with personal data
        $jsonPD ->email = $emailPersonal;
        $jsonPD->nationality = (string)$nationality;
        $jsonPD->civilStatus = intval($civilStatus);
        $jsonPD->phone = intval($cellPhone);

        // json with contractual data
        $jsonCD->positionCode = explode(" - ",$position)[1];//split(" - ",$position)[1];
        $jsonCD->init_date = $contract_date;
        $jsonCD->end_date = $contract_end_date;
	$jsonCD->workdayCode = $jornada;
	$jsonCD->companyCode = $empresa;	

        // Principal Json 
        $json->rut = $rut ;
        $json->name = $nombres;
        $json->gender = $gender;
        $json->lastName = $aux;
        $json->birth_day = $birthday;
        $json->email = $emailIST;
        $json->contract_date = $contract_date;
        $json->management = $management;
        $json->locationCode = explode(" - ",$location)[1];//(string)split(" - ",$location)[1] ;  
        $json->costCenterCode  = explode(" - ",$centerCost)[1];//(string)split(" - ",$centerCost)[1]; 
        $json->contractType = intval( $contract );
        $json->personalDataDTO = $jsonPD;
        $json->contractualDataDTO = $jsonCD;
        
        $json->annexPhone = intval($annexPhone) ;
        $json->areaCode = intval($areaCode);
        $json->phone = intval($cellPhoneIST);    
        $json = json_encode($json);
        $json = $json;

        
        $url = urlapi."users";	

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json"));
        $result = curl_exec($ch);
        $httpCodeResponse = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // set @@http_code = response http code from API
        $dato = Doctrine::getTable("DatoSeguimiento")->findOneByNombreAndEtapaId("http_code", $etapa->id);
        if($dato){
            $dato->valor = $httpCodeResponse;
            $dato->save();
        }

        $httpCreated = 201;
        //If response from API is different 201, save json in @@error_json
        if($httpCodeResponse != $httpCreated){
            $json_error = Doctrine::getTable("DatoSeguimiento")->findOneByNombreAndEtapaId("error_json", $etapa->id);
            if($json_error){
                $json_error->valor =  $json;
                $json_error->save();
            }
        }

    }

}
