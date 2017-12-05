<?php
class Migration_32 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn('tarea', 'externa', 'boolean', null, array('notnull'=>1, 'default' => 0));
        
        $this->createTable('evento_externo', array(
                'id' => array( 'type' => 'int(10) unsigned AUTO_INCREMENT','primary' => 1),
                'nombre' => array( 'type' => 'varchar(128)', 'notnull' => true ),
                'metodo' => array( 'type' => 'enum', 'default' => null, 'values' => array('GET','POST','PUT')),
                'url' => array( 'type' => 'varchar(256)', 'notnull' => true ),
                'mensaje' => array( 'type' => 'text', 'notnull' => true ),
                'regla' => array( 'type' => 'text', 'notnull' => true ),
                'tarea_id' => array( 'type' => 'integer', 'length' => 4, 'unsigned' => 1, 'notnull' => 1 )
            )
        );
        $this->createForeignKey( 'evento_externo', 'eetarea_foreign_key', array(
                'local'        => 'tarea_id',
                'foreign'      => 'id',
                'foreignTable' => 'tarea',
                'onUpdate'     => 'CASCADE',
                'onDelete'     => 'CASCADE',
            )
        );
    }
    
    public function down(){
        $this->removeColumn('tarea','externa');
        $this->dropTable('evento_externo');
    }
}
?>