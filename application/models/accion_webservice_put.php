<?php
require_once('accion.php');

class AccionWebservicePut extends Accion {

    public function displayForm() {
        $display = '<p>Esta accion consultara via REST usando la siguiente URL. usanndo PUT</p>';
   
        $display.= '<label>URL</label>';
        $display.='<input type="text" class="input-xxlarge" name="extra[url]" value="' . ($this->extra ? $this->extra->url : '') . '" />';


        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();

        $CI->form_validation->set_rules('extra[url]', 'URL', 'required');
    }

    public function ejecutar(Etapa $etapa) {
        $r=new Regla($this->extra->url);
        $url=$r->getExpresionParaOutput($etapa->id);
        
	ChromePhp::log($url);	
        //Hacemos encoding a la url
        $url=preg_replace_callback('/([\?&][^=]+=)([^&]+)/', function($matches){
            $key=$matches[1];
            $value=$matches[2];
            return $key.urlencode($value);
        },
        $url);
	$url = urlapi.$url;       

	$json = null;

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


