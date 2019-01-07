<?php
require_once('accion.php');
require_once('ChromePhp.php');
require_once(FCPATH."procesos.php");
/*
Esta accion permite procesar un archivo excel junto a un archivo de texto para poder enviar correo electronico masivo,
reemplazando contenido del archivo de texto (plantilla) por valores indicados en el archivo Excel.
*/

class AccionCorreoMasivo extends Accion {

    public function displayForm() {	
	$display='<label>Archivo Excel</label>';
    $display.='<input type="text" name="extra[adjunto]" value="' . (isset($this->extra->adjunto) ? $this->extra->adjunto : '') . '"/>';
    $display.='<label>Archivo Texto</label>';
    $display.='<input type="text" name="extra[adjuntoTxt]" value="' . (isset($this->extra->adjuntoTxt) ? $this->extra->adjuntoTxt : '') . '"/>';
    $display.='<label>Asunto</label>';
    $display.='<input type="text" name="extra[subject]" value="' . (isset($this->extra->subject) ? $this->extra->subject : '') . '"/>';

	return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('extra[adjunto]', 'adjunto', 'required');
        $CI->form_validation->set_rules('extra[adjuntoTxt]', 'adjuntoTxt', 'required');
        $CI->form_validation->set_rules('extra[subject]', 'subject', 'required');
    }

    public function ejecutar(Etapa $etapa){		

        $regla=new Regla($this->extra->subject);
        $subject=$regla->getExpresionParaOutput($etapa->id);

        //Get Files
        $regla=new Regla($this->extra->adjunto);
        $filenameExcel=$regla->getExpresionParaOutput($etapa->id);
        $fileExcel=Doctrine_Query::create()
                    ->from('File f, f.Tramite t')
                    ->where('f.filename = ? AND t.id = ?',array($filenameExcel,$etapa->Tramite->id))
                    ->fetchOne();

        $regla=new Regla($this->extra->adjuntoTxt);
        $filenameTxt=$regla->getExpresionParaOutput($etapa->id);
        $fileTxt=Doctrine_Query::create()
                    ->from('File f, f.Tramite t')
                    ->where('f.filename = ? AND t.id = ?',array($filenameTxt,$etapa->Tramite->id))
                    ->fetchOne();

        //Read txt
        $msgOrig='';
        if ($fileTxt) {
           $msgOrig = file_get_contents('uploads/datos/'.$filenameTxt);
        }
        $message = $msgOrig;   
        

        if($fileExcel){ 
    		$CI =& get_instance();
       		$CI->load->library('Excel');
    		$inputFileNameExcel = 'uploads/datos/'.$filenameExcel;		
    	
    		//Load file
    		$objPHPExcel=null; 
         
    		try {
    			log_message('info',"Load excel: ".$filenameExcel);
    			$inputFileTypeExcel = PHPExcel_IOFactory::identify($inputFileNameExcel);
    			$objReader = PHPExcel_IOFactory::createReader($inputFileTypeExcel);
    			$objPHPExcel = $objReader->load($inputFileNameExcel);
    		} catch(Exception $e) {
        			log_message('error',"Load excel: ".$filenameExcel." failed");
    			die('Error loading file "'.pathinfo($inputFileNameExcel,PATHINFO_BASENAME).'": '.$e->getMessage());
    		}	

    		//Get worksheet dimensions
    		$sheet = $objPHPExcel->getSheet(0); 
    		$highestRow = $sheet->getHighestRow(); 
    		$highestColumn = $sheet->getHighestColumn();
    		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
          
            //Secuencia para correo
            $CI = & get_instance();
            $cuenta=$etapa->Tramite->Proceso->Cuenta;
            $CI->email->from('gerencia.personas@ist.cl', $cuenta->nombre_largo);
         
            $CI->email->subject($subject);
          
    		//Read values
    		log_message('info',"Read values");
            
            //Read first row. The first row must contain the name of the parameters to be replaced in the body
            $columns = $sheet->rangeToArray(
                'A1:'.$highestColumn.'1',     // The worksheet range that we want to retrieve
                NULL,        // Value that should be returned for empty cells
                TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                TRUE         // Should the array be indexed by cell row and cell column
            );  
            

            $countMailSend = 0;
    		for ($row = 2; $row <= $highestRow; ++ $row){			
    			
                //Replace content
                $index=0;
                $cellPersonalMail="";
                foreach ($columns[1] as $a) {
                    $message = str_replace($a, $sheet->getCellByColumnAndRow($index,$row), $message);
                    if ($index == 0) {
                        //First column should contain mail 
                        $cellPersonalMail = $sheet->getCellByColumnAndRow($index,$row);
                    }
                    $index++; 
                }
                
                //Add Recipient and body 
                $CI->email->to($cellPersonalMail);
                $CI->email->message($message);
                $CI->email->send();

                $message = $msgOrig;
                $countMailSend++;
            }

            // set @@cantidad_correos response
            $dato = Doctrine::getTable("DatoSeguimiento")->findOneByNombreAndEtapaId("cantidad_correos", $etapa->id);
            if($dato){
                $dato->valor = $countMailSend;     
                $dato->save();
            }

        }    
    }
}
