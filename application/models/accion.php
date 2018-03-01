<?php

class Accion extends Doctrine_Record {

    function setTableDefinition() {        
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('tipo');
        $this->hasColumn('extra');
        $this->hasColumn('proceso_id');

        
        $this->setSubclasses(array(
                'AccionEnviarCorreo'  => array('tipo' => 'enviar_correo'),
                'AccionEnviarAdminDays'  => array('tipo' => 'enviar_admin_days'),
		'AccionExcelLicencia'  => array('tipo' => 'excel_licencia'),
		'AccionValidarExcelLicencia'  => array('tipo' => 'validar_excel_licencia'),
		'AccionEditarLicencia'  => array('tipo' => 'editar_licencia'),
		'AccionWebservice'  => array('tipo' => 'webservice'),
                'AccionVariable'  => array('tipo' => 'variable'),
		'AccionGuardarFiniquito'  => array('tipo' => 'guardar_finiquito'),
		'AccionGuardarLicencia'  => array('tipo' => 'guardar_licencia')
            )
        );
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Proceso', array(
            'local' => 'proceso_id',
            'foreign' => 'id'
        ));
        
        $this->hasMany('Evento as Eventos', array(
            'local' => 'id',
            'foreign' => 'accion_id'
        ));
    }
    
    public function displayForm(){
        return NULL;
    }
    
    public function validateForm(){
        return;
    }
    
    //Ejecuta la regla, de acuerdo a los datos del tramite tramite_id
    public function ejecutar($tramite_id){
        return;
    }
    
    public function setExtra($datos_array) {
        if ($datos_array) 
            $this->_set('extra' , json_encode($datos_array));
        else 
            $this->_set('extra' , NULL);
    }
    
    public function getExtra(){
        return json_decode($this->_get('extra'));
    }
    
    public function exportComplete()
    {        
        $accion = $this;                
        $object = $accion->toArray();

        return json_encode($object);
    }
    
    /**
     * @param $input
     * @return Accion
     */
    public static function importComplete($input)
    {
        $json = json_decode($input);
        $accion = new Accion();
        
        try {
            
            //Asignamos los valores a las propiedades de la Accion
            foreach ($json as $keyp => $p_attr) {
                if ($keyp != 'id' && $keyp != 'proceso_id')
                    $accion->{$keyp} = $p_attr;
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }                

        return $accion;
    }

}
