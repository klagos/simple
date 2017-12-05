<?php
class Migration_33 extends Doctrine_Migration_Base {
    public function up(){
        
        $this->createTable('acontecimiento', array(
                'id' => array( 'type' => 'int(10) unsigned AUTO_INCREMENT','primary' => 1),
                'estado' => array( 'type' => 'boolean', 'notnull' => true ),
                'evento_externo_id' => array( 'type' => 'integer', 'length' => 4, 'unsigned' => 1, 'notnull' => 1 ),
                'etapa_id' => array( 'type' => 'integer', 'length' => 4, 'unsigned' => 1, 'notnull' => 1 )
            )
        );
        $this->createForeignKey( 'acontecimiento', 'ac_evento_externo_foreign_key', array(
                'local'        => 'evento_externo_id',
                'foreign'      => 'id',
                'foreignTable' => 'evento_externo',
                'onUpdate'     => 'CASCADE',
                'onDelete'     => 'CASCADE',
            )
        );
        $this->createForeignKey( 'acontecimiento', 'ac_etapa_foreign_key', array(
                'local'        => 'etapa_id',
                'foreign'      => 'id',
                'foreignTable' => 'etapa',
                'onUpdate'     => 'CASCADE',
                'onDelete'     => 'CASCADE',
            )
        );
    }
    
    public function down(){
        $this->dropTable('acontecimiento');
    }
}
?>