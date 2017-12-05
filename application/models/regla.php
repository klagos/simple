<?php

class Regla {

    private $regla;

    function __construct($regla) {
        $this->regla = $regla;
    }
    
    

    //Evalua la regla de acuerdo a los datos capturados en el tramite tramite_id
    public function evaluar($etapa_id,$ev=FALSE) {
        if (!$this->regla)
            return TRUE;

        $new_regla = $this->getExpresionParaEvaluar($etapa_id,$ev);
        $new_regla = 'return ' . $new_regla . ';';
        $CI = & get_instance();
        $CI->load->library('SaferEval');
        $resultado = FALSE;
        if (!$errores = $CI->safereval->checkScript($new_regla, FALSE))
            $resultado = @eval($new_regla);
        
        return $resultado;
    }
    
    //Obtiene la expresion con los reemplazos de variables ya hechos de acuerdo a los datos capturados en el tramite tramite_id.
    //Esta expresion es la que se evalua finalmente en la regla
    public function getExpresionParaEvaluar($etapa_id,$ev=FALSE){
        $new_regla=$this->regla;
        $new_regla=preg_replace_callback('/@@(\w+)((->\w+|\[\w+\])*)/', function($match) use ($etapa_id,$ev) {
                    $nombre_dato = $match[1];
                    $accesor=isset($match[2])?$match[2]:'';
                    
                    $dato = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($nombre_dato,$etapa_id);                    
                    if ($dato) {
                        $dato_almacenado=eval('$x=json_decode(\''.json_encode($dato->valor,JSON_HEX_APOS).'\'); return $x'.$accesor.';');
                        $valor_dato='json_decode(\''.json_encode($dato_almacenado).'\')';
                        if($ev){
                            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
                            $campo = Doctrine_Query::create()
                                                    ->select('c.tipo')
                                                    ->from('Campo c, c.Formulario f, f.Proceso p')
                                                    ->where('c.nombre=? AND p.activo=1 AND p.id=?',array($nombre_dato,$etapa->Tarea->Proceso->id))
                                                    ->execute();
                            
                            if($campo[0]->tipo=='documento' or $campo[0]->tipo=='file'){
                                $files =Doctrine::getTable('File')->findByTramiteIdAndFilename($etapa->Tramite->id,$dato->valor);
                                if(count($files) > 0){
                                    foreach($files as $f){
                                        $ruta = $f->tipo=='documento'? 'uploads/documentos/' : 'uploads/datos/';
                                        $valor_dato = "'".$ruta.$dato->valor."'";
                                    }
                                }
                            }
                        }
                    }
                    else {
                        //No reemplazamos el dato
                        $valor_dato = 'json_decode(\''.json_encode(null).'\')';
                    }

                    return $valor_dato;
                }, $new_regla);
                
         //Variables globales
         $new_regla=preg_replace_callback('/@#(\w+)/', function($match) use ($etapa_id) {
                    $nombre_dato = $match[1];
                    
                    $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
                    $dato = Doctrine::getTable('DatoSeguimiento')->findGlobalByNombreAndProceso($nombre_dato,$etapa->Tramite->id);
                    $valor_dato=var_export($dato,true);

                    return $valor_dato;
                }, $new_regla);
                
         $new_regla=preg_replace_callback('/@!(\w+)/', function($match) use ($etapa_id) {
                    $nombre_dato = $match[1];
                    
                    $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
                    $usuario=$etapa->Usuario;
                    
                    if($nombre_dato=='rut')
                        return "'".$usuario->rut."'";
                    else if($nombre_dato=='nombre')         //Deprecated
                        return "'".$usuario->nombres."'";
                    else if($nombre_dato=='apellidos')      //Deprecated
                        return "'".$usuario->apellido_paterno.' '.$usuario->apellido_materno."'";
                    else if($nombre_dato=='nombres')
                        return "'".$usuario->nombres."'";
                    else if($nombre_dato=='apellido_paterno')
                        return "'".$usuario->apellido_paterno."'";
                    else if($nombre_dato=='apellido_materno')
                        return "'".$usuario->apellido_materno."'";
                    else if($nombre_dato=='email')
                        return "'".$usuario->email."'";
                    else if($nombre_dato=='tramite_id'){
                        return "'".Doctrine::getTable('Etapa')->find($etapa_id)->tramite_id."'";
                    }
                    else if ($nombre_dato=='tramite_proc_cont'){
                    	return Doctrine::getTable('Tramite')->find(Doctrine::getTable('Etapa')->find($etapa_id)->tramite_id)->tramite_proc_cont;
                    }
                    
                }, $new_regla);
                
         //Si quedaron variables sin reemplazar, la evaluacion deberia ser siempre falsa.
         if(preg_match('/@@\w+/', $new_regla))
            return false;
                   
         return $new_regla;
    }
    
