<?php

require_once 'application/third_party/file-uploader.php';

class Uploader extends MY_Controller {

    function __construct() {
        parent::__construct();

    }

    function datos($campo_id,$etapa_id) {
        $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
        if(UsuarioSesion::usuario()->id!=$etapa->usuario_id){
            echo 'Usuario no tiene permisos para subir archivos en esta etapa';
            exit;
        }
        $campo=  Doctrine_Query::create()
                ->from('Campo c, c.Formulario.Pasos.Tarea.Etapas e')
                ->where('c.id = ? AND e.id = ?',array($campo_id,$etapa_id))
                ->fetchOne();
        if(!$campo){
            echo 'Campo no existe';
            exit;
        }
        
        
        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array('gif', 'jpg', 'png', 'pdf', 'doc', 'docx','zip','rar','ppt','pptx','xls','xlsx','mpp','vsd','odt','odp','ods','odg');
        if(isset($campo->extra->filetypes))
            $allowedExtensions=$campo->extra->filetypes;
            
        // max file size in bytes
        $sizeLimit = 20 * 1024 * 1024;

        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload('uploads/datos/');
        
          if(isset($result['success'])){
              $file=new File();
              $file->tramite_id=$etapa->Tramite->id;
              $archivo=$result['file_name'];
              $archivo = trim($archivo);
              $archivo = str_replace(array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),$archivo);
              $archivo = str_replace(array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),$archivo);
              $archivo = str_replace(array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),$archivo);
              $archivo = str_replace(array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $archivo);
              $archivo = str_replace(array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),$archivo);
              $archivo = str_replace(array('ñ', 'Ñ', 'ç', 'Ç'),array('n', 'N', 'c', 'C',), $archivo);
              $archivo = str_replace(array("\\","¨","º","-","~","#","@","|","!","\"","·","$","%","&","/","(", ")","?","'","¡","¿","[","^","`","]","+","}","{","¨","´",">","< ",";", ",",":"," "),'',$archivo);  
              //$file->filename=$result['file_name'];
              $file->filename= $archivo;
              $result['file_name']= $archivo;
              $file->tipo='dato';
              $file->llave=strtolower(random_string('alnum', 12));
              $file->save();
              
              $result['id']=$file->id;
              $result['llave']=$file->llave;
          }
        // to pass data through iframe you will need to encode all html tags
        //echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
          echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    function datos_get($filename) {
        $id=$this->input->get('id');
        $token=$this->input->get('token');
        
        //Chequeamos los permisos en el frontend
        $file=Doctrine_Query::create()
                ->from('File f, f.Tramite t, t.Etapas e, e.Usuario u')
                ->where('f.id = ? AND f.llave = ? AND u.id = ?',array($id,$token,UsuarioSesion::usuario()->id))
                ->fetchOne();
        
        if(!$file){
            //Chequeamos permisos en el backend
            $file=Doctrine_Query::create()
                ->from('File f, f.Tramite.Proceso.Cuenta.UsuariosBackend u')
                ->where('f.id = ? AND f.llave = ? AND u.id = ? AND (u.rol like "%super%" OR u.rol like "%operacion%" OR u.rol like "%seguimiento%")',array($id,$token,UsuarioBackendSesion::usuario()->id))
                ->fetchOne();
            
            if(!$file){
                echo 'Usuario no tiene permisos para ver este archivo.';
                exit;
            }
        }
        
        $path='uploads/datos/'.$file->filename;
        
        if(preg_match('/^\.\./', $file->filename)){
            echo 'Archivo invalido';
            exit;
        }

        if(!file_exists($path)){
            echo 'Archivo no existe';
            exit;
        }
  
        header('Content-Type: '. get_mime_by_extension($path));
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }

}
