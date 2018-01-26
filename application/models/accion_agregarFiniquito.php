<?php
require_once('accion.php');
require_once('ChromePhp.php');

/*
Esta accion envia a la api simple los datos del finiquito y los asocia a un usuario
*/
class AccionGuardarFiniquito extends Accion {

    public function displayForm() {

        $display = '<label>Rut</label>';
        $display.='<input type="text" name="extra[rut]" value="' . (isset($this->extra->rut) ? $this->extra->rut : '') . '" />';
        $display.= '<label>Fecha Inicio</label>';
        $display.='<input type="text" name="extra[fecha_inicio]" value="' . (isset($this->extra->fecha_inicio) ? $this->extra->fecha_inicio: '') . '" />';
        $display.= '<label>Fecha Término</label>';
        $display.='<input type="text" name="extra[fecha_termino]" value="' . (isset($this->extra->fecha_termino) ? $this->extra->fecha_termino : '') . '" />';
        $display.='<label>Causal</label>';
        $display.='<input type="text" name="extra[causal]" value="' . (isset($this->extra->causal) ? $this->extra->causal : '') . '" />';
        $display.='<label>Archivo</label>';
	$display.='<input type="text" name="extra[archivo]" value="' . (isset($this->extra->archivo) ? $this->extra->archivo : '') . '" />';

        return $display;
    }

    public function validateForm() {
        /*$CI = & get_instance();
        $CI->form_validation->set_rules('extra[para]', 'Para', 'required');
        $CI->form_validation->set_rules('extra[tema]', 'Tema', 'required');
        $CI->form_validation->set_rules('extra[contenido]', 'Contenido', 'required');
   	*/
    }

    public function ejecutar(Etapa $etapa) {

        $regla=new Regla($this->extra->rut);
	$rut=$regla->getExpresionParaOutput($etapa->id);
        
        $regla=new Regla($this->extra->fecha_inicio);
        $fecha_inicio=$regla->getExpresionParaOutput($etapa->id);
        
	$regla=new Regla($this->extra->fecha_termino);
        $fecha_termino=$regla->getExpresionParaOutput($etapa->id);

	$regla=new Regla($this->extra->causal);
        $causal=$regla->getExpresionParaOutput($etapa->id);
	
	$regla=new Regla($this->extra->archivo);
        $filename=$regla->getExpresionParaOutput($etapa->id);
        $file=Doctrine_Query::create()
                    ->from('File f, f.Tramite t')
                    ->where('f.filename = ? AND t.id = ?',array($filename,$etapa->Tramite->id))
                    ->fetchOne();
		

    }

}