    //Obtiene la expresion con los reemplazos de variables ya hechos de acuerdo a los datos capturados en el tramite tramite_id.
    //Esta es una representacion con las variables reemplazadas. No es una expresion evaluable. (Los arrays y strings no estan definidos como tal)
    public function getExpresionParaOutput($etapa_id, $evaluar=false){
        //print_r( stdClass::__set_state(array( 'region' => 'Antofagasta', 'comuna' => 'San Pedro de Atacama' )));
        //exit;
        $new_regla=$this->regla;     
        $new_regla=preg_replace_callback('/@@(\w+)((->\w+|\[\w+\])*)/', function($match) use ($etapa_id,$evaluar) {
                    $nombre_dato = $match[1];
                    $accesor=isset($match[2])?$match[2]:'';
                    
                    $dato = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($nombre_dato,$etapa_id);
                    if ($dato) {
                        $dato_almacenado=eval('$x=json_decode(\''.json_encode($dato->valor,JSON_HEX_APOS).'\'); return $x'.$accesor.';');
                        
                        if(!is_string($dato_almacenado)){
                            $valor_dato= json_encode($dato_almacenado);

                            if($evaluar == true){
                                //Grilla
                                $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
                                $result = Doctrine_Query::create()
                                                    ->select('c.tipo')
                                                    ->from('Campo c, c.Formulario f, f.Proceso p')
                                                    ->where('c.nombre=? AND p.activo=1 AND p.id=?',array($nombre_dato,$etapa->Tarea->Proceso->id))
                                                    ->execute();
                                if($result[0]->tipo == 'grid'){
                                    $valor_dato = json_decode($valor_dato);
                                    $tabla = '<table border="1" cellpadding="1" cellspacing="1"><thead>';
                                    foreach ($valor_dato as $key => $array) {
                                        if($key == 1){
                                            $tabla .= '</thead><tbody><tr>';
                                        }else{
                                            $tabla .= '<tr>';    
                                        }
                                        foreach($array as $llave => $value){
                                            if($key==0){
                                                $tabla .= '<td><strong>'.$value.'</strong></td>';
                                            }else{
                                                $tabla .= '<td>'.$value.'</td>';
                                            }
                                        }
                                        $tabla .= '</tr>';
                                    }
                                    $tabla .= '</tbody></table>';
                                    $valor_dato = $tabla;
                                //Fin grilla
                                //Instituciones y comunas    
                                }elseif ($result[0]->tipo == 'comunas' or $result[0]->tipo == 'instituciones_gob') {
                                    $valor_dato= json_decode($valor_dato,true);
                                    $i = 0;
                                    foreach ($valor_dato as $key => $value) {
                                        if($i==1){
                                            $valor_dato = $value;
                                        }
                                        $i++;
                                    }
                                }
                                //Fin instituciones y comunas
                            }
                        }
                        else{
                            $valor_dato=$dato_almacenado;
                        }
                    }
                    else {
                        //Entregamos vacio
                        $valor_dato = '';
                    }

                    return $valor_dato;
                }, $new_regla);
         
         //Variables globales
         $new_regla=preg_replace_callback('/@#(\w+)/', function($match) use ($etapa_id) {
                    $nombre_dato = $match[1];
                    
                    $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
                    $dato = Doctrine::getTable('DatoSeguimiento')->findGlobalByNombreAndProceso($nombre_dato,$etapa->Tramite->id);
                    $valor_dato=json_encode($dato);

                    return $valor_dato;
                }, $new_regla);
         
         $new_regla=preg_replace_callback('/@!(\w+)/', function($match) use ($etapa_id) {
                    $nombre_dato = $match[1];
                    
                    $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
                    $usuario=$etapa->Usuario;
                    
                    if($nombre_dato=='rut')
                        return $usuario->rut;
                    else if($nombre_dato=='nombre')         //Deprecated
                        return $usuario->nombres;
                    else if($nombre_dato=='apellidos')      //Deprecated
                        return $usuario->apellido_paterno.' '.$usuario->apellido_materno;
                    else if($nombre_dato=='nombres')
                        return $usuario->nombres;
                    else if($nombre_dato=='apellido_paterno')
                        return $usuario->apellido_paterno;
                    else if($nombre_dato=='apellido_materno')
                        return $usuario->apellido_materno;
                    else if($nombre_dato=='email')
                        return $usuario->email;
                    else if($nombre_dato=='tramite_id'){
                        return Doctrine::getTable('Etapa')->find($etapa_id)->tramite_id;
                    }
                    else if ($nombre_dato=='tramite_proc_cont'){
                    	return	Doctrine::getTable('Tramite')->find(Doctrine::getTable('Etapa')->find($etapa_id)->tramite_id)->tramite_proc_cont;
                    }
                }, $new_regla);
          
         return $new_regla;
    }

}