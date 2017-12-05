<?php
class Migration_35 extends Doctrine_Migration_Base {
    public function up(){
        
        $this->addColumn('evento', 'evento_externo_id', 'int(10) unsigned', null, array('notnull'=>0));

        $this->createForeignKey( 'evento', 'fke_evento_externo_foreign_key', array(
                'local'        => 'evento_externo_id',
                'foreign'      => 'id',
                'foreignTable' => 'evento_externo',
                'onUpdate'     => 'CASCADE',
                'onDelete'     => 'CASCADE',
            )
        );
    }
    
    public function down(){
        $this->removeColumn('evento','evento_externo_id');
    }
}
?>