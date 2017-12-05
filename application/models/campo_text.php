<?php
require_once('campo.php');
class CampoText extends Campo{
    
    public $requiere_datos=false;

    protected function display($modo, $dato,$etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }else{
            $valor_default=$this->valor_default;
        }
        
      
        $display='<label class="control-label" for="'.$this->id.'">' . $this->etiqueta . (!in_array('required', $this->validacion) ? ' (Opcional)' : '') . '</label>';
        $display.='<div class="controls">';
        $display.='<input id="'.$this->id.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="text" class="input-semi-large" name="' . $this->nombre . '" value="' . ($dato?htmlspecialchars($dato->valor):htmlspecialchars($valor_default)) . '" data-modo="'.$modo.'" />';
        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';
        
        return $display;
    }
    
}